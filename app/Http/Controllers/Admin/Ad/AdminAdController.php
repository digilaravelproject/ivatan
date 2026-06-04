<?php

namespace App\Http\Controllers\Admin\Ad;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdPayment;
use App\Models\User;
use App\Services\AdPaymentService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class AdminAdController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $ads = Ad::with('package', 'user')->latest()->paginate(20);
        return view('admin.ads.index', compact('ads'));
        // return response()->json(['ads' => $ads]);
    }
    // List pending ads
    public function pending()
    {
        $ads = Ad::where('status', 'pending_admin_approval')->with('package', 'user')->latest()->paginate(20);
        return view('admin.ads.pending', compact('ads'));
    }


    // Approve ad: creates pending payment and a Razorpay order, then returns order details
    public function approve(Request $request, Ad $ad, AdPaymentService $paymentService)
    {
        if ($ad->status !== 'pending_admin_approval') {
            // return response()->json(['message' => 'Ad not pending approval'], 422);
            return redirect()->back()->with('error', 'Ad not pending approval.');
        }


        DB::beginTransaction();
        try {
            // mark as awaiting payment
            $ad->status = 'awaiting_payment';
            $ad->save();


            // create payment record
            $amount = $ad->package ? $ad->package->price : 0;


            $payment = AdPayment::create([
                'ad_id' => $ad->id,
                'user_id' => $ad->user_id,
                'amount' => $amount,
                'currency' => $ad->package?->currency ?? 'INR',
                'status' => 'pending',
            ]);


            // create razorpay order (amount in paise)
            $order = $paymentService->createOrder($payment, (int)round($amount * 100));


            // save razorpay order id
            $payment->update(['razorpay_order_id' => $order['id']]);


            DB::commit();

            try {
                $creator = User::find($ad->user_id);
                if ($creator) {
                    $this->notificationService->sendToUser($creator, 'content_approved', [
                        'title'       => 'Ad Approved',
                        'message'     => 'Your advertisement has been approved. Please complete the payment to activate it.',
                        'ad_id'       => $ad->id,
                        'action_url'  => null,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Ad approval notification failed', ['error' => $e->getMessage()]);
            }

            return redirect()
                ->route('admin.ads.pending')
                ->with('success', 'Ad approved and payment order created.');
        } catch (\Exception $e) {
            DB::rollBack();
            // return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function reject(Request $request, Ad $ad)
    {
        $ad->status = 'rejected';
        $ad->save();

        try {
            $creator = User::find($ad->user_id);
            if ($creator) {
                $this->notificationService->sendToUser($creator, 'content_rejected', [
                    'title'       => 'Ad Rejected',
                    'message'     => 'Your advertisement has been rejected by the admin.',
                    'ad_id'       => $ad->id,
                    'action_url'  => null,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Ad rejection notification failed', ['error' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.ads.pending')
            ->with('success', 'Ad rejected.');
    }
}
