<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class FacebookController extends Controller
{
    /**
     * Redirect to Facebook for authentication.
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')
            ->redirectUrl(url('/auth/facebook/callback'))
            ->scopes(['pages_show_list', 'pages_read_engagement', 'pages_manage_posts', 'instagram_basic', 'instagram_content_publish'])
            ->redirect();
    }

    /**
     * Handle Facebook callback.
     */
    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')
                ->redirectUrl(url('/auth/facebook/callback'))
                ->user();

            $user = User::where('facebook_id', $facebookUser->id)->first();

            if ($user) {
                // Update token
                $user->update([
                    'facebook_access_token' => $facebookUser->token,
                ]);
            } else {
                // Check if user already logged in
                if (Auth::check()) {
                    $user = Auth::user();
                    $user->update([
                        'facebook_id' => $facebookUser->id,
                        'facebook_access_token' => $facebookUser->token,
                    ]);
                } else {
                    // Check if email exists
                    $existingUser = User::where('email', $facebookUser->email)->first();
                    if ($existingUser && $facebookUser->email) {
                        $existingUser->update([
                            'facebook_id' => $facebookUser->id,
                            'facebook_access_token' => $facebookUser->token,
                        ]);
                        Auth::login($existingUser);
                        $user = $existingUser;
                    } else {
                        // Create new user
                        $user = User::create([
                            'name' => $facebookUser->name ?? $facebookUser->nickname ?? 'Facebook User',
                            'email' => $facebookUser->email ?? ($facebookUser->id . '@facebook.com'),
                            'password' => bcrypt(Str::random(16)),
                            'facebook_id' => $facebookUser->id,
                            'facebook_access_token' => $facebookUser->token,
                            'role' => 'user',
                        ]);
                    }
                }
            }

            // Sync with FacebookAccount model for scraping/posting
            $user->facebookAccounts()->updateOrCreate(
                ['facebook_user_id' => $facebookUser->id],
                [
                    'name' => $facebookUser->name ?? $facebookUser->nickname ?? 'Facebook Account',
                    'email' => $facebookUser->email,
                    'access_token' => $facebookUser->token,
                    'refresh_token' => $facebookUser->refreshToken,
                    'is_active' => true,
                ]
            );

            Auth::login($user);
            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            dd('ERROR FACEBOOK DATABASE: ' . $e->getMessage() . ' di baris ' . $e->getLine());
        }
    }

    /**
     * Disconnect Facebook account.
     */
    public function disconnect()
    {
        $user = Auth::user();
        $user->update([
            'facebook_id' => null,
            'facebook_token' => null,
            'facebook_refresh_token' => null,
        ]);

        return back()->with('success', 'Facebook account disconnected.');
    }
}