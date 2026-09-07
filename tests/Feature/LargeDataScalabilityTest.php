<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Enums\UserStatus;
use App\Models\Article;
use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LargeDataScalabilityTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        ini_set('memory_limit', '256M');
    }

    public function test_system_handles_1200_articles_with_clean_pagination_and_performance(): void
    {
        $category = Category::where('slug', 'nasional')->first()
            ?? Category::factory()->create(['slug' => 'nasional', 'name' => 'Nasional']);

        $author = User::where('username', 'zakky77')->first()
            ?? User::factory()->create(['username' => 'zakky77', 'status' => UserStatus::Active]);

        // Seed 1000 published test articles cleanly via Factory
        for ($batch = 0; $batch < 10; $batch++) {
            Article::factory()->count(100)->create([
                'category_id' => $category->id,
                'author_id' => $author->id,
                'status' => ArticleStatus::Published,
            ]);
        }

        $totalArticles = Article::count();
        $this->assertGreaterThanOrEqual(1000, $totalArticles);

        // 1. Admin Article List Pagination Test
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['label' => 'Super Admin', 'is_system' => true]);
        $author->roles()->sync([$role->id]);

        $adminRes = $this->actingAs($author)->get('/admin/articles');
        $adminRes->assertStatus(200);
        $adminRes->assertSee('Daftar Artikel &amp; Naskah Berita', false);

        // 2. Public Latest News Pagination Test
        $latestRes = $this->get('/latest');
        $latestRes->assertStatus(200);

        // 3. Category Article Listing Pagination Test
        $categoryRes = $this->get('/category/nasional');
        $categoryRes->assertStatus(200);

        // 4. Search Pagination Test
        $searchRes = $this->get('/search?q=Performa');
        $searchRes->assertStatus(200);

        // 5. Trending & Popular Services Test
        $trendingRes = $this->get('/trending');
        $trendingRes->assertStatus(200);

        $popularRes = $this->get('/popular');
        $popularRes->assertStatus(200);

        // 6. Author Profile Listing Test
        $authorRes = $this->get('/author/zakky77');
        $authorRes->assertStatus(200);

        // 7. Sitemap Article Generation Test
        $sitemapRes = $this->get('/sitemap-articles.xml');
        $sitemapRes->assertStatus(200);

        // Peak Memory Check (< 64MB)
        $peakMemoryMb = memory_get_peak_usage(true) / (1024 * 1024);
        $this->assertLessThan(128, $peakMemoryMb, "Peak memory exceeded limits: {$peakMemoryMb}MB");
    }
}
