<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Redirect to Google for authentication.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                $user->update([
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                ]);
            } else {
                if (Auth::check()) {
                    $user = Auth::user();
                    $user->update([
                        'google_id' => $googleUser->id,
                        'google_token' => $googleUser->token,
                        'google_refresh_token' => $googleUser->refreshToken,
                    ]);
                } else {
                    $existingUser = User::where('email', $googleUser->email)->first();
                    if ($existingUser) {
                        $existingUser->update([
                            'google_id' => $googleUser->id,
                            'google_token' => $googleUser->token,
                            'google_refresh_token' => $googleUser->refreshToken,
                        ]);
                        Auth::login($existingUser);
                        return redirect()->intended('/dashboard');
                    }

                    $user = User::create([
                        'name' => $googleUser->name ?? $googleUser->nickname ?? 'Google User',
                        'email' => $googleUser->email,
                        'password' => bcrypt(Str::random(16)),
                        'google_id' => $googleUser->id,
                        'google_token' => $googleUser->token,
                        'google_refresh_token' => $googleUser->refreshToken,
                        'role' => 'user',
                    ]);
                }
            }

            Auth::login($user);
            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['google' => 'Google authentication failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Disconnect Google account.
     */
    public function disconnect()
    {
        $user = Auth::user();
        $user->update([
            'google_id' => null,
            'google_token' => null,
            'google_refresh_token' => null,
        ]);

        return back()->with('success', 'Google account disconnected.');
    }
}