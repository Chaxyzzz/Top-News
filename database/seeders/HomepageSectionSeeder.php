<?php

namespace Database\Seeders;

use App\Enums\HomepageLayoutVariant;
use App\Enums\HomepageSectionType;
use App\Enums\HomepageSourceType;
use App\Models\HomepageSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HomepageSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'key' => 'hero',
                'title' => 'Berita Utama (Hero)',
                'subtitle' => 'Lead story editorial dan sorotan utama peristiwa penting hari ini.',
                'section_type' => HomepageSectionType::Hero->value,
                'source_type' => HomepageSourceType::System->value,
                'layout_variant' => HomepageLayoutVariant::HeroPrimary->value,
                'item_limit' => 5,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'key' => 'latest',
                'title' => 'Berita Terbaru',
                'subtitle' => 'Informasi aktual terverifikasi sepanjang hari.',
                'section_type' => HomepageSectionType::Latest->value,
                'source_type' => HomepageSourceType::Automatic->value,
                'layout_variant' => HomepageLayoutVariant::Grid4->value,
                'item_limit' => 8,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'key' => 'trending',
                'title' => 'Sedang Tren',
                'subtitle' => 'Topik yang tengah ramai diperbincangkan publik.',
                'section_type' => HomepageSectionType::Trending->value,
                'source_type' => HomepageSourceType::Automatic->value,
                'layout_variant' => HomepageLayoutVariant::NumberedList->value,
                'item_limit' => 5,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'key' => 'popular',
                'title' => 'Paling Banyak Dibaca',
                'subtitle' => 'Berita terpopuler dalam sepekan terakhir.',
                'section_type' => HomepageSectionType::Popular->value,
                'source_type' => HomepageSourceType::Automatic->value,
                'layout_variant' => HomepageLayoutVariant::HorizontalList->value,
                'item_limit' => 5,
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'key' => 'category_highlights',
                'title' => 'Sorotan Rubrik Berita',
                'subtitle' => 'Berita pilihan berdasarkan rubrik dan kategori terfavorit.',
                'section_type' => HomepageSectionType::CategoryHighlight->value,
                'source_type' => HomepageSourceType::Category->value,
                'layout_variant' => HomepageLayoutVariant::Grid4->value,
                'item_limit' => 4,
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'key' => 'opinion',
                'title' => 'Opini & Kolom Redaksi',
                'subtitle' => 'Sudut pandang independen dari para pakar dan jurnalis senior.',
                'section_type' => HomepageSectionType::Opinion->value,
                'source_type' => HomepageSourceType::Automatic->value,
                'layout_variant' => HomepageLayoutVariant::OpinionCards->value,
                'item_limit' => 3,
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'key' => 'editors_choice',
                'title' => 'Pilihan Editor',
                'subtitle' => 'Laporan mendalam dan liputan khusus pilihan redaksi TopNews.',
                'section_type' => HomepageSectionType::EditorsChoice->value,
                'source_type' => HomepageSourceType::System->value,
                'layout_variant' => HomepageLayoutVariant::Grid3->value,
                'item_limit' => 3,
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'key' => 'video',
                'title' => 'Berita Video Terkini',
                'subtitle' => 'Liputan visual peristiwa penting dan dokumenter singkat.',
                'section_type' => HomepageSectionType::Video->value,
                'source_type' => HomepageSourceType::Automatic->value,
                'layout_variant' => HomepageLayoutVariant::MediaGrid->value,
                'item_limit' => 4,
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'key' => 'photo_story',
                'title' => 'Galeri Foto Cerita',
                'subtitle' => 'Rekaman visual jurnalistik bercerita dari berbagai penjuru.',
                'section_type' => HomepageSectionType::PhotoStory->value,
                'source_type' => HomepageSourceType::Automatic->value,
                'layout_variant' => HomepageLayoutVariant::MediaGrid->value,
                'item_limit' => 4,
                'sort_order' => 9,
                'is_active' => true,
            ],
        ];

        foreach ($sections as $sec) {
            HomepageSection::updateOrCreate(
                ['key' => $sec['key']],
                array_merge($sec, ['uuid' => (string) Str::uuid()])
            );
        }
    }
}
