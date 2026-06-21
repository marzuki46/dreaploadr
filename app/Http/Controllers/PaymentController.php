<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(
        protected MidtransService $midtrans
    ) {}

    /**
     * Show pricing/subscription page.
     */
    public function pricing()
    {
        return view('pricing');
    }

    /**
     * Subscribe to premium.
     */
    public function subscribe(Request $request)
    {
        $user = Auth::user();
        $plan = $request->input('plan', 'monthly');

        $amount = match ($plan) {
            'yearly' => 350000,
            default => 50000,
        };

        $orderId = 'SUB-' . $user->id . '-' . time();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'order_id' => $orderId,
            'type' => 'subscription',
            'amount' => $amount,
            'status' => 'pending',
            'description' => "Subscription {$plan}",
        ]);

        $paymentUrl = $this->midtrans->createSnapTransaction($transaction);

        if ($paymentUrl) {
            return redirect($paymentUrl);
        }

        // If Midtrans not configured, simulate success
        $transaction->update(['status' => 'completed']);
        $user->update(['plan' => 'premium']);

        return redirect('/dashboard')->with('success', 'Welcome to Premium!');
    }

    /**
     * Midtrans payment notification handler.
     */
    public function notification(Request $request)
    {
        $this->midtrans->handleNotification($request->all());
        return response('OK', 200);
    }

    /**
     * Payment success callback.
     */
    public function success(Request $request)
    {
        $orderId = $request->input('order_id');
        if ($orderId) {
            $transaction = Transaction::where('order_id', $orderId)->first();
            if ($transaction && $transaction->status === 'pending') {
                $transaction->update(['status' => 'completed']);
                $transaction->user?->update(['plan' => 'premium']);
            }
        }

        return redirect('/dashboard')->with('success', 'Payment successful! Welcome to Premium.');
    }

    /**
     * Payment failure callback.
     */
    public function failure(Request $request)
    {
        return redirect('/pricing')->with('error', 'Payment was cancelled or failed.');
    }
}