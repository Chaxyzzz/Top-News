<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Enums\ReactionType;
use App\Enums\UserStatus;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleReactionTest extends TestCase
{
    use RefreshDatabase;

    protected User $reader;

    protected User $author;

    protected Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->reader = User::factory()->create([
            'status' => UserStatus::Active,
            'account_type' => 'reader',
        ]);
        $this->reader->assignRole('reader');

        $this->author = User::factory()->create([
            'status' => UserStatus::Active,
            'account_type' => 'staff',
        ]);
        $this->author->assignRole('journalist');

        $category = Category::factory()->create(['is_active' => true]);

        $this->article = Article::factory()->create([
            'title' => 'Inovasi Panel Surya Generasi Baru',
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHours(1),
            'category_id' => $category->id,
            'author_id' => $this->author->id,
        ]);
    }

    public function test_reader_can_react_to_article(): void
    {
        $response = $this->actingAs($this->reader)
            ->post(route('news.reaction', $this->article), [
                'reaction' => 'useful',
            ]);

        $this->assertDatabaseHas('article_reactions', [
            'article_id' => $this->article->id,
            'user_id' => $this->reader->id,
            'reaction_type' => 'useful',
        ]);

        $this->assertEquals(ReactionType::Useful, $this->article->getUserReaction($this->reader));
    }

    public function test_reader_toggling_same_reaction_removes_it(): void
    {
        // First reaction
        $this->actingAs($this->reader)
            ->post(route('news.reaction', $this->article), ['reaction' => 'useful']);

        // Toggle again
        $this->actingAs($this->reader)
            ->post(route('news.reaction', $this->article), ['reaction' => 'useful']);

        $this->assertDatabaseMissing('article_reactions', [
            'article_id' => $this->article->id,
            'user_id' => $this->reader->id,
        ]);

        $this->assertNull($this->article->getUserReaction($this->reader));
    }

    public function test_reader_changing_reaction_updates_existing_record(): void
    {
        $this->actingAs($this->reader)
            ->post(route('news.reaction', $this->article), ['reaction' => 'useful']);

        $this->actingAs($this->reader)
            ->post(route('news.reaction', $this->article), ['reaction' => 'important']);

        $this->assertDatabaseCount('article_reactions', 1);
        $this->assertDatabaseHas('article_reactions', [
            'article_id' => $this->article->id,
            'user_id' => $this->reader->id,
            'reaction_type' => 'important',
        ]);
    }

    public function test_aggregate_counts_reflect_multiple_user_reactions(): void
    {
        $reader2 = User::factory()->create(['status' => UserStatus::Active, 'account_type' => 'reader']);
        $reader2->assignRole('reader');

        $this->actingAs($this->reader)
            ->post(route('news.reaction', $this->article), ['reaction' => 'useful']);

        $this->actingAs($reader2)
            ->post(route('news.reaction', $this->article), ['reaction' => 'useful']);

        $counts = $this->article->getReactionCounts();
        $this->assertEquals(2, $counts['useful']);
        $this->assertEquals(0, $counts['interesting']);
        $this->assertEquals(0, $counts['important']);
    }

    public function test_guest_cannot_react_to_article(): void
    {
        $response = $this->post(route('news.reaction', $this->article), ['reaction' => 'useful']);
        $response->assertRedirect(route('login'));
    }
}
