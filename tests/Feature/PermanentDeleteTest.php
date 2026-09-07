<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Enums\UserStatus;
use App\Models\Article;
use App\Models\ArticleDailyStat;
use App\Models\ArticleEditorialAction;
use App\Models\ArticleReaction;
use App\Models\ArticleRevision;
use App\Models\Bookmark;
use App\Models\BreakingNews;
use App\Models\Category;
use App\Models\Comment;
use App\Models\HomepageSection;
use App\Models\Media;
use App\Models\Page;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PermanentDeleteTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create(['status' => UserStatus::Active]);
        $this->superAdmin->assignRole('super_admin');
    }

    public function test_article_permanent_delete_succeeds_without_undefined_method_error(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $media = Media::create([
            'original_filename' => 'test-image.jpg',
            'filename' => 'test-image.jpg',
            'extension' => 'jpg',
            'disk' => 'public',
            'path' => 'media/2026/08/test-image.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'width' => 1920,
            'height' => 1080,
            'user_id' => $this->superAdmin->id,
        ]);

        $article = Article::factory()->create([
            'category_id' => $category->id,
            'author_id' => $this->superAdmin->id,
            'featured_media_id' => $media->id,
            'status' => ArticleStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $article->tags()->attach($tag->id);

        $section = HomepageSection::create([
            'title' => 'Test Section',
            'key' => 'test_section_'.uniqid(),
            'section_type' => 'custom_curated',
            'source_type' => 'manual',
            'layout_variant' => 'grid_4',
            'item_limit' => 4,
            'is_active' => true,
        ]);
        $article->curatedInSections()->attach($section->id, ['sort_order' => 1]);

        ArticleDailyStat::create([
            'article_id' => $article->id,
            'date' => now()->toDateString(),
            'views' => 10,
        ]);

        ArticleRevision::create([
            'article_id' => $article->id,
            'user_id' => $this->superAdmin->id,
            'revision_number' => 1,
            'title' => $article->title,
            'content' => $article->content,
            'content_type' => ArticleType::News,
        ]);

        ArticleEditorialAction::create([
            'article_id' => $article->id,
            'user_id' => $this->superAdmin->id,
            'action' => 'published',
        ]);

        Bookmark::create([
            'article_id' => $article->id,
            'user_id' => $this->superAdmin->id,
        ]);

        ArticleReaction::create([
            'article_id' => $article->id,
            'user_id' => $this->superAdmin->id,
            'reaction_type' => 'useful',
            'ip_hash' => 'test-ip',
        ]);

        Comment::create([
            'article_id' => $article->id,
            'user_id' => $this->superAdmin->id,
            'body' => 'Test comment',
            'status' => 'approved',
        ]);

        BreakingNews::create([
            'headline' => 'Test Breaking',
            'article_id' => $article->id,
            'is_active' => true,
            'created_by' => $this->superAdmin->id,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.articles.destroy', $article));

        $response->assertRedirect(route('admin.articles.index'));
        $response->assertSessionHas('success');

        // Confirm Article permanently removed
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);

        // Confirm pivot & owned child records cleaned up
        $this->assertDatabaseMissing('article_tag', ['article_id' => $article->id]);
        $this->assertDatabaseMissing('homepage_section_articles', ['article_id' => $article->id]);
        $this->assertDatabaseMissing('article_daily_stats', ['article_id' => $article->id]);
        $this->assertDatabaseMissing('article_revisions', ['article_id' => $article->id]);
        $this->assertDatabaseMissing('article_editorial_actions', ['article_id' => $article->id]);
        $this->assertDatabaseMissing('bookmarks', ['article_id' => $article->id]);
        $this->assertDatabaseMissing('article_reactions', ['article_id' => $article->id]);
        $this->assertDatabaseMissing('comments', ['article_id' => $article->id]);
        $this->assertDatabaseMissing('breaking_news', ['article_id' => $article->id]);

        // Confirm shared resources remain intact
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('tags', ['id' => $tag->id]);
        $this->assertDatabaseHas('media', ['id' => $media->id]);

        // Confirm public GET returns 404
        $publicResponse = $this->get(route('news.show', $article->slug));
        $publicResponse->assertNotFound();
    }

    public function test_user_permanent_delete_safeguards_work(): void
    {
        // 1. Self deletion blocked by policy (403 Forbidden)
        $selfResponse = $this->actingAs($this->superAdmin)
            ->delete(route('admin.users.destroy', $this->superAdmin));
        $selfResponse->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $this->superAdmin->id]);

        // 2. Regular staff user deletion succeeds and reassigns author articles
        $staffUser = User::factory()->create(['status' => UserStatus::Active]);
        $role = Role::firstOrCreate(['name' => 'journalist'], ['label' => 'Journalist', 'is_system' => true]);
        $staffUser->roles()->sync([$role->id]);

        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'category_id' => $category->id,
            'author_id' => $staffUser->id,
        ]);

        $deleteResponse = $this->actingAs($this->superAdmin)
            ->delete(route('admin.users.destroy', $staffUser));

        $deleteResponse->assertRedirect(route('admin.users.index'));
        $deleteResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $staffUser->id]);
        $this->assertDatabaseHas('articles', ['id' => $article->id, 'author_id' => $this->superAdmin->id]);
    }

    public function test_category_and_page_deletion_succeeds(): void
    {
        $category = Category::factory()->create();
        $catResponse = $this->actingAs($this->superAdmin)
            ->delete(route('admin.categories.destroy', $category));
        $catResponse->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);

        $page = Page::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
            'content' => 'Page content',
            'is_system' => false,
            'status' => 'published',
            'created_by' => $this->superAdmin->id,
        ]);
        $pageResponse = $this->actingAs($this->superAdmin)
            ->delete(route('admin.pages.destroy', $page));
        $pageResponse->assertRedirect(route('admin.pages.index'));
        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }
}
