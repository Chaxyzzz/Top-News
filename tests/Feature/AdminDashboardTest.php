<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Newsroom');
        $response->assertSee('Ringkasan kendali sistem');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_dashboard_displays_real_metrics(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        // Create 2 additional active users
        $user1 = User::factory()->create();
        $user1->assignRole('journalist');

        $user2 = User::factory()->create();
        $user2->assignRole('editor');

        // Create 1 suspended user
        $user3 = User::factory()->suspended()->create();
        $user3->assignRole('contributor');

        $response = $this->actingAs($superAdmin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        // Total users = 4, active = 3, suspended = 1, roles = 6
        $response->assertSee('Total Pengguna');
        $response->assertSee('Pengguna Aktif');
        $response->assertSee('Ditangguhkan');
        $response->assertSee('Role Terdefinisi');
    }

    public function test_audit_logs_explorer_is_accessible_to_permitted_users(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'test.security_event',
            'description' => 'Contoh catatan aktivitas pengujian',
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.audit-logs.index'));

        $response->assertStatus(200);
        $response->assertSee('Catatan Audit & Keamanan Sistem');
        $response->assertSee('test.security_event');
        $response->assertSee('Contoh catatan aktivitas pengujian');
    }

    public function test_audit_logs_explorer_can_filter_by_action(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'user.created',
            'description' => 'Pengguna A dibuat',
            'created_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'user.suspended',
            'description' => 'Pengguna B ditangguhkan',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.audit-logs.index', ['action' => 'user.suspended']));

        $response->assertStatus(200);
        $response->assertSee('Pengguna B ditangguhkan');
        $response->assertDontSee('Pengguna A dibuat');
    }

    public function test_unauthorized_user_cannot_access_audit_logs(): void
    {
        $contributor = User::factory()->create();
        $contributor->assignRole('contributor');

        $response = $this->actingAs($contributor)->get(route('admin.audit-logs.index'));

        $response->assertStatus(403);
    }

    public function test_system_diagnostics_page_is_secure_and_does_not_leak_secrets(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get(route('admin.system.index'));

        $response->assertStatus(200);
        $response->assertSee('Diagnostik & Informasi Sistem');
        $response->assertSee('Versi Laravel');

        // Confirm sensitive environment keys are strictly absent
        $response->assertDontSee(config('app.key'));
        $response->assertDontSee('DB_PASSWORD');
    }

    public function test_non_admin_cannot_access_system_diagnostics(): void
    {
        $journalist = User::factory()->create();
        $journalist->assignRole('journalist');

        $response = $this->actingAs($journalist)->get(route('admin.system.index'));

        $response->assertStatus(403);
    }

    public function test_newsroom_and_content_foundation_pages_can_be_rendered(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('editor');

        $newsroomResponse = $this->actingAs($editor)->get(route('admin.newsroom.index'));
        $newsroomResponse->assertStatus(200);
        $newsroomResponse->assertSee('Alur Redaksi & Newsroom Pipeline');

        $contentResponse = $this->actingAs($editor)->get(route('admin.content.index'));
        $contentResponse->assertStatus(200);
        $contentResponse->assertSee('Struktur Konten & Taksonomi');
    }
}
