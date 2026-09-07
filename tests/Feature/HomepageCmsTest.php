<?php

namespace Tests\Feature;

use App\Enums\HomepageLayoutVariant;
use App\Enums\HomepageSectionType;
use App\Enums\HomepageSourceType;
use App\Models\Article;
use App\Models\Category;
use App\Models\HomepageSection;
use App\Models\User;
use App\Services\HomepageConfigurationService;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class HomepageCmsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $editor;

    protected User $reader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->editor = User::factory()->create();
        $this->editor->assignRole('editor');

        $this->reader = User::factory()->create();
        $this->reader->assignRole('reader');
    }

    public function test_admin_can_view_homepage_sections_index(): void
    {
        HomepageSection::create([
            'key' => 'hero_test',
            'title' => 'Hero Test',
            'section_type' => HomepageSectionType::Hero,
            'source_type' => HomepageSourceType::System,
            'layout_variant' => HomepageLayoutVariant::HeroPrimary,
            'item_limit' => 5,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.homepage.index'));

        $response->assertOk();
        $response->assertSee('Hero Test');
        $response->assertSee('Tata Letak Redaksi');
    }

    public function test_unauthorized_user_cannot_access_homepage_cms(): void
    {
        $response = $this->actingAs($this->reader)->get(route('admin.homepage.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_update_section_configuration(): void
    {
        $section = HomepageSection::create([
            'key' => 'latest_test',
            'title' => 'Berita Terkini Lama',
            'section_type' => HomepageSectionType::Latest,
            'source_type' => HomepageSourceType::Automatic,
            'layout_variant' => HomepageLayoutVariant::Grid4,
            'item_limit' => 6,
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.homepage.update', $section), [
            'title' => 'Berita Terkini Baru',
            'subtitle' => 'Subjudul baru',
            'source_type' => HomepageSourceType::Automatic->value,
            'layout_variant' => HomepageLayoutVariant::Grid4->value,
            'item_limit' => 8,
            'sort_order' => 2,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.homepage.index'));
        $this->assertDatabaseHas('homepage_sections', [
            'id' => $section->id,
            'title' => 'Berita Terkini Baru',
            'item_limit' => 8,
        ]);
    }

    public function test_admin_can_reorder_homepage_sections(): void
    {
        $sec1 = HomepageSection::create([
            'key' => 'sec_1',
            'title' => 'Bagian 1',
            'section_type' => HomepageSectionType::Trending,
            'source_type' => HomepageSourceType::Automatic,
            'layout_variant' => HomepageLayoutVariant::NumberedList,
            'item_limit' => 5,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $sec2 = HomepageSection::create([
            'key' => 'sec_2',
            'title' => 'Bagian 2',
            'section_type' => HomepageSectionType::Popular,
            'source_type' => HomepageSourceType::Automatic,
            'layout_variant' => HomepageLayoutVariant::HorizontalList,
            'item_limit' => 5,
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.homepage.reorder'), [
            'order' => [
                $sec1->id => 10,
                $sec2->id => 5,
            ],
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(10, $sec1->fresh()->sort_order);
        $this->assertEquals(5, $sec2->fresh()->sort_order);
    }

    public function test_editor_can_curate_articles_for_manual_section(): void
    {
        $category = Category::factory()->create();
        $art1 = Article::factory()->published()->create(['category_id' => $category->id]);
        $art2 = Article::factory()->published()->create(['category_id' => $category->id]);

        $section = HomepageSection::create([
            'key' => 'custom_section',
            'title' => 'Pilihan Khusus',
            'section_type' => HomepageSectionType::CustomCurated,
            'source_type' => HomepageSourceType::Manual,
            'layout_variant' => HomepageLayoutVariant::Grid3,
            'item_limit' => 5,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->editor)->post(route('admin.homepage.curate.update', $section), [
            'article_ids' => [$art2->id, $art1->id],
        ]);

        $response->assertRedirect(route('admin.homepage.index'));
        $this->assertDatabaseHas('homepage_section_articles', [
            'homepage_section_id' => $section->id,
            'article_id' => $art2->id,
            'sort_order' => 1,
        ]);
        $this->assertDatabaseHas('homepage_section_articles', [
            'homepage_section_id' => $section->id,
            'article_id' => $art1->id,
            'sort_order' => 2,
        ]);
    }

    public function test_admin_can_preview_homepage_with_preview_banner(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.homepage.preview'));

        $response->assertOk();
        $response->assertSee('MODE PRATINJAU REDAKSI');
    }

    public function test_get_active_sections_returns_eloquent_collection_and_recovers_from_corrupt_cache(): void
    {
        $service = app(HomepageConfigurationService::class);

        // 1. Cache Miss
        Cache::flush();
        $result = $service->getActiveSections();
        $this->assertInstanceOf(Collection::class, $result);

        // 2. Cache Hit
        $result2 = $service->getActiveSections();
        $this->assertInstanceOf(Collection::class, $result2);

        // 3. Corrupt / Invalid Cache Recovery
        Cache::put(HomepageConfigurationService::CACHE_KEY, 'corrupt_string_data', 3600);
        $result3 = $service->getActiveSections();
        $this->assertInstanceOf(Collection::class, $result3);
    }
}
