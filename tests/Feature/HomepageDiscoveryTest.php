<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Services\PublicContentCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    protected User $editor;

    protected User $journalist;

    protected Category $techCategory;

    protected Category $economyCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->editor = User::whereHas('roles', fn ($q) => $q->where('name', 'editor'))->first()
            ?? User::factory()->create();
        $this->journalist = User::whereHas('roles', fn ($q) => $q->where('name', 'journalist'))->first()
            ?? User::factory()->create();

        $this->techCategory = Category::where('slug', 'teknologi')->first()
            ?? Category::factory()->create(['name' => 'Teknologi', 'slug' => 'teknologi']);
        $this->economyCategory = Category::where('slug', 'ekonomi')->first()
            ?? Category::factory()->create(['name' => 'Ekonomi', 'slug' => 'ekonomi']);
    }

    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('TopNews');
    }

    public function test_lead_story_is_selected_by_highest_homepage_priority(): void
    {
        $highPriorityArticle = Article::factory()->create([
            'title' => 'Prioritas Tertinggi Headline Berita',
            'status' => ArticleStatus::Published,
            'homepage_priority' => 99,
            'is_featured' => true,
            'published_at' => now()->subHour(),
            'category_id' => $this->techCategory->id,
            'author_id' => $this->journalist->id,
        ]);

        $normalArticle = Article::factory()->create([
            'title' => 'Berita Biasa Tanpa Prioritas',
            'status' => ArticleStatus::Published,
            'homepage_priority' => 0,
            'is_featured' => false,
            'published_at' => now()->subMinutes(10),
            'category_id' => $this->economyCategory->id,
            'author_id' => $this->journalist->id,
        ]);

        // Invalidate cache to see fresh test articles
        app(PublicContentCacheService::class)->invalidateHomepage();

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Prioritas Tertinggi Headline Berita');
    }

    public function test_unpublished_and_draft_articles_are_strictly_excluded_from_homepage(): void
    {
        $draftArticle = Article::factory()->create([
            'title' => 'Naskah Rahasia Draf Belum Terbit',
            'status' => ArticleStatus::Draft,
            'published_at' => null,
            'category_id' => $this->techCategory->id,
            'author_id' => $this->journalist->id,
        ]);

        $scheduledArticle = Article::factory()->create([
            'title' => 'Naskah Terjadwal Masa Depan',
            'status' => ArticleStatus::Scheduled,
            'published_at' => now()->addDay(),
            'category_id' => $this->techCategory->id,
            'author_id' => $this->journalist->id,
        ]);

        app(PublicContentCacheService::class)->invalidateHomepage();

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertDontSee('Naskah Rahasia Draf Belum Terbit');
        $response->assertDontSee('Naskah Terjadwal Masa Depan');
    }

    public function test_breaking_news_bar_shows_only_active_published_breaking_news(): void
    {
        $breakingArticle = Article::factory()->create([
            'title' => 'Peringatan Dini Cuaca Ekstrem Terkini',
            'status' => ArticleStatus::Published,
            'is_breaking' => true,
            'published_at' => now()->subMinutes(15),
            'category_id' => $this->economyCategory->id,
            'author_id' => $this->journalist->id,
        ]);

        app(PublicContentCacheService::class)->invalidateHomepage();

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('BREAKING NEWS');
        $response->assertSee('Peringatan Dini Cuaca Ekstrem Terkini');
    }

    public function test_opinion_section_only_displays_opinion_content_type(): void
    {
        $opinionArticle = Article::factory()->create([
            'title' => 'Kolom Refleksi Kedaulatan Digital',
            'content_type' => ArticleType::Opinion,
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHours(2),
            'category_id' => $this->techCategory->id,
            'author_id' => $this->journalist->id,
        ]);

        app(PublicContentCacheService::class)->invalidateHomepage();

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Kolom Refleksi Kedaulatan Digital');
    }
}
