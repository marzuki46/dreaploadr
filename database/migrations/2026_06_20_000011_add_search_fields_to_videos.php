<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('author_name')->nullable()->after('title');
            $table->string('keywords')->nullable()->after('description');
            $table->string('source_type')->default('page')->after('keywords');
            $table->string('search_query')->nullable()->after('source_type');
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['author_name', 'keywords', 'source_type', 'search_query']);
        });
    }
};