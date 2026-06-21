<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('tiktok_refresh_token')->nullable()->after('tiktok_access_token');
            $table->timestamp('youtube_token_expires_at')->nullable()->after('youtube_refresh_token');
            $table->timestamp('tiktok_token_expires_at')->nullable()->after('tiktok_refresh_token');
            $table->timestamp('facebook_token_expires_at')->nullable()->after('facebook_access_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'tiktok_refresh_token',
                'youtube_token_expires_at',
                'tiktok_token_expires_at',
                'facebook_token_expires_at'
            ]);
        });
    }
};
