<?php

namespace Tests\Feature;

use App\Enums\AdCampaignStatus;
use App\Enums\AdDeviceScope;
use App\Models\AdCampaign;
use App\Models\AdSlot;
use App\Models\Advertisement;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvertisementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $reader;

    protected AdSlot $slot;

    protected AdCampaign $campaign;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->reader = User::factory()->create();
        $this->reader->assignRole('reader');

        $this->slot = AdSlot::create([
            'key' => 'home_leaderboard',
            'name' => 'Leaderboard Test',
            'placement' => 'homepage',
            'device_scope' => AdDeviceScope::All,
            'width' => 970,
            'height' => 90,
            'is_active' => true,
        ]);

        $this->campaign = AdCampaign::create([
            'name' => 'Kampanye Bank Mandiri',
            'advertiser' => 'Bank Mandiri',
            'status' => AdCampaignStatus::Active,
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_admin_can_create_campaign(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.advertising.campaigns.store'), [
            'name' => 'Kampanye Telkomsel 5G',
            'advertiser' => 'Telkomsel',
            'status' => AdCampaignStatus::Active->value,
            'budget_note' => 'Paket Kuartal 1',
        ]);

        $response->assertRedirect(route('admin.advertising.campaigns.index'));
        $this->assertDatabaseHas('ad_campaigns', [
            'name' => 'Kampanye Telkomsel 5G',
            'advertiser' => 'Telkomsel',
        ]);
    }

    public function test_admin_can_create_advertisement(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.advertising.ads.store'), [
            'campaign_id' => $this->campaign->id,
            'ad_slot_id' => $this->slot->id,
            'name' => 'Banner Livin Mandiri',
            'headline' => 'Buka Rekening Mudah Lewat Livin',
            'destination_url' => 'https://bankmandiri.co.id/livin',
            'is_active' => '1',
            'priority' => 10,
        ]);

        $response->assertRedirect(route('admin.advertising.ads.index'));
        $this->assertDatabaseHas('advertisements', [
            'name' => 'Banner Livin Mandiri',
            'destination_url' => 'https://bankmandiri.co.id/livin',
            'priority' => 10,
        ]);
    }

    public function test_public_homepage_renders_eligible_ad_with_sponsored_attribute(): void
    {
        $ad = Advertisement::create([
            'campaign_id' => $this->campaign->id,
            'ad_slot_id' => $this->slot->id,
            'name' => 'Promo Diskon Tiket',
            'headline' => 'Diskon Penerbangan Liburan 50%',
            'destination_url' => 'https://garuda-indonesia.com/promo',
            'is_active' => true,
            'priority' => 10,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Diskon Penerbangan Liburan 50%');
        $response->assertSee('Iklan / Promosi');
        $response->assertSee('rel="sponsored noopener noreferrer"', false);
    }

    public function test_clicking_ad_increments_clicks_and_redirects_safely_without_open_redirect(): void
    {
        $ad = Advertisement::create([
            'campaign_id' => $this->campaign->id,
            'ad_slot_id' => $this->slot->id,
            'name' => 'Iklan Aman',
            'destination_url' => 'https://resmi.example.com/tujuan',
            'is_active' => true,
            'clicks_count' => 0,
        ]);

        // Attempt open redirect attempt with arbitrary ?url= query string
        $response = $this->get(route('ads.click', ['advertisement' => $ad->uuid, 'url' => 'https://attacker.evil.com']));

        // Must redirect to stored destination, NOT attacker URL
        $response->assertRedirect('https://resmi.example.com/tujuan');
        $this->assertEquals(1, $ad->fresh()->clicks_count);
    }

    public function test_impression_beacon_increments_impressions_count(): void
    {
        $ad = Advertisement::create([
            'campaign_id' => $this->campaign->id,
            'ad_slot_id' => $this->slot->id,
            'name' => 'Iklan Impresi',
            'destination_url' => 'https://example.com',
            'is_active' => true,
            'impressions_count' => 0,
        ]);

        $response = $this->post(route('ads.impression', $ad->uuid));

        $response->assertNoContent();
        $this->assertEquals(1, $ad->fresh()->impressions_count);
    }

    public function test_inactive_or_paused_ad_is_not_rendered_publicly(): void
    {
        // Inactive ad
        Advertisement::create([
            'campaign_id' => $this->campaign->id,
            'ad_slot_id' => $this->slot->id,
            'name' => 'Iklan Mati',
            'headline' => 'Teks Iklan Mati Yang Tidak Boleh Muncul',
            'destination_url' => 'https://example.com',
            'is_active' => false,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('Teks Iklan Mati Yang Tidak Boleh Muncul');
    }
}
