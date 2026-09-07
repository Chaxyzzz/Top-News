<?php

namespace Tests\Feature;

use App\Enums\PageStatus;
use App\Enums\PageType;
use App\Models\Page;
use App\Models\User;
use Database\Seeders\PageSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaticPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(SettingsSeeder::class);
        $this->seed(PageSeeder::class);
    }

    public function test_public_can_access_published_core_pages(): void
    {
        $this->get(route('about'))->assertOk()->assertSee('Tentang TopNews');
        $this->get(route('editorial.guidelines'))->assertOk()->assertSee('Pedoman & Standar Etika Redaksi');
        $this->get(route('privacy'))->assertOk()->assertSee('Kebijakan Privasi');
        $this->get(route('terms'))->assertOk()->assertSee('Syarat & Ketentuan Penggunaan');
        $this->get(route('disclaimer'))->assertOk()->assertSee('Sanggahan (Disclaimer)');
        $this->get(route('advertise'))->assertOk()->assertSee('Informasi Kerja Sama & Iklan');
    }

    public function test_draft_page_returns_404_for_public_visitor(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $page = Page::create([
            'title' => 'Halaman Draf Rahasia',
            'slug' => 'draf-rahasia',
            'content' => '<p>Konten rahasia belum terbit</p>',
            'status' => PageStatus::Draft,
            'created_by' => $admin->id,
        ]);

        $this->get(route('page.show', $page->slug))->assertNotFound();
    }

    public function test_admin_can_preview_draft_page_with_noindex(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $page = Page::create([
            'title' => 'Halaman Draf Redaksi',
            'slug' => 'draf-redaksi',
            'content' => '<p>Konten uji coba redaksi</p>',
            'status' => PageStatus::Draft,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.pages.preview', $page));

        $response->assertOk();
        $response->assertSee('Konten uji coba redaksi', false);
        $response->assertSee('noindex, nofollow', false);
    }

    public function test_core_protected_page_cannot_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $aboutPage = Page::where('page_type', PageType::About->value)->first();

        $this->assertTrue($aboutPage->isProtectedCorePage());

        $response = $this->actingAs($admin)->delete(route('admin.pages.destroy', $aboutPage));

        $response->assertForbidden();
        $this->assertDatabaseHas('pages', ['id' => $aboutPage->id]);
    }

    public function test_custom_page_can_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $page = Page::create([
            'title' => 'Halaman Promo Spesial',
            'slug' => 'promo-spesial',
            'content' => '<p>Promo terbatas</p>',
            'status' => PageStatus::Published,
            'created_by' => $admin->id,
        ]);

        $this->assertFalse($page->isProtectedCorePage());

        $response = $this->actingAs($admin)->delete(route('admin.pages.destroy', $page));

        $response->assertRedirect(route('admin.pages.index'));
        $this->assertSoftDeleted('pages', ['id' => $page->id]);
    }

    public function test_html_sanitizer_removes_scripts_from_page_content(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $maliciousHtml = '<p>Normal text</p><script>alert("XSS")</script><img src="x" onerror="alert(1)">';

        $this->actingAs($admin)->post(route('admin.pages.store'), [
            'title' => 'Halaman Aman dari XSS',
            'slug' => 'halaman-aman',
            'content' => $maliciousHtml,
            'status' => PageStatus::Published->value,
        ]);

        $saved = Page::where('slug', 'halaman-aman')->first();
        $this->assertNotNull($saved);
        $this->assertStringNotContainsString('<script>', $saved->content);
        $this->assertStringNotContainsString('onerror', $saved->content);
        $this->assertStringContainsString('Normal text', $saved->content);
    }
}
