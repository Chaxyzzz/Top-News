<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Models\AdCampaign;
use App\Models\AdSlot;
use App\Models\Advertisement;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Services\ContentSanitizerService;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

class FinalSystemAuditTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_seeder_is_fully_idempotent_and_creates_all_defaults(): void
    {
        // 1. Run DatabaseSeeder first time
        $this->seed(DatabaseSeeder::class);

        // Verify core models exist
        $this->assertDatabaseHas('roles', ['name' => 'super_admin']);
        $this->assertDatabaseHas('roles', ['name' => 'reader']);
        $this->assertDatabaseHas('settings', ['group' => 'general', 'key' => 'site_name']);
        $this->assertDatabaseHas('pages', ['slug' => 'tentang-kami']);
        $this->assertDatabaseHas('homepage_sections', ['key' => 'hero']);
        $this->assertDatabaseHas('menus', ['key' => 'primary']);
        $this->assertDatabaseHas('ad_slots', ['key' => 'home_leaderboard']);

        $categoryCount = Category::count();
        $this->assertGreaterThan(0, $categoryCount);

        // 2. Run DatabaseSeeder a second time to guarantee complete idempotency
        $this->seed(DatabaseSeeder::class);

        // Category count must remain identical (zero duplicate rows)
        $this->assertEquals($categoryCount, Category::count());
    }

    public function test_super_admin_safeguard_prevents_deletion_or_suspension_of_last_super_admin(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(SettingsSeeder::class);

        $superAdmin = User::factory()->create(['status' => 'active']);
        $superAdmin->assignRole('super_admin');

        // Sole active Super Admin cannot be deleted
        $response = $this->actingAs($superAdmin)->delete(route('admin.users.destroy', $superAdmin));
        $this->assertTrue(in_array($response->status(), [302, 403]));
        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);

        // Sole active Super Admin cannot be suspended
        $suspendResponse = $this->actingAs($superAdmin)->post(route('admin.users.suspend', $superAdmin));
        $this->assertTrue(in_array($suspendResponse->status(), [302, 403]));
        $this->assertEquals('active', $superAdmin->fresh()->status->value);
    }

    public function test_reader_access_matrix_forbidden_on_all_admin_endpoints(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(SettingsSeeder::class);

        $reader = User::factory()->create(['account_type' => 'reader', 'status' => 'active']);
        $reader->assignRole('reader');

        $adminRoutes = [
            'admin.newsroom.index',
            'admin.articles.index',
            'admin.categories.index',
            'admin.tags.index',
            'admin.users.index',
            'admin.roles.index',
            'admin.media.index',
            'admin.galleries.index',
            'admin.comments.index',
            'admin.homepage.index',
            'admin.breaking-news.index',
            'admin.navigation.index',
            'admin.advertising.overview',
            'admin.advertising.ads.index',
            'admin.newsletter.index',
            'admin.contacts.index',
            'admin.settings.index',
            'admin.analytics.overview',
        ];

        foreach ($adminRoutes as $routeName) {
            $response = $this->actingAs($reader)->get(route($routeName));
            $this->assertEquals(
                403,
                $response->status(),
                "Reader should be forbidden (403) from accessing {$routeName}"
            );
        }
    }

    public function test_article_public_visibility_matrix(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(SettingsSeeder::class);

        $author = User::factory()->create();
        $category = Category::factory()->create();

        // 1. Published article -> 200 OK
        $published = Article::factory()->create([
            'author_id' => $author->id,
            'category_id' => $category->id,
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHour(),
            'slug' => 'artikel-terbit-publik-sukses',
        ]);
        $this->get(route('news.show', $published->slug))->assertOk();

        // 2. All non-published states must strictly return 404
        $hiddenStatuses = [
            ArticleStatus::Draft,
            ArticleStatus::Submitted,
            ArticleStatus::InReview,
            ArticleStatus::RevisionRequested,
            ArticleStatus::Approved,
            ArticleStatus::Archived,
        ];

        foreach ($hiddenStatuses as $status) {
            $hiddenArticle = Article::factory()->create([
                'author_id' => $author->id,
                'category_id' => $category->id,
                'status' => $status,
                'published_at' => now()->subDay(),
                'slug' => 'artikel-status-rahasia-'.$status->value,
            ]);

            $this->get(route('news.show', $hiddenArticle->slug))
                ->assertNotFound();
        }

        // 3. Future scheduled article must return 404
        $scheduledFuture = Article::factory()->create([
            'author_id' => $author->id,
            'category_id' => $category->id,
            'status' => ArticleStatus::Scheduled,
            'published_at' => now()->addDays(2),
            'slug' => 'artikel-jadwal-masa-depan',
        ]);
        $this->get(route('news.show', $scheduledFuture->slug))->assertNotFound();

        // 4. Non-existent slug must return 404
        $this->get(route('news.show', 'slug-tidak-pernah-ada-999'))->assertNotFound();
    }

    public function test_empty_state_resilience_across_all_public_portals(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(SettingsSeeder::class);

        // With zero articles, zero videos, zero photo stories, zero ads:
        $publicRoutes = [
            '/',
            '/latest',
            '/popular',
            '/trending',
            '/opinion',
            '/video',
            '/photo-story',
            '/about',
            '/contact',
            '/editorial',
            '/sitemap.xml',
            '/robots.txt',
        ];

        foreach ($publicRoutes as $uri) {
            $response = $this->get($uri);
            $this->assertEquals(
                200,
                $response->status(),
                "Public route {$uri} failed to render cleanly in empty database state"
            );
        }
    }

    public function test_ad_click_open_redirect_protection(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(SettingsSeeder::class);

        $slot = AdSlot::create([
            'key' => 'test_slot',
            'name' => 'Test Slot',
            'placement' => 'homepage',
            'device_scope' => 'all',
            'width' => 728,
            'height' => 90,
            'is_active' => true,
        ]);

        $user = User::factory()->create();

        $campaign = AdCampaign::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Official Advertiser Campaign',
            'advertiser' => 'Verified Advertiser Ltd',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $validDestination = 'https://advertiser-verified-example.com/promo';

        $ad = Advertisement::create([
            'uuid' => (string) Str::uuid(),
            'campaign_id' => $campaign->id,
            'ad_slot_id' => $slot->id,
            'name' => 'Safe Banner Creative',
            'destination_url' => $validDestination,
            'alt_text' => 'Official Promo',
            'is_active' => true,
        ]);

        // 1. Legitimate ad click redirects to stored destination
        $response = $this->get(route('ads.click', $ad->uuid));
        $response->assertRedirect($validDestination);

        // 2. Open redirect manipulation attempt (e.g. ?redirect_to=evil.com) is completely ignored
        $attackResponse = $this->get(route('ads.click', $ad->uuid).'?redirect_to=https://evil-phishing-site.com');
        $attackResponse->assertRedirect($validDestination);
        $this->assertNotEquals('https://evil-phishing-site.com', $attackResponse->headers->get('Location'));
    }

    public function test_xss_sanitization_defense_across_inputs(): void
    {
        $maliciousSnippet = '<p>Normal text <script>alert("XSS")</script><img src="x" onerror="alert(1)"><a href="javascript:alert(1)">Link</a></p>';
        $clean = ContentSanitizerService::sanitize($maliciousSnippet);

        $this->assertStringNotContainsString('<script>', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringContainsString('Normal text', $clean);
    }

    public function test_timezone_and_locale_configuration(): void
    {
        $this->assertEquals('Asia/Jakarta', config('app.timezone'));
        $this->assertEquals('id', config('app.locale'));
    }
}
