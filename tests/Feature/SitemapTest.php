<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\PageSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(SettingsSeeder::class);
        $this->seed(PageSeeder::class);
        Cache::flush();
    }

    public function test_sitemap_index_returns_valid_xml_with_sub_sitemaps(): void
    {
        $response = $this->get('/sitemap.xml');
        $response->assertOk();
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        $response->assertSee('<sitemapindex', false);
        $response->assertSee(url('/sitemap-articles.xml'), false);
        $response->assertSee(url('/sitemap-pages.xml'), false);
        $response->assertSee(url('/sitemap-categories.xml'), false);
        $response->assertSee(url('/sitemap-authors.xml'), false);
    }

    public function test_articles_sitemap_includes_published_and_excludes_draft_and_archived(): void
    {
        $author = User::factory()->create();
        $category = Category::factory()->create();

        $published = Article::factory()->create([
            'author_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Artikel Terbit Publik',
            'slug' => 'artikel-terbit-publik',
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHours(5),
        ]);

        $draft = Article::factory()->create([
            'author_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Artikel Draf Rahasia',
            'slug' => 'artikel-draf-rahasia',
            'status' => ArticleStatus::Draft,
            'published_at' => null,
        ]);

        $futureScheduled = Article::factory()->create([
            'author_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Artikel Terjadwal Besok',
            'slug' => 'artikel-terjadwal-besok',
            'status' => ArticleStatus::Scheduled,
            'published_at' => now()->addDay(),
        ]);

        $response = $this->get('/sitemap-articles.xml');
        $response->assertOk();
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));

        $response->assertSee(route('news.show', $published->slug), false);
        $response->assertDontSee(route('news.show', $draft->slug), false);
        $response->assertDontSee(route('news.show', $futureScheduled->slug), false);
    }

    public function test_pages_sitemap_includes_core_institutional_pages(): void
    {
        $response = $this->get('/sitemap-pages.xml');
        $response->assertOk();
        $response->assertSee(route('about'), false);
        $response->assertSee(route('editorial.guidelines'), false);
        $response->assertSee(route('privacy'), false);
        $response->assertSee(route('terms'), false);
        $response->assertSee(route('contact'), false);
    }

    public function test_google_news_sitemap_contains_only_recent_news_within_48_hours(): void
    {
        $author = User::factory()->create();
        $category = Category::factory()->create();

        // 1. Recent news (within 48h)
        $recentNews = Article::factory()->create([
            'author_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Berita Hangat 2 Jam Lalu',
            'slug' => 'berita-hangat-2-jam-lalu',
            'content_type' => ArticleType::News,
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHours(2),
        ]);

        // 2. Old news (> 48h)
        $oldNews = Article::factory()->create([
            'author_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Berita Lama Seminggu Lalu',
            'slug' => 'berita-lama-seminggu-lalu',
            'content_type' => ArticleType::News,
            'status' => ArticleStatus::Published,
            'published_at' => now()->subDays(7),
        ]);

        $response = $this->get('/news-sitemap.xml');
        $response->assertOk();
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        $response->assertSee('xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"', false);
        $response->assertSee('<news:name>TopNews</news:name>', false);
        $response->assertSee('<news:language>id</news:language>', false);

        // Recent news is present
        $response->assertSee(route('news.show', $recentNews->slug), false);
        $response->assertSee('Berita Hangat 2 Jam Lalu', false);

        // Old news is excluded per Google News 48h specification
        $response->assertDontSee(route('news.show', $oldNews->slug), false);
        $response->assertDontSee('Berita Lama Seminggu Lalu', false);
    }
}
