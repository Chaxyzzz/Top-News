<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\ArticleDailyStat;
use App\Models\Category;
use App\Models\User;
use App\Services\ArticleViewService;
use App\Services\PopularArticleService;
use App\Services\TrendingArticleService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ArticleViewAndTrendingTest extends TestCase
{
    use RefreshDatabase;

    protected User $journalist;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        Article::query()->forceDelete();
        Cache::flush();

        $this->journalist = User::whereHas('roles', fn ($q) => $q->where('name', 'journalist'))->first()
            ?? User::factory()->create();
        $this->category = Category::first() ?? Category::factory()->create();
    }

    public function test_view_service_records_views_and_deduplicates_unique_views(): void
    {
        $article = Article::factory()->create([
            'title' => 'Uji Coba Metrik Keterbacaan Artikel',
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHours(2),
            'category_id' => $this->category->id,
            'author_id' => $this->journalist->id,
            'views_count' => 0,
        ]);

        $service = app(ArticleViewService::class);

        // First view in session
        $this->assertTrue($service->recordView($article));

        $stat = ArticleDailyStat::where('article_id', $article->id)
            ->where('date', Carbon::today()->toDateString())
            ->first();

        $this->assertNotNull($stat);
        $this->assertEquals(1, $stat->views);
        $this->assertEquals(1, $stat->unique_views);
        $this->assertEquals(1, $article->fresh()->views_count);

        // Second view in same session
        $service->recordView($article);
        $stat->refresh();

        $this->assertEquals(2, $stat->views);
        $this->assertEquals(1, $stat->unique_views); // deduplicated
        $this->assertEquals(2, $article->fresh()->views_count);
    }

    public function test_reading_page_triggers_view_recording(): void
    {
        $article = Article::factory()->create([
            'slug' => 'baca-berita-dan-rekam-statistik',
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHour(),
            'category_id' => $this->category->id,
            'author_id' => $this->journalist->id,
            'views_count' => 0,
        ]);

        $response = $this->get(route('news.show', $article->slug));

        $response->assertStatus(200);
        $this->assertEquals(1, $article->fresh()->views_count);
    }

    public function test_trending_service_calculates_and_sorts_by_velocity(): void
    {
        $articleA = Article::factory()->create([
            'title' => 'Artikel Cepat Viral',
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHours(2), // Very fresh
            'category_id' => $this->category->id,
            'author_id' => $this->journalist->id,
        ]);

        $articleB = Article::factory()->create([
            'title' => 'Artikel Lama Banyak View',
            'status' => ArticleStatus::Published,
            'published_at' => now()->subDays(10), // Old
            'category_id' => $this->category->id,
            'author_id' => $this->journalist->id,
        ]);

        ArticleDailyStat::create([
            'article_id' => $articleA->id,
            'date' => Carbon::today()->toDateString(),
            'views' => 100,
            'unique_views' => 80,
        ]);

        ArticleDailyStat::create([
            'article_id' => $articleB->id,
            'date' => Carbon::today()->toDateString(),
            'views' => 100,
            'unique_views' => 80,
        ]);

        Cache::flush();
        $trendingService = app(TrendingArticleService::class);
        $results = $trendingService->getTrending(2);

        $this->assertNotEmpty($results);
        $this->assertEquals($articleA->id, $results->first()->id);
    }

    public function test_popular_service_filters_by_timeframe(): void
    {
        $article = Article::factory()->create([
            'title' => 'Artikel Populer Minggu Ini',
            'status' => ArticleStatus::Published,
            'published_at' => now()->subDays(3),
            'category_id' => $this->category->id,
            'author_id' => $this->journalist->id,
        ]);

        ArticleDailyStat::create([
            'article_id' => $article->id,
            'date' => Carbon::now()->subDays(2)->toDateString(),
            'views' => 500,
            'unique_views' => 400,
        ]);

        Cache::flush();
        $popularService = app(PopularArticleService::class);
        $results7Days = $popularService->getPopular('7days', 5);

        $this->assertTrue($results7Days->contains('id', $article->id));
    }

    public function test_trending_service_empty_cache_returns_eloquent_collection(): void
    {
        Cache::flush();
        $trendingService = app(TrendingArticleService::class);
        $result = $trendingService->getTrending(5);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function test_trending_service_cached_result_returns_eloquent_collection(): void
    {
        $article = Article::factory()->create([
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHour(),
            'category_id' => $this->category->id,
            'author_id' => $this->journalist->id,
            'views_count' => 10,
        ]);

        Cache::flush();
        $trendingService = app(TrendingArticleService::class);

        // First call populates cache
        $firstCall = $trendingService->getTrending(5);
        $this->assertInstanceOf(Collection::class, $firstCall);
        $this->assertCount(1, $firstCall);

        // Second call retrieves from cache
        $secondCall = $trendingService->getTrending(5);
        $this->assertInstanceOf(Collection::class, $secondCall);
        $this->assertCount(1, $secondCall);
        $this->assertEquals($article->id, $secondCall->first()->id);
    }

    public function test_trending_service_handles_corrupt_legacy_cache_gracefully(): void
    {
        $article = Article::factory()->create([
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHour(),
            'category_id' => $this->category->id,
            'author_id' => $this->journalist->id,
            'views_count' => 5,
        ]);

        // Manually place corrupt / invalid value into the cache key
        Cache::put('topnews.trending.v2.limit_5', 'corrupt_string_value', 600);
        Cache::put('topnews.trending.limit_5', new \stdClass, 600);

        $trendingService = app(TrendingArticleService::class);
        $result = $trendingService->getTrending(5);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals($article->id, $result->first()->id);
    }

    public function test_trending_service_omits_deleted_or_unpublished_articles(): void
    {
        $published = Article::factory()->create([
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHour(),
            'category_id' => $this->category->id,
            'author_id' => $this->journalist->id,
            'views_count' => 10,
        ]);

        $draft = Article::factory()->create([
            'status' => ArticleStatus::Draft,
            'published_at' => null,
            'category_id' => $this->category->id,
            'author_id' => $this->journalist->id,
            'views_count' => 10,
        ]);

        // Manually seed cache payload containing both published ID and draft ID and non-existent ID
        Cache::put('topnews.trending.v2.limit_5', [
            ['id' => $published->id, 'trending_score' => 10.0, 'recent_views' => 10],
            ['id' => $draft->id, 'trending_score' => 9.0, 'recent_views' => 10],
            ['id' => 999999, 'trending_score' => 8.0, 'recent_views' => 10],
        ], 600);

        $trendingService = app(TrendingArticleService::class);
        $result = $trendingService->getTrending(5);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals($published->id, $result->first()->id);
    }

    public function test_popular_service_handles_corrupt_cache(): void
    {
        Article::factory()->create([
            'status' => ArticleStatus::Published,
            'published_at' => now()->subDays(2),
            'category_id' => $this->category->id,
            'author_id' => $this->journalist->id,
            'views_count' => 50,
        ]);

        Cache::put('topnews.popular.v2.7days.limit_5', 'invalid_cached_data', 600);

        $popularService = app(PopularArticleService::class);
        $result = $popularService->getPopular('7days', 5);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertNotEmpty($result);
    }
}
