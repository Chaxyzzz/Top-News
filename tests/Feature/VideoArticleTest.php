<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoArticleTest extends TestCase
{
    use RefreshDatabase;

    protected User $journalist;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->journalist = User::factory()->create(['status' => 'active']);
        $this->journalist->assignRole('journalist');

        $this->category = Category::factory()->create(['name' => 'Teknologi', 'slug' => 'teknologi']);
    }

    public function test_video_article_creation_normalizes_youtube_url(): void
    {
        $response = $this->actingAs($this->journalist)->post(route('admin.articles.store'), [
            'title' => 'Liputan Video Peluncuran AI Terbaru',
            'content' => '<p>Deskripsi berita video.</p>',
            'category_id' => $this->category->id,
            'content_type' => ArticleType::Video->value,
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration_seconds' => 212,
        ]);

        $this->assertDatabaseHas('articles', [
            'title' => 'Liputan Video Peluncuran AI Terbaru',
            'content_type' => ArticleType::Video->value,
        ]);

        $this->assertDatabaseHas('article_videos', [
            'video_id' => 'dQw4w9WgXcQ',
            'provider' => 'youtube',
            'embed_url' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0&modestbranding=1',
            'duration_seconds' => 212,
        ]);
    }

    public function test_published_video_article_renders_responsive_player(): void
    {
        $article = Article::create([
            'title' => 'Video Wawancara Eksklusif',
            'slug' => 'video-wawancara-eksklusif',
            'content' => '<p>Pembahasan mendalam.</p>',
            'category_id' => $this->category->id,
            'author_id' => $this->journalist->id,
            'status' => ArticleStatus::Published,
            'content_type' => ArticleType::Video,
            'published_at' => now()->subHour(),
        ]);

        $article->video()->create([
            'provider' => 'youtube',
            'video_id' => 'dQw4w9WgXcQ',
            'embed_url' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0&modestbranding=1',
            'duration_seconds' => 180,
        ]);

        $response = $this->get(route('news.show', $article->slug));

        $response->assertStatus(200);
        $response->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ');
    }

    public function test_public_video_index_page_displays_published_videos(): void
    {
        $article = Article::create([
            'title' => 'Video Berita Terpopuler Minggu Ini',
            'slug' => 'video-berita-terpopuler-minggu-ini',
            'content' => '<p>Rangkuman video.</p>',
            'category_id' => $this->category->id,
            'author_id' => $this->journalist->id,
            'status' => ArticleStatus::Published,
            'content_type' => ArticleType::Video,
            'published_at' => now()->subHours(2),
        ]);

        $article->video()->create([
            'provider' => 'youtube',
            'video_id' => 'dQw4w9WgXcQ',
            'embed_url' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            'duration_seconds' => 120,
        ]);

        $response = $this->get(route('video.index'));

        $response->assertStatus(200);
        $response->assertSee('TopNews Video');
        $response->assertSee('Video Berita Terpopuler Minggu Ini');
    }
}
