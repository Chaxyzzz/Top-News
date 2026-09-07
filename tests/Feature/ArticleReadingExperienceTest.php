<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Services\RelatedArticleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleReadingExperienceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_published_article_loads_successfully_with_complete_reading_landmarks(): void
    {
        $category = Category::create([
            'name' => 'Investigasi',
            'slug' => 'investigasi',
            'description' => 'Kanal investigasi',
            'is_active' => true,
        ]);

        $author = User::factory()->create([
            'name' => 'Budi Santoso',
            'username' => 'budisantoso',
        ]);

        $tag = Tag::create(['name' => 'Transparansi', 'slug' => 'transparansi']);

        $article = Article::create([
            'title' => 'Laporan Khusus: Jejak Transformasi Energi Bersih Indonesia',
            'slug' => 'laporan-khusus-jejak-transformasi-energi-bersih',
            'subtitle' => 'Investigasi mendalam mengenai transisi energi terbarukan di tanah air.',
            'content' => '<p>Pemerintah bersama para pemangku kepentingan terus mempercepat pembangunan pembangkit listrik tenaga surya dan bayu.</p>',
            'excerpt' => 'Investigasi mendalam mengenai transisi energi terbarukan di tanah air.',
            'category_id' => $category->id,
            'author_id' => $author->id,
            'status' => ArticleStatus::Published,
            'content_type' => ArticleType::News,
            'reading_time' => 5,
            'published_at' => now()->subHours(2),
        ]);

        $article->tags()->attach($tag);

        $response = $this->get(route('news.show', $article->slug));

        $response->assertStatus(200);
        $response->assertSee('Laporan Khusus: Jejak Transformasi Energi Bersih Indonesia');
        $response->assertSee('Investigasi mendalam mengenai transisi energi terbarukan di tanah air.');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Investigasi');
        $response->assertSee('#Transparansi');
        $response->assertSee('5 menit baca');
        $response->assertSee('Tentang Penulis');
        $response->assertSee('Bagikan');
        $response->assertSee('Salin Tautan');
    }

    public function test_unpublished_article_states_strictly_return_404_publicly(): void
    {
        $category = Category::first();
        $author = User::factory()->create();

        $statuses = [
            ArticleStatus::Draft,
            ArticleStatus::Submitted,
            ArticleStatus::InReview,
            ArticleStatus::RevisionRequested,
            ArticleStatus::Approved,
            ArticleStatus::Archived,
        ];

        foreach ($statuses as $status) {
            $article = Article::create([
                'title' => "Artikel Status {$status->value}",
                'slug' => "artikel-status-{$status->value}",
                'content' => '<p>Konten rahasia internal</p>',
                'category_id' => $category->id,
                'author_id' => $author->id,
                'status' => $status,
                'published_at' => null,
            ]);

            $response = $this->get(route('news.show', $article->slug));
            $response->assertStatus(404);
        }

        // Scheduled in future
        $futureArticle = Article::create([
            'title' => 'Artikel Masa Depan',
            'slug' => 'artikel-masa-depan',
            'content' => '<p>Konten belum tayang</p>',
            'category_id' => $category->id,
            'author_id' => $author->id,
            'status' => ArticleStatus::Published,
            'published_at' => now()->addDays(2),
        ]);

        $this->get(route('news.show', $futureArticle->slug))->assertStatus(404);
    }

    public function test_sponsored_disclosure_is_rendered_when_article_is_sponsored(): void
    {
        $category = Category::first();
        $author = User::factory()->create();

        $sponsoredArticle = Article::create([
            'title' => 'Inovasi Teknologi Perbankan Masa Depan',
            'slug' => 'inovasi-teknologi-perbankan-masa-depan',
            'content' => '<p>Layanan perbankan digital terkini memudahkan transaksi masyarakat.</p>',
            'category_id' => $category->id,
            'author_id' => $author->id,
            'status' => ArticleStatus::Published,
            'is_sponsored' => true,
            'published_at' => now()->subHour(),
        ]);

        $response = $this->get(route('news.show', $sponsoredArticle->slug));

        $response->assertStatus(200);
        $response->assertSee('ADVERTORIAL / SPONSORED');
    }

    public function test_editorial_correction_note_is_rendered_when_present(): void
    {
        $category = Category::first();
        $author = User::factory()->create();

        $article = Article::create([
            'title' => 'Perkembangan Pembangunan MRT Fase 3',
            'slug' => 'perkembangan-pembangunan-mrt-fase-3',
            'content' => '<p>Rute pembangunan MRT diperbarui sesuai keputusan terbaru pemerintah.</p>',
            'category_id' => $category->id,
            'author_id' => $author->id,
            'status' => ArticleStatus::Published,
            'correction_note' => 'Pembaruan data rute stasiun dilakukan pada 28 Agustus 2026 pukul 14.00 WIB.',
            'published_at' => now()->subHours(3),
        ]);

        $response = $this->get(route('news.show', $article->slug));

        $response->assertStatus(200);
        $response->assertSee('Catatan Koreksi / Pembaruan Redaksi');
        $response->assertSee('Pembaruan data rute stasiun dilakukan pada 28 Agustus 2026 pukul 14.00 WIB.');
    }

    public function test_related_articles_service_prioritizes_matching_category_and_tags(): void
    {
        $categoryA = Category::create(['name' => 'Kanal A', 'slug' => 'kanal-a', 'is_active' => true]);
        $categoryB = Category::create(['name' => 'Kanal B', 'slug' => 'kanal-b', 'is_active' => true]);
        $author = User::factory()->create();

        $tagTech = Tag::create(['name' => 'Tech', 'slug' => 'tech']);

        $mainArticle = Article::create([
            'title' => 'Artikel Utama Tech',
            'slug' => 'artikel-utama-tech',
            'content' => '<p>Konten utama</p>',
            'category_id' => $categoryA->id,
            'author_id' => $author->id,
            'status' => ArticleStatus::Published,
            'published_at' => now()->subDay(),
        ]);
        $mainArticle->tags()->attach($tagTech);

        $sameCategoryAndTagArticle = Article::create([
            'title' => 'Artikel Terkait Sangat Relevan',
            'slug' => 'artikel-terkait-sangat-relevan',
            'content' => '<p>Konten relevan</p>',
            'category_id' => $categoryA->id,
            'author_id' => $author->id,
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHours(5),
        ]);
        $sameCategoryAndTagArticle->tags()->attach($tagTech);

        $service = app(RelatedArticleService::class);
        $related = $service->getRelatedArticles($mainArticle, 3);

        $this->assertTrue($related->contains('id', $sameCategoryAndTagArticle->id));
        $this->assertFalse($related->contains('id', $mainArticle->id));
    }

    public function test_chronological_next_and_previous_navigation(): void
    {
        $category = Category::create(['name' => 'Otomotif', 'slug' => 'otomotif', 'is_active' => true]);
        $author = User::factory()->create();

        $article1 = Article::create([
            'title' => 'Artikel Pertama Otomotif',
            'slug' => 'artikel-pertama-otomotif',
            'content' => '<p>Konten 1</p>',
            'category_id' => $category->id,
            'author_id' => $author->id,
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHours(10),
        ]);

        $article2 = Article::create([
            'title' => 'Artikel Kedua Otomotif',
            'slug' => 'artikel-kedua-otomotif',
            'content' => '<p>Konten 2</p>',
            'category_id' => $category->id,
            'author_id' => $author->id,
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHours(5),
        ]);

        $article3 = Article::create([
            'title' => 'Artikel Ketiga Otomotif',
            'slug' => 'artikel-ketiga-otomotif',
            'content' => '<p>Konten 3</p>',
            'category_id' => $category->id,
            'author_id' => $author->id,
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHours(1),
        ]);

        $service = app(RelatedArticleService::class);
        $nav = $service->getPreviousAndNext($article2);

        $this->assertNotNull($nav['previous']);
        $this->assertEquals($article1->id, $nav['previous']->id);

        $this->assertNotNull($nav['next']);
        $this->assertEquals($article3->id, $nav['next']->id);
    }
}
