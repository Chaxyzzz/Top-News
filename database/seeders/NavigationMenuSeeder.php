<?php

namespace Database\Seeders;

use App\Enums\MenuLinkType;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class NavigationMenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Primary Menu
        $primaryMenu = Menu::firstOrCreate(
            ['key' => 'primary'],
            ['name' => 'Navigasi Utama (Header)']
        );

        // Delete existing items to re-seed cleanly
        $primaryMenu->items()->delete();

        $primaryItems = [
            ['label' => 'Beranda', 'link_type' => MenuLinkType::Route, 'route_name' => 'home', 'sort_order' => 1],
            ['label' => 'Terbaru', 'link_type' => MenuLinkType::Route, 'route_name' => 'latest', 'sort_order' => 2],
        ];

        $order = 3;
        $categories = Category::active()->orderBy('navigation_order')->orderBy('sort_order')->limit(8)->get();
        foreach ($categories as $cat) {
            $primaryItems[] = [
                'label' => $cat->name,
                'link_type' => MenuLinkType::Category,
                'category_id' => $cat->id,
                'sort_order' => $order++,
            ];
        }

        $primaryItems[] = ['label' => 'Opini', 'link_type' => MenuLinkType::Route, 'route_name' => 'opinion.index', 'sort_order' => $order++];
        $primaryItems[] = ['label' => 'Video', 'link_type' => MenuLinkType::Route, 'route_name' => 'video.index', 'sort_order' => $order++];
        $primaryItems[] = ['label' => 'Foto Cerita', 'link_type' => MenuLinkType::Route, 'route_name' => 'photo-story.index', 'sort_order' => $order++];

        foreach ($primaryItems as $item) {
            MenuItem::create(array_merge($item, [
                'menu_id' => $primaryMenu->id,
                'is_active' => true,
            ]));
        }

        // 2. Footer Menu
        $footerMenu = Menu::firstOrCreate(
            ['key' => 'footer'],
            ['name' => 'Navigasi Kaki (Footer)']
        );

        $footerMenu->items()->delete();

        $footerItems = [];
        $fOrder = 1;
        foreach ($categories as $cat) {
            $footerItems[] = [
                'label' => $cat->name,
                'link_type' => MenuLinkType::Category,
                'category_id' => $cat->id,
                'sort_order' => $fOrder++,
            ];
        }
        $footerItems[] = ['label' => 'Opini & Kolom', 'link_type' => MenuLinkType::Route, 'route_name' => 'opinion.index', 'sort_order' => $fOrder++];
        $footerItems[] = ['label' => 'Sedang Tren', 'link_type' => MenuLinkType::Route, 'route_name' => 'trending.index', 'sort_order' => $fOrder++];
        $footerItems[] = ['label' => 'Tentang TopNews', 'link_type' => MenuLinkType::Route, 'route_name' => 'about', 'sort_order' => $fOrder++];
        $footerItems[] = ['label' => 'Kontak Redaksi & Iklan', 'link_type' => MenuLinkType::Route, 'route_name' => 'contact', 'sort_order' => $fOrder++];

        foreach ($footerItems as $fItem) {
            MenuItem::create(array_merge($fItem, [
                'menu_id' => $footerMenu->id,
                'is_active' => true,
            ]));
        }
    }
}
