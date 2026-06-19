<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ecommerce\UserOrder;
use App\Models\Ecommerce\UserPayment;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\GatewayManager;
use App\Services\Payment\Exceptions\PaymentGatewayException;
use App\Services\Setting\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PhonePeWebhookController extends Controller
{
    protected ?PaymentGatewayInterface $gateway = null;

    public function __construct(
        protected GatewayManager $gatewayManager,
        protected SettingService $settings,
    ) {}

    protected function gateway(): PaymentGatewayInterface
    {
        return $this->gateway ??= $this->gatewayManager->driver('phonepe');
    }

    public function handle(Request $request): JsonResponse
    {
        try {
            $signature = $request->header('X-VERIFY');
            $base64Response = $request->input('response');

            if (!$signature || !$base64Response) {
                Log::warning('PhonePe webhook: missing signature or response data', [
                    'method' => $request->method(),
                    'has_signature' => !empty($signature),
                    'has_response' => !empty($base64Response),
                    'headers' => $request->headers->all(),
                    'payload' => $request->all(),
                ]);
                return response()->json(['status' => 'error', 'message' => 'Missing signature or data'], 400);
            }

            $gatewayConfig = $this->settings->getGatewayConfig('phonepe');
            $webhookSecret = $gatewayConfig['secret'] ?? '';

            if (empty($webhookSecret)) {
                Log::warning('PhonePe webhook: Salt Key not configured');
                return response()->json(['status' => 'error', 'message' => 'Webhook not configured'], 500);
            }

            // Verify using the gateway service method
            if (!$this->gateway()->verifyWebhookSignature($base64Response, $signature, $webhookSecret)) {
                Log::warning('PhonePe webhook: invalid signature');
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
            }

            $decodedPayload = json_decode(base64_decode($base64Response), true);
            Log::info('PhonePe webhook payload decoded successfully', ['payload' => $decodedPayload]);

            $event = $this->gateway()->parseWebhookEvent($decodedPayload);
            $merchantTransactionId = $decodedPayload['data']['merchantTransactionId'] ?? null;

            if (!$merchantTransactionId) {
                Log::warning('PhonePe webhook: no merchantTransactionId found');
                return response()->json(['status' => 'error', 'message' => 'Missing transaction ID'], 400);
            }

            $dedupKey = "webhook_dedup_phonepe_{$merchantTransactionId}";
            if (\Illuminate\Support\Facades\Cache::get($dedupKey)) {
                Log::info('PhonePe webhook: duplicate event skipped', ['merchantTransactionId' => $merchantTransactionId]);
                return response()->json(['status' => 'duplicate']);
            }

            // Find matching order by UUID
            $order = UserOrder::where('uuid', $merchantTransactionId)->first();

            if (!$order) {
                Log::warning('PhonePe webhook: order not found', ['merchantTransactionId' => $merchantTransactionId]);
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            if ($event === 'payment.success') {
                $transactionId = $decodedPayload['data']['transactionId'] ?? $merchantTransactionId;
                $checksum = $signature;

                // Dispatch order payment processing job
                \App\Jobs\ProcessOrderPayment::dispatch(
                    $order->id,
                    $transactionId,
                    '', // no razorpay order id
                    $checksum
                );

                \Illuminate\Support\Facades\Cache::put($dedupKey, true, 3600);

                Log::info('PhonePe webhook: payment.success processed', ['order_id' => $order->id]);
            } else {
                Log::warning('PhonePe webhook: payment failed or unhandled code', [
                    'code' => $decodedPayload['code'] ?? 'unknown',
                    'order_id' => $order->id,
                ]);

                // Update payment record to failed
                $payment = UserPayment::where('order_id', $order->id)
                    ->where('status', 'initiated')
                    ->first();

                if ($payment) {
                    $payment->update([
                        'status' => 'failed',
                        'meta' => array_merge(is_array($payment->meta) ? $payment->meta : [], [
                            'webhook_failure_response' => $decodedPayload,
                        ]),
                    ]);
                }
            }

            return response()->json(['status' => 'success']);
        } catch (PaymentGatewayException $e) {
            Log::error('PhonePe webhook: gateway error', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 502);
        } catch (\Throwable $e) {
            Log::error('PhonePe webhook: processing failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Processing failed'], 500);
        }
    }
}
