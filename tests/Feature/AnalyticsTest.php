<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Services\AnalyticsService;
use App\Services\ArticleViewService;
use Carbon\Carbon;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(SettingsSeeder::class);
        Cache::flush();
    }

    public function test_article_view_records_aggregates_without_mutating_article_updated_at(): void
    {
        $author = User::factory()->create();
        $category = Category::factory()->create();

        // Create article with an editorial timestamp in the past
        $originalModifiedTime = Carbon::now()->subDays(3)->startOfSecond();

        $article = Article::factory()->create([
            'author_id' => $author->id,
            'category_id' => $category->id,
            'status' => ArticleStatus::Published,
            'published_at' => Carbon::now()->subDays(4),
            'updated_at' => $originalModifiedTime,
            'views_count' => 0,
        ]);

        $this->assertEquals($originalModifiedTime->toDateTimeString(), $article->fresh()->updated_at->toDateTimeString());

        // Record a view event
        $viewService = app(ArticleViewService::class);
        $recorded = $viewService->recordView($article);
        $this->assertTrue($recorded);

        // 1. Check article views incremented
        $refreshedArticle = $article->fresh();
        $this->assertEquals(1, $refreshedArticle->views_count);

        // 2. CRITICAL SEO CHECK: Article updated_at MUST NOT have changed!
        $this->assertEquals(
            $originalModifiedTime->toDateTimeString(),
            $refreshedArticle->updated_at->toDateTimeString(),
            'View tracking must not mutate article updated_at timestamp'
        );

        // 3. Check article daily stat recorded
        $today = Carbon::today()->toDateString();
        $this->assertDatabaseHas('article_daily_stats', [
            'article_id' => $article->id,
            'date' => $today,
            'views' => 1,
            'unique_views' => 1,
        ]);

        // 4. Check platform site daily stat recorded
        $this->assertDatabaseHas('site_daily_stats', [
            'date' => $today,
            'article_views' => 1,
            'page_views' => 1,
            'unique_sessions' => 1,
        ]);

        // 5. Check traffic source daily stat recorded
        $this->assertDatabaseHas('traffic_source_daily_stats', [
            'date' => $today,
            'source_type' => 'direct',
            'source_domain' => 'direct',
            'views' => 1,
        ]);

        // 6. Check device daily stat recorded
        $this->assertDatabaseHas('device_daily_stats', [
            'date' => $today,
            'views' => 1,
        ]);
    }

    public function test_privacy_no_raw_ip_columns_in_analytics_tables(): void
    {
        $this->assertFalse(Schema::hasColumn('site_daily_stats', 'ip_address'));
        $this->assertFalse(Schema::hasColumn('site_daily_stats', 'ip'));
        $this->assertFalse(Schema::hasColumn('traffic_source_daily_stats', 'ip_address'));
        $this->assertFalse(Schema::hasColumn('traffic_source_daily_stats', 'ip'));
        $this->assertFalse(Schema::hasColumn('device_daily_stats', 'ip_address'));
    }

    public function test_reader_cannot_access_analytics_dashboard(): void
    {
        $reader = User::factory()->create();
        $reader->assignRole('reader');

        $this->actingAs($reader)
            ->get(route('admin.analytics.overview'))
            ->assertForbidden();
    }

    public function test_journalist_sees_scoped_content_and_cannot_access_traffic_channel_tab(): void
    {
        $journalist = User::factory()->create();
        $journalist->assignRole('journalist');

        // Journalist can view overview with author scoping
        $resOverview = $this->actingAs($journalist)
            ->get(route('admin.analytics.overview'));
        $resOverview->assertOk();
        $resOverview->assertSee('Mode Penulis: Khusus Artikel Anda');

        // Journalist cannot access traffic tab (restricted to editors and admins)
        $this->actingAs($journalist)
            ->get(route('admin.analytics.traffic'))
            ->assertForbidden();
    }

    public function test_admin_has_full_access_to_all_analytics_tabs(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->get(route('admin.analytics.overview'))->assertOk();
        $this->actingAs($admin)->get(route('admin.analytics.content'))->assertOk();
        $this->actingAs($admin)->get(route('admin.analytics.traffic'))->assertOk();
    }

    public function test_analytics_service_calculates_and_zero_fills_time_series(): void
    {
        $start = Carbon::today()->subDays(6)->startOfDay();
        $end = Carbon::today()->endOfDay();

        $service = app(AnalyticsService::class);
        $overview = $service->getOverviewMetrics($start, $end);

        $this->assertCount(7, $overview['trend_labels']);
        $this->assertCount(7, $overview['trend_data']);
        $this->assertIsInt($overview['total_views']);
        $this->assertIsInt($overview['total_unique']);
    }
}
