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
        // 1. Daily Platform Overall Stats
        Schema::create('site_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->unsignedBigInteger('page_views')->default(0);
            $table->unsignedBigInteger('unique_sessions')->default(0);
            $table->unsignedBigInteger('article_views')->default(0);
            $table->unsignedBigInteger('searches')->default(0);
            $table->unsignedBigInteger('comments_submitted')->default(0);
            $table->unsignedBigInteger('newsletter_subscriptions')->default(0);
            $table->timestamps();

            $table->index('date');
        });

        // 2. Daily Traffic Sources (Host/Domain Only, Privacy-Safe)
        Schema::create('traffic_source_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('source_type', 30)->default('direct'); // direct, search, social, internal, referral, unknown
            $table->string('source_domain', 190)->default('direct');
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('unique_views')->default(0);
            $table->timestamps();

            $table->unique(['date', 'source_type', 'source_domain'], 'uq_traffic_source_daily');
            $table->index(['date', 'source_type']);
        });

        // 3. Daily Device Breakdown (Desktop, Mobile, Tablet, Unknown)
        Schema::create('device_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('device_type', 30)->default('desktop');
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamps();

            $table->unique(['date', 'device_type'], 'uq_device_daily');
            $table->index(['date', 'device_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_daily_stats');
        Schema::dropIfExists('traffic_source_daily_stats');
        Schema::dropIfExists('site_daily_stats');
    }
};
