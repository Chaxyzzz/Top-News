<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Models\Article;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduledPublishingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_scheduled_command_publishes_past_due_articles(): void
    {
        // 1. Article scheduled in past (should be published)
        $dueArticle = Article::factory()->scheduled(now()->subMinutes(5))->create([
            'title' => 'Artikel Terjadwal yang Sudah Waktunya Tayang',
        ]);

        // 2. Article scheduled in future (should remain scheduled)
        $futureArticle = Article::factory()->scheduled(now()->addHour())->create([
            'title' => 'Artikel Terjadwal untuk Masa Depan',
        ]);

        $this->artisan('topnews:publish-scheduled')
            ->assertSuccessful();

        $dueArticle->refresh();
        $futureArticle->refresh();

        $this->assertEquals(ArticleStatus::Published, $dueArticle->status);
        $this->assertNotNull($dueArticle->published_at);

        $this->assertEquals(ArticleStatus::Scheduled, $futureArticle->status);
    }
}
