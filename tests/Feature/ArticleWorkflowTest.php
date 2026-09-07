<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Models\Article;
use App\Models\ArticleRevision;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_journalist_can_create_and_save_draft(): void
    {
        $journalist = User::factory()->create();
        $journalist->assignRole('journalist');

        $category = Category::factory()->create();

        $response = $this->actingAs($journalist)->post(route('admin.articles.store'), [
            'title' => 'Liputan Khusus Perkembangan IKN',
            'content' => '<p>Pembangunan infrastruktur IKN terus berlanjut secara intensif.</p>',
            'category_id' => $category->id,
            'content_type' => 'news',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('articles', [
            'title' => 'Liputan Khusus Perkembangan IKN',
            'status' => ArticleStatus::Draft->value,
            'author_id' => $journalist->id,
        ]);
    }

    public function test_journalist_can_submit_own_draft_for_review(): void
    {
        $journalist = User::factory()->create();
        $journalist->assignRole('journalist');

        $article = Article::factory()->create([
            'author_id' => $journalist->id,
            'status' => ArticleStatus::Draft,
        ]);

        $response = $this->actingAs($journalist)->post(route('admin.articles.submit', $article));

        $response->assertRedirect();
        $article->refresh();
        $this->assertEquals(ArticleStatus::Submitted, $article->status);
        $this->assertNotNull($article->submitted_at);
    }

    public function test_editor_can_start_review_and_request_revision(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('editor');

        $article = Article::factory()->submitted()->create();

        // 1. Start review
        $response = $this->actingAs($editor)->post(route('admin.articles.review.start', $article));
        $response->assertRedirect();
        $article->refresh();
        $this->assertEquals(ArticleStatus::InReview, $article->status);
        $this->assertEquals($editor->id, $article->editor_id);

        // 2. Request revision
        $revResponse = $this->actingAs($editor)->post(route('admin.articles.revision.request', $article), [
            'note' => 'Perjelas data statistik di paragraf 3 dan lengkapi nama narasumber.',
        ]);
        $revResponse->assertRedirect();
        $article->refresh();
        $this->assertEquals(ArticleStatus::RevisionRequested, $article->status);

        $this->assertDatabaseHas('article_editorial_actions', [
            'article_id' => $article->id,
            'action' => 'revision_requested',
            'note' => 'Perjelas data statistik di paragraf 3 dan lengkapi nama narasumber.',
        ]);
    }

    public function test_editor_can_approve_and_publish_article(): void
    {
        $editorInChief = User::factory()->create();
        $editorInChief->assignRole('editor_in_chief');

        $article = Article::factory()->inReview()->create();

        // 1. Approve
        $approveResponse = $this->actingAs($editorInChief)->post(route('admin.articles.approve', $article));
        $approveResponse->assertRedirect();
        $article->refresh();
        $this->assertEquals(ArticleStatus::Approved, $article->status);
        $this->assertNotNull($article->approved_at);

        // 2. Publish
        $publishResponse = $this->actingAs($editorInChief)->post(route('admin.articles.publish', $article));
        $publishResponse->assertRedirect();
        $article->refresh();
        $this->assertEquals(ArticleStatus::Published, $article->status);
        $this->assertNotNull($article->published_at);
    }

    public function test_journalist_cannot_directly_publish_or_approve_draft(): void
    {
        $journalist = User::factory()->create();
        $journalist->assignRole('journalist');

        $article = Article::factory()->create([
            'author_id' => $journalist->id,
            'status' => ArticleStatus::Draft,
        ]);

        $publishResponse = $this->actingAs($journalist)->post(route('admin.articles.publish', $article));
        $publishResponse->assertStatus(403);

        $approveResponse = $this->actingAs($journalist)->post(route('admin.articles.approve', $article));
        $approveResponse->assertStatus(403);

        $article->refresh();
        $this->assertEquals(ArticleStatus::Draft, $article->status);
    }

    public function test_journalist_cannot_edit_another_journalists_draft(): void
    {
        $journalist1 = User::factory()->create();
        $journalist1->assignRole('journalist');

        $journalist2 = User::factory()->create();
        $journalist2->assignRole('journalist');

        $article = Article::factory()->create([
            'author_id' => $journalist1->id,
            'status' => ArticleStatus::Draft,
        ]);

        $response = $this->actingAs($journalist2)->put(route('admin.articles.update', $article), [
            'title' => 'Judul yang Dibajak',
            'content' => '<p>Konten bajakan</p>',
            'category_id' => $article->category_id,
            'content_type' => 'news',
        ]);

        $response->assertStatus(403);
    }

    public function test_public_route_returns_404_for_unpublished_articles_and_200_for_published(): void
    {
        $category = Category::factory()->create();

        // 1. Draft -> 404
        $draft = Article::factory()->create(['status' => ArticleStatus::Draft, 'category_id' => $category->id]);
        $this->get(route('news.show', $draft->slug))->assertStatus(404);

        // 2. Submitted -> 404
        $submitted = Article::factory()->submitted()->create(['category_id' => $category->id]);
        $this->get(route('news.show', $submitted->slug))->assertStatus(404);

        // 3. Approved -> 404
        $approved = Article::factory()->approved()->create(['category_id' => $category->id]);
        $this->get(route('news.show', $approved->slug))->assertStatus(404);

        // 4. Future Scheduled -> 404
        $scheduled = Article::factory()->scheduled(now()->addDays(2))->create(['category_id' => $category->id]);
        $this->get(route('news.show', $scheduled->slug))->assertStatus(404);

        // 5. Archived -> 404
        $archived = Article::factory()->archived()->create(['category_id' => $category->id]);
        $this->get(route('news.show', $archived->slug))->assertStatus(404);

        // 6. Published -> 200
        $published = Article::factory()->published()->create([
            'category_id' => $category->id,
            'title' => 'Berita Resmi Terbit di Portal TopNews',
        ]);
        $publishedResponse = $this->get(route('news.show', $published->slug));
        $publishedResponse->assertStatus(200);
        $publishedResponse->assertSee('Berita Resmi Terbit di Portal TopNews');
    }

    public function test_article_revision_can_be_restored(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $article = Article::factory()->create([
            'title' => 'Versi Asli Artikel',
            'content' => '<p>Isi naskah versi 1</p>',
        ]);

        // Create snapshot revision
        $revision = ArticleRevision::create([
            'article_id' => $article->id,
            'user_id' => $superAdmin->id,
            'revision_number' => 1,
            'title' => 'Versi Asli Artikel',
            'content' => '<p>Isi naskah versi 1</p>',
            'content_type' => ArticleType::News->value,
            'created_at' => now(),
        ]);

        // Update article with changes
        $article->update([
            'title' => 'Versi Baru yang Diubah',
            'content' => '<p>Isi naskah versi 2</p>',
        ]);

        // Restore to revision 1
        $response = $this->actingAs($superAdmin)->post(route('admin.articles.revisions.restore', [$article, $revision]));

        $response->assertRedirect();
        $article->refresh();
        $this->assertEquals('Versi Asli Artikel', $article->title);
        $this->assertEquals('<p>Isi naskah versi 1</p>', $article->content);
    }
}
