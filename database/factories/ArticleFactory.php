<?php

namespace Database\Factories;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);

        return [
            'uuid' => (string) Str::uuid(),
            'title' => $title,
            'slug' => Str::slug($title),
            'subtitle' => fake()->sentence(8),
            'excerpt' => fake()->paragraph(2),
            'content' => '<p>'.implode('</p><p>', fake()->paragraphs(4)).'</p>',
            'content_type' => ArticleType::News,
            'category_id' => Category::factory(),
            'author_id' => User::factory(),
            'editor_id' => null,
            'status' => ArticleStatus::Draft,
            'featured_image' => null,
            'featured_image_alt' => null,
            'featured_image_caption' => null,
            'source_name' => 'TopNews Redaksi',
            'source_url' => null,
            'is_featured' => false,
            'is_breaking' => false,
            'is_editor_choice' => false,
            'is_sponsored' => false,
            'allow_comments' => true,
            'reading_time' => 2,
            'submitted_at' => null,
            'review_started_at' => null,
            'approved_at' => null,
            'scheduled_at' => null,
            'published_at' => null,
            'archived_at' => null,
            'seo_title' => null,
            'seo_description' => null,
            'canonical_url' => null,
            'robots_index' => true,
        ];
    }

    /**
     * Article is in submitted state.
     */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ArticleStatus::Submitted,
            'submitted_at' => now()->subHours(2),
        ]);
    }

    /**
     * Article is in review state.
     */
    public function inReview(?User $editor = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ArticleStatus::InReview,
            'submitted_at' => now()->subHours(4),
            'review_started_at' => now()->subHour(),
            'editor_id' => $editor ? $editor->id : User::factory(),
        ]);
    }

    /**
     * Article is in revision requested state.
     */
    public function revisionRequested(?User $editor = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ArticleStatus::RevisionRequested,
            'submitted_at' => now()->subHours(6),
            'review_started_at' => now()->subHours(3),
            'editor_id' => $editor ? $editor->id : User::factory(),
        ]);
    }

    /**
     * Article is in approved state.
     */
    public function approved(?User $editor = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ArticleStatus::Approved,
            'submitted_at' => now()->subHours(8),
            'review_started_at' => now()->subHours(4),
            'approved_at' => now()->subHour(),
            'editor_id' => $editor ? $editor->id : User::factory(),
        ]);
    }

    /**
     * Article is in scheduled state.
     */
    public function scheduled(?CarbonInterface $scheduledAt = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ArticleStatus::Scheduled,
            'approved_at' => now()->subHour(),
            'scheduled_at' => $scheduledAt ?? now()->addDay(),
        ]);
    }

    /**
     * Article is in published state.
     */
    public function published(?CarbonInterface $publishedAt = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ArticleStatus::Published,
            'approved_at' => now()->subHours(2),
            'published_at' => $publishedAt ?? now()->subHour(),
        ]);
    }

    /**
     * Article is in archived state.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ArticleStatus::Archived,
            'published_at' => now()->subMonths(6),
            'archived_at' => now()->subWeek(),
        ]);
    }
}
