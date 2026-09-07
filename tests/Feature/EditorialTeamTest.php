<?php

namespace Tests\Feature;

use App\Enums\UserStatus;
use App\Models\User;
use Database\Seeders\PageSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditorialTeamTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(SettingsSeeder::class);
        $this->seed(PageSeeder::class);
    }

    public function test_editorial_page_displays_selected_staff_in_order(): void
    {
        $pemred = User::factory()->create([
            'name' => 'Zakky Mubaraq',
            'email' => 'zakky.confidential@topnews.id',
            'phone' => '+6281199998888',
            'public_title' => 'Pemimpin Redaksi',
            'bio' => 'Jurnalis senior dengan 15 tahun pengalaman peliputan investigasi.',
            'show_on_editorial_team' => true,
            'editorial_team_order' => 1,
            'status' => UserStatus::Active,
            'account_type' => 'staff',
        ]);

        $redaktur = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi.confidential@topnews.id',
            'public_title' => 'Redaktur Pelaksana',
            'bio' => 'Mengawasi rubrik politik dan ekonomi harian.',
            'show_on_editorial_team' => true,
            'editorial_team_order' => 2,
            'status' => UserStatus::Active,
            'account_type' => 'staff',
        ]);

        $hiddenStaff = User::factory()->create([
            'name' => 'Staff Internal Rahasia',
            'email' => 'secret@topnews.id',
            'show_on_editorial_team' => false,
            'status' => UserStatus::Active,
            'account_type' => 'staff',
        ]);

        $response = $this->get(route('editorial.team'));

        $response->assertOk();
        $response->assertSee('Zakky Mubaraq');
        $response->assertSee('Pemimpin Redaksi');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Redaktur Pelaksana');

        // Verify hidden staff is not displayed
        $response->assertDontSee('Staff Internal Rahasia');

        // Verify private data is NOT exposed
        $response->assertDontSee('zakky.confidential@topnews.id');
        $response->assertDontSee('+6281199998888');
    }

    public function test_authorized_editor_can_update_editorial_team_order_and_titles(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $staff = User::factory()->create([
            'name' => 'Dian Pratama',
            'status' => UserStatus::Active,
            'account_type' => 'staff',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.editorial-team.update'), [
            'members' => [
                [
                    'id' => $staff->id,
                    'public_title' => 'Redaktur Berita Nasional',
                    'bio' => 'Fokus pada hukum dan tata kelola pemerintahan.',
                    'show_on_editorial_team' => 1,
                    'editorial_team_order' => 5,
                ],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $staff->refresh();
        $this->assertTrue($staff->show_on_editorial_team);
        $this->assertSame('Redaktur Berita Nasional', $staff->public_title);
        $this->assertSame(5, $staff->editorial_team_order);
    }
}
