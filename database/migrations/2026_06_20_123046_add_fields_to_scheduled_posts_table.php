<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scheduled_posts', function (Blueprint $table) {
            $table->foreignId('video_id')->after('ai_content_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('facebook_account_id')->after('video_id')->nullable()->constrained()->cascadeOnDelete();
            $table->text('description')->after('facebook_account_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('scheduled_posts', function (Blueprint $table) {
            $table->dropForeign(['video_id']);
            $table->dropForeign(['facebook_account_id']);
            $table->dropColumn(['video_id', 'facebook_account_id', 'description']);
        });
    }
};