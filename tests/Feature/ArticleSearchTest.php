<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Enums\UserStatus;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleSearchTest extends TestCase
{
    use RefreshDatabase;

    protected User $author;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);

        $this->author = User::factory()->create([
            'status' => UserStatus::Active,
            'account_type' => 'staff',
        ]);
        $this->author->assignRole('journalist');

        $this->category = Category::factory()->create([
            'name' => 'Politik',
            'slug' => 'politik',
            'is_active' => true,
        ]);
    }

    public function test_search_finds_published_articles_by_title_keyword(): void
    {
        $matching = Article::factory()->create([
            'title' => 'Pembangunan Kereta Cepat Nusantara',
            'status' => ArticleStatus::Published,
            'published_at' => now()->subDay(),
            'category_id' => $this->category->id,
            'author_id' => $this->author->id,
        ]);

        $nonMatching = Article::factory()->create([
            'title' => 'Perkembangan Ekonomi Digital',
            'status' => ArticleStatus::Published,
            'published_at' => now()->subDay(),
            'category_id' => $this->category->id,
            'author_id' => $this->author->id,
        ]);

        $response = $this->get(route('search', ['q' => 'Kereta Cepat']));

        $response->assertStatus(200);
        $response->assertSee('Pembangunan Kereta Cepat Nusantara');
        $response->assertDontSee('Perkembangan Ekonomi Digital');
    }

    public function test_search_never_returns_draft_or_in_review_articles(): void
    {
        Article::factory()->create([
            'title' => 'Rencana Rahasia Kereta Cepat',
            'status' => ArticleStatus::Draft,
            'published_at' => null,
            'category_id' => $this->category->id,
            'author_id' => $this->author->id,
        ]);

        Article::factory()->create([
            'title' => 'Review Naskah Kereta Cepat',
            'status' => ArticleStatus::InReview,
            'published_at' => null,
            'category_id' => $this->category->id,
            'author_id' => $this->author->id,
        ]);

        $response = $this->get(route('search', ['q' => 'Kereta Cepat']));

        $response->assertStatus(200);
        $response->assertDontSee('Rencana Rahasia Kereta Cepat');
        $response->assertDontSee('Review Naskah Kereta Cepat');
    }

    public function test_search_relevance_ranks_title_match_higher_than_content_only_match(): void
    {
        $bodyOnly = Article::factory()->create([
            'title' => 'Laporan Khusus Wilayah Timur',
            'content' => '<p>Wilayah timur berpotensi mengembangkan kecerdasan buatan dalam pertanian modern.</p>',
            'status' => ArticleStatus::Published,
            'published_at' => now()->subDays(2),
            'category_id' => $this->category->id,
            'author_id' => $this->author->id,
        ]);

        $titleMatch = Article::factory()->create([
            'title' => 'Kecerdasan Buatan Merevolusi Industri',
            'content' => '<p>Teknologi baru mengubah cara kerja.</p>',
            'status' => ArticleStatus::Published,
            'published_at' => now()->subDays(5),
            'category_id' => $this->category->id,
            'author_id' => $this->author->id,
        ]);

        $response = $this->get(route('search', ['q' => 'Kecerdasan Buatan', 'sort' => 'relevance']));

        $response->assertStatus(200);

        // Title match must appear before content-only match in HTML output
        $content = $response->getContent();
        $posTitle = strpos($content, 'Kecerdasan Buatan Merevolusi Industri');
        $posBody = strpos($content, 'Laporan Khusus Wilayah Timur');

        $this->assertNotFalse($posTitle);
        $this->assertNotFalse($posBody);
        $this->assertTrue($posTitle < $posBody, 'Title match must be ranked before content-only match.');
    }

    public function test_search_filters_by_category(): void
    {
        $catEkonomi = Category::factory()->create(['name' => 'Ekonomi', 'slug' => 'ekonomi']);

        Article::factory()->create([
            'title' => 'Inflasi Nasional Mengalami Penurunan',
            'category_id' => $this->category->id, // Politik
            'status' => ArticleStatus::Published,
            'published_at' => now()->subDay(),
            'author_id' => $this->author->id,
        ]);

        Article::factory()->create([
            'title' => 'Pertumbuhan Inflasi Kuartal Tiga',
            'category_id' => $catEkonomi->id, // Ekonomi
            'status' => ArticleStatus::Published,
            'published_at' => now()->subDay(),
            'author_id' => $this->author->id,
        ]);

        $response = $this->get(route('search', [
            'q' => 'Inflasi',
            'category' => 'ekonomi',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Pertumbuhan Inflasi Kuartal Tiga');
        $response->assertDontSee('Inflasi Nasional Mengalami Penurunan');
    }

    public function test_short_query_is_handled_safely(): void
    {
        $response = $this->get(route('search', ['q' => 'a']));

        $response->assertStatus(200);
        $response->assertSee('Pusat Pencarian Berita');
    }
}
