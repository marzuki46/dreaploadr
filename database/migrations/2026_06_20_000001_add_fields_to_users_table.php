<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('password');
            $table->string('subscription_plan')->nullable()->after('role');
            $table->string('bank_account_number')->nullable()->after('subscription_plan');
            $table->string('google_id')->nullable()->unique()->after('bank_account_number');
            $table->string('facebook_id')->nullable()->unique()->after('google_id');
            $table->string('avatar')->nullable()->after('facebook_id');
            $table->text('facebook_access_token')->nullable()->after('avatar');
            $table->timestamp('subscription_ends_at')->nullable()->after('facebook_access_token');
            $table->boolean('is_onboarding_completed')->default(false)->after('subscription_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'subscription_plan',
                'bank_account_number',
                'google_id',
                'facebook_id',
                'avatar',
                'facebook_access_token',
                'subscription_ends_at',
                'is_onboarding_completed',
            ]);
        });
    }
};