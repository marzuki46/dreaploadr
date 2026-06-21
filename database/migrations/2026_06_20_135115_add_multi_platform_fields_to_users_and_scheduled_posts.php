<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Users table: platform permissions
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'can_post_youtube')) {
                $table->boolean('can_post_youtube')->default(false)->after('is_onboarding_completed');
            }
            if (!Schema::hasColumn('users', 'can_post_tiktok')) {
                $table->boolean('can_post_tiktok')->default(false)->after('can_post_youtube');
            }
            if (!Schema::hasColumn('users', 'youtube_access_token')) {
                $table->text('youtube_access_token')->nullable()->after('can_post_tiktok');
            }
            if (!Schema::hasColumn('users', 'youtube_refresh_token')) {
                $table->text('youtube_refresh_token')->nullable()->after('youtube_access_token');
            }
            if (!Schema::hasColumn('users', 'tiktok_access_token')) {
                $table->text('tiktok_access_token')->nullable()->after('youtube_refresh_token');
            }
        });

        // Scheduled posts: platform support
        Schema::table('scheduled_posts', function (Blueprint $table) {
            if (!Schema::hasColumn('scheduled_posts', 'platform')) {
                $table->string('platform', 20)->default('facebook')->after('user_id');
            }
            if (!Schema::hasColumn('scheduled_posts', 'platform_post_id')) {
                $table->string('platform_post_id', 255)->nullable()->after('facebook_post_id');
            }
            if (!Schema::hasColumn('scheduled_posts', 'platform_page_id')) {
                $table->string('platform_page_id', 255)->nullable()->after('platform_post_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'can_post_youtube',
                'can_post_tiktok',
                'youtube_access_token',
                'youtube_refresh_token',
                'tiktok_access_token',
            ]);
        });

        Schema::table('scheduled_posts', function (Blueprint $table) {
            $table->dropColumn([
                'platform',
                'platform_post_id',
                'platform_page_id',
            ]);
        });
    }
};