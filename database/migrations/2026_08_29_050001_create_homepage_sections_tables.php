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
        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('key')->unique();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('section_type'); // hero, latest, category_highlight, trending, popular, editors_choice, opinion, video, photo_story, custom_curated
            $table->string('source_type');  // automatic, category, manual, system
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->unsignedTinyInteger('item_limit')->default(4);
            $table->string('layout_variant'); // hero_primary, grid_4, grid_3, lead_plus_list, horizontal_list, numbered_list, opinion_cards, media_grid
            $table->json('settings')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
            $table->index('section_type');
            $table->index('source_type');
        });

        Schema::create('homepage_section_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('homepage_section_id')->constrained('homepage_sections')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['homepage_section_id', 'article_id']);
            $table->index(['homepage_section_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_section_articles');
        Schema::dropIfExists('homepage_sections');
    }
};
