<?php

namespace Tests\Feature;

use App\Enums\ContactMessageStatus;
use App\Models\ContactMessage;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(SettingsSeeder::class);
    }

    public function test_reader_can_submit_contact_message(): void
    {
        $response = $this->post(route('contact.submit'), [
            'name' => 'Ahmad Pembaca',
            'email' => 'ahmad@example.com',
            'category' => 'editorial',
            'subject' => 'Konfirmasi Informasi Berita Terkini',
            'message' => 'Halo redaksi, kami ingin menyampaikan klarifikasi mengenai peristiwa tadi pagi.',
        ]);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Ahmad Pembaca',
            'email' => 'ahmad@example.com',
            'category' => 'editorial',
            'subject' => 'Konfirmasi Informasi Berita Terkini',
            'status' => ContactMessageStatus::New->value,
        ]);
    }

    public function test_contact_form_validation_rejects_empty_fields(): void
    {
        $response = $this->post(route('contact.submit'), [
            'name' => '',
            'email' => 'not-an-email',
            'subject' => '',
            'message' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    }

    public function test_guest_cannot_access_admin_contact_inbox(): void
    {
        $response = $this->get(route('admin.contacts.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authorized_staff_can_view_and_update_contact_status(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $message = ContactMessage::create([
            'name' => 'Narasumber Publik',
            'email' => 'narasumber@example.com',
            'subject' => 'Klarifikasi Fakta',
            'message' => 'Naskah klarifikasi resmi dari instansi kami.',
            'status' => ContactMessageStatus::New,
        ]);

        // Viewing detail marks as Read
        $this->actingAs($admin)->get(route('admin.contacts.show', $message))->assertOk();

        $message->refresh();
        $this->assertSame(ContactMessageStatus::Read, $message->status);
        $this->assertNotNull($message->read_at);

        // Update status to In Progress
        $this->actingAs($admin)->put(route('admin.contacts.update-status', $message), [
            'status' => ContactMessageStatus::InProgress->value,
        ])->assertRedirect();

        $message->refresh();
        $this->assertSame(ContactMessageStatus::InProgress, $message->status);

        // Assign to staff
        $this->actingAs($admin)->post(route('admin.contacts.assign', $message), [
            'assigned_to' => $admin->id,
        ])->assertRedirect();

        $message->refresh();
        $this->assertSame($admin->id, $message->assigned_to);
    }
}
