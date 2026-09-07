<?php

namespace Tests\Feature;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Akses Newsroom');
    }

    public function test_zakky77_user_can_authenticate_with_username_and_password(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['label' => 'Super Admin', 'is_system' => true]);
        $user = User::updateOrCreate(['username' => 'Zakky77'], [
            'uuid' => (string) Str::uuid(),
            'name' => 'Zakky Mubaraq',
            'email' => 'topnews90@gmail.com',
            'password' => 'Mikaliso77',
            'account_type' => 'staff',
            'status' => UserStatus::Active,
        ]);
        $user->roles()->sync([$role->id]);

        $response = $this->post('/login', [
            'login' => 'Zakky77',
            'password' => 'Mikaliso77',
            'remember' => '1',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_remember_me_checkbox_accepts_browser_on_string(): void
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('secret12345'),
            'status' => UserStatus::Active,
        ]);

        $response = $this->post('/login', [
            'login' => 'testuser',
            'password' => 'secret12345',
            'remember' => 'on',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertSessionHasNoErrors();
    }

    public function test_remember_me_unchecked_authenticates(): void
    {
        $user = User::factory()->create([
            'username' => 'testuser2',
            'password' => Hash::make('secret12345'),
            'status' => UserStatus::Active,
        ]);

        $response = $this->post('/login', [
            'login' => 'testuser2',
            'password' => 'secret12345',
            'remember' => '0',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertSessionHasNoErrors();
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'editor@topnews.id',
            'password' => Hash::make('secret12345'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'login' => 'editor@topnews.id',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login');
    }

    public function test_inactive_users_cannot_authenticate(): void
    {
        $user = User::factory()->inactive()->create([
            'email' => 'inactive@topnews.id',
            'password' => Hash::make('secret12345'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'login' => 'inactive@topnews.id',
            'password' => 'secret12345',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login');
    }

    public function test_suspended_users_cannot_authenticate(): void
    {
        $user = User::factory()->suspended()->create([
            'email' => 'suspended@topnews.id',
            'password' => Hash::make('secret12345'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'login' => 'suspended@topnews.id',
            'password' => 'secret12345',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('home'));
    }

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Pemulihan Kata Sandi');
    }

    public function test_suspended_notice_page_can_be_rendered(): void
    {
        $response = $this->get('/suspended');

        $response->assertStatus(200);
        $response->assertSee('Akun Anda Ditangguhkan');
    }
}
