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
        Schema::table('articles', function (Blueprint $table) {
            $table->unsignedInteger('homepage_priority')->default(0)->after('is_breaking');
            $table->unsignedBigInteger('views_count')->default(0)->after('reading_time');

            $table->index(['status', 'homepage_priority', 'published_at'], 'idx_articles_homepage_priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex('idx_articles_homepage_priority');
            $table->dropColumn(['homepage_priority', 'views_count']);
        });
    }
};
