<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Enums\UserStatus;
use App\Models\Article;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkTest extends TestCase
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
            'title' => 'Liputan Khusus Investigasi TopNews',
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHour(),
            'category_id' => $category->id,
            'author_id' => $this->author->id,
        ]);
    }

    public function test_reader_can_bookmark_and_unbookmark_article(): void
    {
        // 1. First toggle -> Saved
        $response = $this->actingAs($this->reader)
            ->post(route('news.bookmark', $this->article));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $this->reader->id,
            'article_id' => $this->article->id,
        ]);
        $this->assertTrue($this->article->isBookmarkedBy($this->reader));

        // 2. Second toggle -> Removed
        $response2 = $this->actingAs($this->reader)
            ->post(route('news.bookmark', $this->article));

        $response2->assertSessionHas('success');
        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $this->reader->id,
            'article_id' => $this->article->id,
        ]);
        $this->assertFalse($this->article->isBookmarkedBy($this->reader));
    }

    public function test_guest_cannot_bookmark_article(): void
    {
        $response = $this->post(route('news.bookmark', $this->article));
        $response->assertRedirect(route('login'));
    }

    public function test_cannot_bookmark_draft_article(): void
    {
        $draft = Article::factory()->create([
            'status' => ArticleStatus::Draft,
            'published_at' => null,
            'author_id' => $this->author->id,
        ]);

        $response = $this->actingAs($this->reader)
            ->post(route('news.bookmark', $draft));

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $this->reader->id,
            'article_id' => $draft->id,
        ]);
    }

    public function test_reader_can_view_own_bookmarks_page(): void
    {
        Bookmark::create([
            'user_id' => $this->reader->id,
            'article_id' => $this->article->id,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->reader)
            ->get(route('account.bookmarks'));

        $response->assertStatus(200);
        $response->assertSee('Liputan Khusus Investigasi TopNews');
    }

    public function test_reader_cannot_delete_another_readers_bookmark(): void
    {
        $otherReader = User::factory()->create([
            'status' => UserStatus::Active,
            'account_type' => 'reader',
        ]);
        $otherReader->assignRole('reader');

        $bookmark = Bookmark::create([
            'user_id' => $otherReader->id,
            'article_id' => $this->article->id,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->reader)
            ->delete(route('bookmarks.destroy', $bookmark));

        $response->assertStatus(403);
    }
}
