<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Services\SettingsService;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(SettingsSeeder::class);
    }

    public function test_settings_service_retrieves_defaults(): void
    {
        $service = app(SettingsService::class);

        $this->assertSame('TopNews', $service->get('general.site_name'));
        $this->assertSame('topnews90@gmail.com', $service->get('contact.public_email'));
        $this->assertTrue($service->get('newsletter.enabled'));
        $this->assertSame(200, $service->get('editorial.default_reading_words_per_minute'));
    }

    public function test_settings_are_cached_and_invalidated_on_update(): void
    {
        $service = app(SettingsService::class);

        // Prime cache
        $this->assertSame('TopNews', $service->get('general.site_name'));
        $this->assertTrue(Cache::has(SettingsService::CACHE_KEY));

        // Update a setting
        $service->set('general', 'site_name', 'TopNews Global', 'string', true);

        // Cache was cleared and re-evaluated
        $this->assertSame('TopNews Global', $service->get('general.site_name'));
    }

    public function test_authorized_admin_can_update_general_settings(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->put(route('admin.settings.general.update'), [
                'site_name' => 'TopNews Media',
                'tagline' => 'Berita Terkini dan Terpercaya',
                'site_description' => 'Portal berita independen terlengkap di Indonesia.',
                'default_locale' => 'id',
                'timezone' => 'Asia/Jakarta',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSame('TopNews Media', app(SettingsService::class)->get('general.site_name'));
    }

    public function test_unauthorized_user_cannot_update_settings(): void
    {
        $user = User::factory()->create(['account_type' => 'reader']);

        $response = $this->actingAs($user)
            ->put(route('admin.settings.general.update'), [
                'site_name' => 'Hacked Site',
                'tagline' => 'Tagline',
                'site_description' => 'Description',
                'default_locale' => 'id',
                'timezone' => 'Asia/Jakarta',
            ]);

        $response->assertForbidden();
        $this->assertSame('TopNews', app(SettingsService::class)->get('general.site_name'));
    }

    public function test_settings_validation_rejects_invalid_data(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->put(route('admin.settings.contact.update'), [
                'public_email' => 'invalid-email-string',
                'office_address' => '',
            ]);

        $response->assertSessionHasErrors(['public_email', 'office_address']);
    }

    public function test_contact_location_and_email_rendered_publicly_without_public_admin_link(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        // Update contact email & location
        $this->actingAs($admin)->put(route('admin.settings.contact.update'), [
            'public_email' => 'topnews90@gmail.com',
            'office_address' => 'Bireuen, Aceh, Indonesia',
            'location' => 'Bireuen, Aceh, Indonesia',
        ])->assertRedirect();

        // 1. Verify Public Contact Page
        $contactRes = $this->get(route('contact'));
        $contactRes->assertOk();
        $contactRes->assertSee('topnews90@gmail.com');
        $contactRes->assertSee('mailto:topnews90@gmail.com', false);
        $contactRes->assertSee('Bireuen, Aceh, Indonesia');

        // 2. Verify Homepage & Footer
        $homeRes = $this->get(route('home'));
        $homeRes->assertOk();
        $homeRes->assertSee('mailto:topnews90@gmail.com', false);
        $homeRes->assertDontSee('Masuk Admin');
        $homeRes->assertDontSee('Admin Login');
        $homeRes->assertDontSee('Newsroom');

        // 3. Verify Admin Route remains functional
        $adminRes = $this->actingAs($admin)->get(route('admin.dashboard'));
        $adminRes->assertOk();
    }
}
