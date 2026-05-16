<?php

namespace App\Http\Controllers\Admin\Ad;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdPayment;
use App\Services\AdPaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AdminAdController extends Controller
{
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


            // return response()->json([
            //     'success' => true,
            //     'message' => 'Ad approved and payment order created',
            //     'order' => $order,
            //     'payment' => $payment,
            //     'ad' => $ad,
            // ]);
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
        // return response()->json(['success' => true, 'message' => 'Ad rejected']);
        return redirect()
            ->route('admin.ads.pending')
            ->with('success', 'Ad rejected.');
    }
}
