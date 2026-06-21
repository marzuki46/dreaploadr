@extends('layouts.app')

@section('title', 'Affiliate Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Affiliate Dashboard</h1>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-sm text-gray-600 font-medium mb-1">Total Earnings</p>
            <p class="text-3xl font-bold text-gray-900">Rp {{ number_format($earnings['total'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-sm text-gray-600 font-medium mb-1">Pending Balance</p>
            <p class="text-3xl font-bold text-amber-600">Rp {{ number_format($earnings['pending'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-sm text-gray-600 font-medium mb-1">Paid Out</p>
            <p class="text-3xl font-bold text-green-600">Rp {{ number_format($earnings['paid'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Referral Link --}}
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 mb-8 text-white">
        <h2 class="text-xl font-semibold mb-2">Your Referral Link</h2>
        <p class="text-indigo-100 mb-3">Share this link to earn commissions from referrals!</p>
        <div class="flex gap-2">
            <input type="text" value="{{ $referralUrl }}" readonly class="flex-1 px-4 py-3 rounded-xl text-gray-900 font-medium" id="referralLink">
            <button onclick="copyReferral()" class="px-6 py-3 bg-white text-indigo-600 font-bold rounded-xl hover:bg-indigo-50 transition">Copy</button>
        </div>
    </div>

    {{-- Recent Clicks --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold mb-4">Recent Referrals</h2>
            @if($affiliates->count() > 0)
                <div class="space-y-3">
                    @foreach($affiliates as $affiliate)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div>
                                <p class="font-medium text-sm">{{ $affiliate->referredUser?->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $affiliate->created_at->format('M d, Y') }}</p>
                            </div>
                            <span class="font-semibold text-green-600">+Rp {{ number_format($affiliate->commission_earned, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No referrals yet.</p>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold mb-4">Recent Clicks</h2>
            @if($clicks->count() > 0)
                <div class="space-y-3">
                    @foreach($clicks as $click)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div>
                                <p class="font-medium text-sm">{{ $click->ip_address ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $click->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $click->user_agent ? Str::limit($click->user_agent, 30) : 'No UA' }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No clicks yet. Share your referral link!</p>
            @endif
        </div>
    </div>

    <div class="mt-8 text-center">
        <a href="/affiliates/withdraw" class="inline-block px-8 py-4 gradient-bg text-white font-bold rounded-xl hover:opacity-90 transition shadow-lg shadow-indigo-200">
            Request Withdrawal
        </a>
    </div>
</div>

@push('scripts')
<script>
function copyReferral() {
    const input = document.getElementById('referralLink');
    input.select();
    document.execCommand('copy');
    alert('Referral link copied!');
}
</script>
@endpush
@endsection