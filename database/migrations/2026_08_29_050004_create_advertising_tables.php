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
        Schema::create('ad_slots', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // home_leaderboard, home_inline_1, article_top, article_inline_1, article_bottom, sidebar
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('placement'); // homepage, article, sidebar, general
            $table->string('device_scope')->default('all'); // all, desktop, mobile
            $table->unsignedSmallInteger('width')->nullable();
            $table->unsignedSmallInteger('height')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'placement']);
        });

        Schema::create('ad_campaigns', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('advertiser');
            $table->string('status')->default('draft'); // draft, active, paused, ended
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('budget_note')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('starts_at');
            $table->index('ends_at');
            $table->index(['status', 'starts_at', 'ends_at']);
        });

        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('campaign_id')->constrained('ad_campaigns')->cascadeOnDelete();
            $table->foreignId('ad_slot_id')->constrained('ad_slots')->restrictOnDelete();
            $table->string('name');
            $table->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('headline')->nullable();
            $table->text('body')->nullable();
            $table->string('destination_url');
            $table->string('alt_text')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->unsignedBigInteger('impressions_count')->default(0);
            $table->unsignedBigInteger('clicks_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['ad_slot_id', 'is_active']);
            $table->index(['campaign_id', 'is_active']);
            $table->index(['is_active', 'priority']);
        });

        Schema::create('ad_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->constrained('advertisements')->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('impressions')->default(0);
            $table->unsignedInteger('clicks')->default(0);
            $table->timestamps();

            $table->unique(['advertisement_id', 'date']);
            $table->index(['date', 'advertisement_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_daily_stats');
        Schema::dropIfExists('advertisements');
        Schema::dropIfExists('ad_campaigns');
        Schema::dropIfExists('ad_slots');
    }
};
