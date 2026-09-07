<?php

namespace Tests\Feature;

use App\Enums\MenuLinkType;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationCmsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $reader;

    protected Menu $primaryMenu;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->reader = User::factory()->create();
        $this->reader->assignRole('reader');

        $this->primaryMenu = Menu::create([
            'key' => 'primary',
            'name' => 'Navigasi Utama',
        ]);
    }

    public function test_admin_can_view_navigation_management(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.navigation.index'));

        $response->assertOk();
        $response->assertSee('Navigasi Menu');
    }

    public function test_admin_can_add_category_item_to_menu(): void
    {
        $category = Category::factory()->create(['name' => 'Investigasi Khusus']);

        $response = $this->actingAs($this->admin)->post(route('admin.navigation.items.store'), [
            'menu_id' => $this->primaryMenu->id,
            'label' => 'Investigasi',
            'link_type' => MenuLinkType::Category->value,
            'category_id' => $category->id,
            'is_active' => '1',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('menu_items', [
            'menu_id' => $this->primaryMenu->id,
            'label' => 'Investigasi',
            'category_id' => $category->id,
        ]);
    }

    public function test_adding_route_item_validates_whitelisted_routes(): void
    {
        // Valid route
        $validRes = $this->actingAs($this->admin)->post(route('admin.navigation.items.store'), [
            'menu_id' => $this->primaryMenu->id,
            'label' => 'Opini Redaksi',
            'link_type' => MenuLinkType::Route->value,
            'route_name' => 'opinion.index',
            'is_active' => '1',
        ]);
        $validRes->assertSessionHas('success');

        // Invalid arbitrary route
        $invalidRes = $this->actingAs($this->admin)->post(route('admin.navigation.items.store'), [
            'menu_id' => $this->primaryMenu->id,
            'label' => 'Halaman Ilegal',
            'link_type' => MenuLinkType::Route->value,
            'route_name' => 'arbitrary.nonexistent.route',
            'is_active' => '1',
        ]);
        $invalidRes->assertSessionHasErrors(['route_name']);
    }

    public function test_admin_can_reorder_menu_items(): void
    {
        $item1 = MenuItem::create([
            'menu_id' => $this->primaryMenu->id,
            'label' => 'Item 1',
            'link_type' => MenuLinkType::Route,
            'route_name' => 'home',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $item2 = MenuItem::create([
            'menu_id' => $this->primaryMenu->id,
            'label' => 'Item 2',
            'link_type' => MenuLinkType::Route,
            'route_name' => 'latest',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.navigation.reorder', $this->primaryMenu), [
            'order' => [
                $item1->id => 10,
                $item2->id => 1,
            ],
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(10, $item1->fresh()->sort_order);
        $this->assertEquals(1, $item2->fresh()->sort_order);
    }

    public function test_admin_can_delete_menu_item(): void
    {
        $item = MenuItem::create([
            'menu_id' => $this->primaryMenu->id,
            'label' => 'Item Dihapus',
            'link_type' => MenuLinkType::Route,
            'route_name' => 'home',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.navigation.items.destroy', $item));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('menu_items', ['id' => $item->id]);
    }

    public function test_public_header_renders_configured_navigation_items(): void
    {
        MenuItem::create([
            'menu_id' => $this->primaryMenu->id,
            'label' => 'Kanal Riset',
            'link_type' => MenuLinkType::Route,
            'route_name' => 'about',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Kanal Riset');
    }
}
