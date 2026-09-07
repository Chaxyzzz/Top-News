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
            // Composite index for author profile and author content analytics
            $table->index(['author_id', 'status', 'published_at'], 'idx_articles_author_status_published');

            // Composite index for homepage/breaking/featured filtering
            $table->index(['status', 'is_featured', 'published_at'], 'idx_articles_status_featured_published');
            $table->index(['status', 'is_breaking', 'published_at'], 'idx_articles_status_breaking_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex('idx_articles_author_status_published');
            $table->dropIndex('idx_articles_status_featured_published');
            $table->dropIndex('idx_articles_status_breaking_published');
        });
    }
};
