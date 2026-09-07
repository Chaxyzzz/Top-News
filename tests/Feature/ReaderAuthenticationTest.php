<?php

namespace Tests\Feature;

use App\Enums\UserStatus;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReaderAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_reader_can_view_registration_form(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Pembaca');
    }

    public function test_reader_can_register_with_valid_details(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Budi Santoso',
            'username' => 'budireader',
            'email' => 'budi@example.com',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('account.bookmarks'));
        $this->assertAuthenticated();

        $user = User::where('email', 'budi@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('reader', $user->account_type);
        $this->assertTrue($user->isReader());
        $this->assertFalse($user->isStaff());
        $this->assertTrue($user->hasRole('reader'));
    }

    public function test_reader_login_redirects_away_from_admin_dashboard(): void
    {
        $reader = User::factory()->create([
            'email' => 'reader@example.com',
            'password' => Hash::make('password123'),
            'status' => UserStatus::Active,
            'account_type' => 'reader',
        ]);
        $reader->assignRole('reader');

        $response = $this->post(route('login'), [
            'login' => 'reader@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('account.bookmarks'));
    }

    public function test_staff_login_redirects_to_admin_dashboard(): void
    {
        $staff = User::factory()->create([
            'email' => 'editor@topnews.id',
            'password' => Hash::make('password123'),
            'status' => UserStatus::Active,
            'account_type' => 'staff',
        ]);
        $staff->assignRole('editor');

        $response = $this->post(route('login'), [
            'login' => 'editor@topnews.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_reader_is_forbidden_from_admin_area(): void
    {
        $reader = User::factory()->create([
            'status' => UserStatus::Active,
            'account_type' => 'reader',
        ]);
        $reader->assignRole('reader');

        $response = $this->actingAs($reader)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }
}
