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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->text('subtitle')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('content_type', 50)->default('news')->index();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('author_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('editor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('draft')->index();

            // Media & Source
            $table->string('featured_image', 255)->nullable();
            $table->string('featured_image_alt', 255)->nullable();
            $table->text('featured_image_caption')->nullable();
            $table->string('source_name', 150)->nullable();
            $table->string('source_url', 255)->nullable();

            // Editorial Flags
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_breaking')->default(false);
            $table->boolean('is_editor_choice')->default(false);
            $table->boolean('is_sponsored')->default(false);
            $table->boolean('allow_comments')->default(true);
            $table->unsignedSmallInteger('reading_time')->default(1);

            // Workflow Timestamps
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('review_started_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('archived_at')->nullable();

            // Basic SEO
            $table->string('seo_title', 255)->nullable();
            $table->text('seo_description')->nullable();
            $table->string('canonical_url', 255)->nullable();
            $table->boolean('robots_index')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Composite indexes for high-speed news queries
            $table->index(['status', 'published_at']);
            $table->index(['category_id', 'status', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
