<?php

namespace Tests\Feature;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RbacAndUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_super_admin_can_access_users_management(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Pengguna & Staf');
    }

    public function test_admin_can_create_new_journalist(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $journalistRole = Role::where('name', 'journalist')->first();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Wartawan Baru',
            'username' => 'wartawan.baru',
            'email' => 'wartawan.baru@topnews.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $journalistRole->id,
            'status' => UserStatus::Active->value,
            'phone' => '08123456789',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'wartawan.baru@topnews.id',
            'username' => 'wartawan.baru',
        ]);
    }

    public function test_journalist_cannot_access_user_management(): void
    {
        $journalist = User::factory()->create();
        $journalist->assignRole('journalist');

        $response = $this->actingAs($journalist)->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    public function test_contributor_cannot_access_user_management(): void
    {
        $contributor = User::factory()->create();
        $contributor->assignRole('contributor');

        $response = $this->actingAs($contributor)->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    public function test_admin_cannot_suspend_super_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($admin)->post(route('admin.users.suspend', $superAdmin));

        $response->assertStatus(403);
        $this->assertEquals(UserStatus::Active, $superAdmin->fresh()->status);
    }

    public function test_admin_cannot_delete_super_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $superAdmin));

        $response->assertStatus(403);
        $this->assertNull($superAdmin->fresh()->deleted_at);
    }

    public function test_last_active_super_admin_cannot_be_suspended_or_deleted(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        // Attempting to suspend itself or last super admin
        $response = $this->actingAs($superAdmin)->post(route('admin.users.suspend', $superAdmin));
        $response->assertStatus(403);

        $deleteResponse = $this->actingAs($superAdmin)->delete(route('admin.users.destroy', $superAdmin));
        $deleteResponse->assertStatus(403);
    }

    public function test_user_cannot_suspend_themselves(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.users.suspend', $admin));

        $response->assertStatus(403);
    }

    public function test_user_can_update_own_profile_and_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $avatarFile = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Nama Baru',
            'email' => 'emailbaru@topnews.id',
            'phone' => '0899999999',
            'avatar' => $avatarFile,
        ]);

        $response->assertRedirect(route('profile.edit'));
        $user->refresh();

        $this->assertEquals('Nama Baru', $user->name);
        $this->assertEquals('emailbaru@topnews.id', $user->email);
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_user_can_change_password_with_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password-123'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'old-password-123',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
    }

    public function test_user_cannot_change_password_with_incorrect_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password-123'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'wrong-password',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_suspended_user_is_logged_out_by_active_middleware(): void
    {
        $user = User::factory()->suspended()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertRedirect(route('suspended'));
        $this->assertGuest();
    }

    public function test_super_admin_cli_provisioning_command_works(): void
    {
        $this->artisan('topnews:make-super-admin', [
            '--name' => 'Chief Technology Officer',
            '--username' => 'cto.topnews',
            '--email' => 'cto@topnews.id',
            '--password' => 'MasterPass123!',
        ])->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'email' => 'cto@topnews.id',
            'username' => 'cto.topnews',
            'status' => UserStatus::Active->value,
        ]);

        $user = User::where('email', 'cto@topnews.id')->first();
        $this->assertTrue($user->isSuperAdmin());
    }
}
