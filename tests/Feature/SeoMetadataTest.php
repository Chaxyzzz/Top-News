<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoMetadataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(SettingsSeeder::class);
    }

    public function test_homepage_contains_site_seo_metadata_and_structured_data(): void
    {
        $response = $this->get(route('home'));
        $response->assertOk();

        // Checks title, description, and canonical
        $response->assertSee('<meta name="robots" content="index,follow"', false);
        $response->assertSee('<link rel="canonical" href="'.url('/'), false);

        // Open Graph
        $response->assertSee('<meta property="og:type" content="website"', false);
        $response->assertSee('<meta property="og:site_name"', false);

        // Structured Data: WebSite and Organization
        $response->assertSee('"@type": "WebSite"', false);
        $response->assertSee('"@type": "NewsMediaOrganization"', false);
        $response->assertSee('"@type": "SearchAction"', false);
    }

    public function test_article_uses_metadata_fallbacks_correctly(): void
    {
        $author = User::factory()->create(['name' => 'Budi Santoso', 'email' => 'private-budi@example.com']);
        $category = Category::factory()->create(['name' => 'Nasional', 'slug' => 'nasional']);

        // 1. Article with explicit seo_title and seo_description
        $articleWithSeo = Article::factory()->create([
            'author_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Judul Berita Asli',
            'seo_title' => 'Judul Khusus Mesin Pencari',
            'excerpt' => 'Ringkasan singkat berita asli.',
            'seo_description' => 'Deskripsi khusus meta tag Google.',
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHour(),
            'robots_index' => true,
        ]);

        $res1 = $this->get(route('news.show', $articleWithSeo->slug));
        $res1->assertOk();
        $res1->assertSee('<title>Judul Khusus Mesin Pencari</title>', false);
        $res1->assertSee('Deskripsi khusus meta tag Google', false);
        $res1->assertSee('<meta name="robots" content="index,follow"', false);

        // 2. Article without SEO overrides (should fallback to title + site name and excerpt)
        $articleFallback = Article::factory()->create([
            'author_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Investigasi Dana Publik',
            'seo_title' => null,
            'excerpt' => 'Laporan investigasi mendalam perihal alokasi APBN.',
            'seo_description' => null,
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHour(),
            'robots_index' => true,
        ]);

        $res2 = $this->get(route('news.show', $articleFallback->slug));
        $res2->assertOk();
        $res2->assertSee('<title>Investigasi Dana Publik — TopNews</title>', false);
        $res2->assertSee('Laporan investigasi mendalam perihal alokasi APBN.', false);
    }

    public function test_article_structured_data_matches_content_type_and_protects_author_privacy(): void
    {
        $author = User::factory()->create([
            'name' => 'Ratna Sari',
            'username' => 'ratnasari',
            'email' => 'ratna.secret@topnews.id',
            'phone' => '+6281234567890',
        ]);
        $category = Category::factory()->create(['name' => 'Politik', 'slug' => 'politik']);

        $newsArticle = Article::factory()->create([
            'author_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Debat Kebijakan Publik',
            'content_type' => ArticleType::News,
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHours(2),
            'robots_index' => true,
        ]);

        $response = $this->get(route('news.show', $newsArticle->slug));
        $response->assertOk();

        // JSON-LD NewsArticle
        $response->assertSee('"@type": "NewsArticle"', false);
        $response->assertSee('"headline": "Debat Kebijakan Publik"', false);
        $response->assertSee('"@type": "Person"', false);
        $response->assertSee('"name": "Ratna Sari"', false);

        // Crucial Privacy Check: Never expose private email or phone in JSON-LD
        $response->assertDontSee('ratna.secret@topnews.id');
        $response->assertDontSee('+6281234567890');

        // BreadcrumbList
        $response->assertSee('"@type": "BreadcrumbList"', false);
        $response->assertSee('"name": "Beranda"', false);
        $response->assertSee('"name": "Politik"', false);
    }

    public function test_search_results_page_strictly_outputs_noindex_follow(): void
    {
        $response = $this->get(route('search', ['q' => 'pemilu']));
        $response->assertOk();
        $response->assertSee('<meta name="robots" content="noindex,follow"', false);
    }

    public function test_archive_pages_preserve_query_page_in_canonical(): void
    {
        $category = Category::factory()->create(['name' => 'Teknologi', 'slug' => 'teknologi']);
        User::factory()->create();

        $page1Url = route('category.show', $category->slug);
        $page2Url = route('category.show', $category->slug).'?page=2';

        $res1 = $this->get($page1Url);
        $res1->assertOk();
        $res1->assertSee('<link rel="canonical" href="'.$page1Url.'"', false);

        $res2 = $this->get($page2Url);
        $res2->assertOk();
        $res2->assertSee('<link rel="canonical" href="'.$page2Url.'"', false);
    }
}
