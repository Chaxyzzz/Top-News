<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\BreakingNews;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BreakingNewsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $editorInChief;

    protected User $reader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->editorInChief = User::factory()->create();
        $this->editorInChief->assignRole('editor_in_chief');

        $this->reader = User::factory()->create();
        $this->reader->assignRole('reader');
    }

    public function test_editor_in_chief_can_create_breaking_news_with_article(): void
    {
        $category = Category::factory()->create();
        $article = Article::factory()->published()->create(['category_id' => $category->id]);

        $response = $this->actingAs($this->editorInChief)->post(route('admin.breaking-news.store'), [
            'headline' => 'Warta Darurat Bencana Alam',
            'article_id' => $article->id,
            'priority' => 10,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.breaking-news.index'));
        $this->assertDatabaseHas('breaking_news', [
            'headline' => 'Warta Darurat Bencana Alam',
            'article_id' => $article->id,
            'priority' => 10,
            'is_active' => true,
        ]);
    }

    public function test_editor_in_chief_can_create_breaking_news_with_external_url(): void
    {
        $response = $this->actingAs($this->editorInChief)->post(route('admin.breaking-news.store'), [
            'headline' => 'Keterangan Pers Resmi Kepresidenan',
            'external_url' => 'https://presidenri.go.id/siaran-pers',
            'priority' => 5,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.breaking-news.index'));
        $this->assertDatabaseHas('breaking_news', [
            'headline' => 'Keterangan Pers Resmi Kepresidenan',
            'external_url' => 'https://presidenri.go.id/siaran-pers',
        ]);
    }

    public function test_public_homepage_renders_active_breaking_news(): void
    {
        $breaking = BreakingNews::create([
            'headline' => 'Breaking: Sidang Paripurna DPR Dimulai',
            'external_url' => 'https://example.com/live',
            'priority' => 10,
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Breaking: Sidang Paripurna DPR Dimulai');
    }

    public function test_unpublished_article_breaking_news_is_suppressed_from_public(): void
    {
        $category = Category::factory()->create();
        $draftArticle = Article::factory()->create([
            'category_id' => $category->id,
            'status' => ArticleStatus::Draft,
            'published_at' => null,
        ]);

        BreakingNews::create([
            'headline' => 'Warta Rahasia Masih Draf',
            'article_id' => $draftArticle->id,
            'priority' => 10,
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('Warta Rahasia Masih Draf');
    }

    public function test_expired_breaking_news_is_not_displayed_publicly(): void
    {
        BreakingNews::create([
            'headline' => 'Peringatan Cuaca Kemarin Lusa',
            'external_url' => 'https://bmkg.go.id',
            'starts_at' => now()->subDays(3),
            'ends_at' => now()->subHour(),
            'priority' => 10,
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('Peringatan Cuaca Kemarin Lusa');
    }

    public function test_admin_can_toggle_active_and_delete_breaking_news(): void
    {
        $breaking = BreakingNews::create([
            'headline' => 'Warta Sementara',
            'external_url' => 'https://example.com',
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        // Toggle active
        $toggleRes = $this->actingAs($this->admin)->post(route('admin.breaking-news.toggle-active', $breaking));
        $toggleRes->assertSessionHas('success');
        $this->assertFalse($breaking->fresh()->is_active);

        // Delete
        $delRes = $this->actingAs($this->admin)->delete(route('admin.breaking-news.destroy', $breaking));
        $delRes->assertRedirect(route('admin.breaking-news.index'));
        $this->assertDatabaseMissing('breaking_news', ['id' => $breaking->id]);
    }
}
