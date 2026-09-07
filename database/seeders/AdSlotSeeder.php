<?php

namespace Database\Seeders;

use App\Enums\AdDeviceScope;
use App\Models\AdSlot;
use Illuminate\Database\Seeder;

class AdSlotSeeder extends Seeder
{
    public function run(): void
    {
        $slots = [
            [
                'key' => 'home_leaderboard',
                'name' => 'Homepage Leaderboard (Bawah Hero)',
                'description' => 'Slot iklan horizontal utama yang tampil di bawah hero section dan berita utama.',
                'placement' => 'homepage',
                'device_scope' => AdDeviceScope::All->value,
                'width' => 970,
                'height' => 90,
                'is_active' => true,
            ],
            [
                'key' => 'home_inline_1',
                'name' => 'Homepage Inline (Antar Bagian)',
                'description' => 'Slot iklan antara berita terbaru dan rubrik tematik.',
                'placement' => 'homepage',
                'device_scope' => AdDeviceScope::All->value,
                'width' => 728,
                'height' => 90,
                'is_active' => true,
            ],
            [
                'key' => 'article_top',
                'name' => 'Atas Naskah Berita (Article Top)',
                'description' => 'Slot iklan di atas judul atau naskah pembuka berita.',
                'placement' => 'article',
                'device_scope' => AdDeviceScope::All->value,
                'width' => 728,
                'height' => 90,
                'is_active' => true,
            ],
            [
                'key' => 'article_inline_1',
                'name' => 'Tengah Paragraf Berita (Article Inline)',
                'description' => 'Slot iklan terukur di antara paragraf narasi berita (disisipkan aman).',
                'placement' => 'article',
                'device_scope' => AdDeviceScope::All->value,
                'width' => 728,
                'height' => 90,
                'is_active' => true,
            ],
            [
                'key' => 'article_bottom',
                'name' => 'Bawah Naskah Berita (Article Bottom)',
                'description' => 'Slot iklan di akhir tulisan sebelum kolom komentar pembaca.',
                'placement' => 'article',
                'device_scope' => AdDeviceScope::All->value,
                'width' => 728,
                'height' => 90,
                'is_active' => true,
            ],
            [
                'key' => 'sidebar',
                'name' => 'Sidebar Berita (Medium Rectangle)',
                'description' => 'Slot iklan kotak 300x250 pada sidebar homepage & halaman arsip.',
                'placement' => 'sidebar',
                'device_scope' => AdDeviceScope::Desktop->value,
                'width' => 300,
                'height' => 250,
                'is_active' => true,
            ],
            [
                'key' => 'mobile_banner',
                'name' => 'Mobile Top Banner',
                'description' => 'Banner kompak khusus perangkat smartphone / tablet.',
                'placement' => 'homepage',
                'device_scope' => AdDeviceScope::Mobile->value,
                'width' => 320,
                'height' => 50,
                'is_active' => true,
            ],
        ];

        foreach ($slots as $slot) {
            AdSlot::updateOrCreate(['key' => $slot['key']], $slot);
        }
    }
}
