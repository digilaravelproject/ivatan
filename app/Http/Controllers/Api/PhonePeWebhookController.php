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
            // Detect Checkout V2 Webhook
            if ($request->has('event') && $request->has('payload') && $request->header('Authorization')) {
                return $this->handlePhonePeV2($request);
            }

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

    protected function handlePhonePeV2(Request $request): JsonResponse
    {
        try {
            $signature = $request->header('Authorization');
            $rawBody = $request->getContent();

            $gatewayConfig = $this->settings->getGatewayConfig('phonepe');
            $webhookSecret = $gatewayConfig['secret'] ?? '';

            if (empty($webhookSecret)) {
                Log::warning('PhonePe V2 webhook: Salt Key not configured');
                return response()->json(['status' => 'error', 'message' => 'Webhook not configured'], 500);
            }

            // Verify signature using V2 helper
            if (!$this->gateway()->verifyV2WebhookSignature($rawBody, $signature, $webhookSecret)) {
                Log::warning('PhonePe V2 webhook: invalid signature');
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
            }

            $decodedPayload = $request->all();
            Log::info('PhonePe V2 webhook payload verified', ['payload' => $decodedPayload]);

            $event = $decodedPayload['event'] ?? '';
            $merchantOrderId = $decodedPayload['payload']['merchantOrderId'] ?? null;

            if (!$merchantOrderId) {
                Log::warning('PhonePe V2 webhook: no merchantOrderId');
                return response()->json(['status' => 'error', 'message' => 'Missing transaction ID'], 400);
            }

            $dedupKey = "webhook_dedup_phonepe_{$merchantOrderId}";
            if (\Illuminate\Support\Facades\Cache::get($dedupKey)) {
                Log::info('PhonePe V2 webhook: duplicate event skipped', ['merchantOrderId' => $merchantOrderId]);
                return response()->json(['status' => 'duplicate']);
            }

            $order = UserOrder::where('uuid', $merchantOrderId)->first();

            if ($event === 'checkout.order.completed' && ($decodedPayload['payload']['state'] ?? '') === 'COMPLETED') {
                if (!$order) {
                    Log::warning('PhonePe V2 webhook: order not found', ['merchantOrderId' => $merchantOrderId]);
                    return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
                }

                $phonepeTxnId = $decodedPayload['payload']['paymentDetails'][0]['transactionId'] ?? $merchantOrderId;

                \App\Jobs\ProcessOrderPayment::dispatch(
                    $order->id,
                    $phonepeTxnId,
                    '', // no razorpay order id
                    $signature,
                    'phonepe',
                );

                \Illuminate\Support\Facades\Cache::put($dedupKey, true, 3600);

                Log::info('PhonePe V2 webhook: checkout.order.completed processed', ['order_id' => $order->id]);
            } else {
                Log::warning('PhonePe V2 webhook: payment failed or unhandled', [
                    'event' => $event,
                    'state' => $decodedPayload['payload']['state'] ?? 'unknown',
                    'order_id' => $order?->id,
                ]);

                if ($order) {
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
            }

            return response()->json(['status' => 'success']);
        } catch (\Throwable $e) {
            Log::error('PhonePe V2 webhook: processing failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Processing failed'], 500);
        }
    }
}
