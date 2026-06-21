<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliateClick;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliateController extends Controller
{
    /**
     * Show affiliate dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        $earnings = [
            'total' => Affiliate::where('user_id', $user->id)->sum('total_commission'),
            'pending' => Affiliate::where('user_id', $user->id)->where('payout_status', 'pending')->sum('total_commission'),
            'paid' => Affiliate::where('user_id', $user->id)->where('payout_status', 'paid')->sum('total_commission'),
        ];

        $affiliates = Affiliate::where('user_id', $user->id)
            ->with('referredUser')
            ->latest()
            ->paginate(15);

        $affiliateIds = Affiliate::where('user_id', $user->id)->pluck('id');
        
        $clicks = AffiliateClick::whereIn('affiliate_id', $affiliateIds)
            ->latest('clicked_at')
            ->take(50)
            ->get();

        $referralUrl = url('/register?ref=' . $user->id);

        return view('affiliates.index', compact('earnings', 'affiliates', 'clicks', 'referralUrl'));
    }

    /**
     * Show withdrawal form.
     */
    public function withdrawalForm()
    {
        $user = Auth::user();
        $balance = Affiliate::where('user_id', $user->id)
            ->where('payout_status', 'pending')
            ->sum('total_commission');

        return view('affiliates.withdraw', compact('balance'));
    }

    /**
     * Request withdrawal.
     */
    public function requestWithdrawal(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:50000'],
            'bank_account' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $balance = Affiliate::where('user_id', $user->id)
            ->where('payout_status', 'pending')
            ->sum('total_commission');

        if ($data['amount'] > $balance) {
            return back()->withErrors(['amount' => 'Insufficient balance.']);
        }

        Transaction::create([
            'user_id' => $user->id,
            'type' => 'withdrawal',
            'amount' => $data['amount'],
            'status' => 'pending',
            'description' => 'Withdrawal to ' . $data['bank_account'],
        ]);

        return back()->with('success', 'Withdrawal request submitted successfully.');
    }
}