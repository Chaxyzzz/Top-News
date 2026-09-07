<?php

namespace Tests\Feature;

use App\Enums\NewsletterSubscriberStatus;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(SettingsSeeder::class);
    }

    public function test_reader_can_subscribe_to_newsletter(): void
    {
        $response = $this->post(route('newsletter.subscribe'), [
            'email' => 'pembaca@example.com',
            'name' => 'Budi Pembaca',
            'source' => 'article',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'pembaca@example.com',
            'name' => 'Budi Pembaca',
            'status' => NewsletterSubscriberStatus::Pending->value,
            'source' => 'article',
        ]);
    }

    public function test_duplicate_active_subscription_returns_friendly_message(): void
    {
        NewsletterSubscriber::create([
            'email' => 'aktif@example.com',
            'name' => 'User Aktif',
            'status' => NewsletterSubscriberStatus::Active,
        ]);

        $response = $this->post(route('newsletter.subscribe'), [
            'email' => 'aktif@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Email Anda telah terdaftar sebagai pelanggan buletin TopNews.');
        $this->assertSame(1, NewsletterSubscriber::where('email', 'aktif@example.com')->count());
    }

    public function test_double_opt_in_verification_activates_subscriber(): void
    {
        $plainToken = 'secret-verification-token-12345';
        $tokenHash = hash('sha256', $plainToken);

        $subscriber = NewsletterSubscriber::create([
            'email' => 'verif@example.com',
            'status' => NewsletterSubscriberStatus::Pending,
            'verification_token_hash' => $tokenHash,
        ]);

        $response = $this->get(route('newsletter.verify', $plainToken));

        $response->assertOk();
        $response->assertSee('Verifikasi Berhasil!');

        $subscriber->refresh();
        $this->assertSame(NewsletterSubscriberStatus::Active, $subscriber->status);
        $this->assertNotNull($subscriber->verified_at);
        $this->assertNull($subscriber->verification_token_hash);
    }

    public function test_subscriber_can_unsubscribe_with_uuid(): void
    {
        $subscriber = NewsletterSubscriber::create([
            'email' => 'berhenti@example.com',
            'status' => NewsletterSubscriberStatus::Active,
        ]);

        $response = $this->get(route('newsletter.unsubscribe.show', $subscriber->uuid));
        $response->assertOk();
        $response->assertSee('berhenti@example.com');

        $postResponse = $this->post(route('newsletter.unsubscribe', $subscriber->uuid));
        $postResponse->assertRedirect(route('home'));

        $subscriber->refresh();
        $this->assertSame(NewsletterSubscriberStatus::Unsubscribed, $subscriber->status);
        $this->assertNotNull($subscriber->unsubscribed_at);
    }

    public function test_csv_export_mitigates_formula_injection(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Create subscriber with formula prefix
        NewsletterSubscriber::create([
            'email' => '=cmd|"/C calc"!A0@example.com',
            'name' => '+ExploitName',
            'status' => NewsletterSubscriberStatus::Active,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.newsletter.export'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->getContent();

        // Formula characters =, + must be safely prepended with a single quote (')
        $this->assertStringContainsString("'=cmd", $content);
        $this->assertStringContainsString("'+ExploitName", $content);
    }
}
