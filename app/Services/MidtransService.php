<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    private string $serverKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY', ''));
        $isProduction = config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false));
        $this->baseUrl = $isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    /**
     * Create Snap transaction.
     */
    public function createSnapTransaction(Transaction $transaction): ?string
    {
        if (empty($this->serverKey)) {
            Log::warning('Midtrans server key not configured');
            return null;
        }

        $user = $transaction->user;

        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->post($this->baseUrl . '/charge', [
                    'payment_type' => 'bank_transfer',
                    'transaction_details' => [
                        'order_id' => $transaction->order_id,
                        'gross_amount' => (int) $transaction->amount,
                    ],
                    'customer_details' => [
                        'first_name' => $user->name,
                        'email' => $user->email,
                    ],
                    'bank_transfer' => [
                        'bank' => 'bca',
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $transaction->update([
                    'payment_url' => $data['redirect_url'] ?? null,
                    'transaction_id' => $data['transaction_id'] ?? null,
                ]);
                return $data['redirect_url'] ?? null;
            }

            Log::error('Midtrans charge error', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans exception', [
                'message' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Check transaction status.
     */
    public function checkStatus(string $orderId): ?array
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->get($this->baseUrl . "/{$orderId}/status");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Midtrans status check exception', [
                'message' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Handle payment notification.
     */
    public function handleNotification(array $notification): void
    {
        $orderId = $notification['order_id'] ?? null;
        $transactionStatus = $notification['transaction_status'] ?? null;

        if (!$orderId || !$transactionStatus) {
            return;
        }

        $transaction = Transaction::where('order_id', $orderId)->first();
        if (!$transaction) {
            return;
        }

        $newStatus = match ($transactionStatus) {
            'settlement', 'capture' => 'completed',
            'pending' => 'pending',
            'deny', 'cancel', 'expire' => 'failed',
            default => $transaction->status,
        };

        if ($newStatus === 'completed' && $transaction->type === 'subscription') {
            $user = $transaction->user;
            $user->update(['plan' => 'premium']);
        }

        $transaction->update(['status' => $newStatus]);
    }
}