@extends('layouts.app')

@section('title', 'Withdrawal')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Request Withdrawal</h1>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-semibold mb-2">Available Balance</h2>
            <p class="text-4xl font-bold text-indigo-600">Rp {{ number_format($balance, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 mt-1">Minimum withdrawal: Rp 50.000</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="/affiliates/withdraw">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Amount</label>
                        <input type="number" name="amount" required min="50000" max="{{ $balance }}" placeholder="Enter amount" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Bank Account</label>
                        <input type="text" name="bank_account" required placeholder="Bank Name - Account Number - Account Name" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    </div>
                    <button type="submit" class="w-full gradient-bg text-white py-3.5 rounded-xl font-bold hover:opacity-90 transition shadow-lg shadow-indigo-200">
                        Submit Withdrawal Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection