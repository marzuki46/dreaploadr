<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                config([
                    'services.facebook.client_id' => \App\Models\Setting::getVal('facebook_client_id', config('services.facebook.client_id')),
                    'services.facebook.client_secret' => \App\Models\Setting::getVal('facebook_client_secret', config('services.facebook.client_secret')),
                    'services.facebook.redirect' => \App\Models\Setting::getVal('facebook_redirect_uri', config('services.facebook.redirect')),
                    
                    'services.google.client_id' => \App\Models\Setting::getVal('youtube_client_id', config('services.google.client_id')),
                    'services.google.client_secret' => \App\Models\Setting::getVal('youtube_client_secret', config('services.google.client_secret')),
                    'services.google.redirect' => \App\Models\Setting::getVal('youtube_redirect_uri', config('services.google.redirect')),
                ]);
            }
        } catch (\Exception $e) {
            // Do nothing if database is not ready
        }
    }
}
