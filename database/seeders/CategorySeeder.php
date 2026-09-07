<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Nasional', 'slug' => 'nasional', 'accent_color' => '#E50914', 'sort_order' => 1, 'description' => 'Berita peristiwa, hukum, dan kebijakan nasional terkini di seluruh Indonesia.'],
            ['name' => 'Politik', 'slug' => 'politik', 'accent_color' => '#1E3A8A', 'sort_order' => 2, 'description' => 'Kabar dinamika politik pemerintahan, parlemen, dan kepemimpinan.'],
            ['name' => 'Ekonomi & Bisnis', 'slug' => 'ekonomi-bisnis', 'accent_color' => '#047857', 'sort_order' => 3, 'description' => 'Pasar modal, perbankan, makroekonomi, dan dunia usaha terpercaya.'],
            ['name' => 'Teknologi', 'slug' => 'teknologi', 'accent_color' => '#6D28D9', 'sort_order' => 4, 'description' => 'Inovasi kecerdasan buatan, gawai, startup, dan transformasi digital.'],
            ['name' => 'Internasional', 'slug' => 'internasional', 'accent_color' => '#B45309', 'sort_order' => 5, 'description' => 'Dinamika geopolitik global, diplomasi, dan peristiwa mancanegara.'],
            ['name' => 'Olahraga', 'slug' => 'olahraga', 'accent_color' => '#DC2626', 'sort_order' => 6, 'description' => 'Sepak bola, bulu tangkis, balap motor, dan olahraga dunia.'],
            ['name' => 'Hiburan', 'slug' => 'hiburan', 'accent_color' => '#DB2777', 'sort_order' => 7, 'description' => 'Sinema, musik, budaya pop, dan dunia seni kreatif.'],
            ['name' => 'Gaya Hidup', 'slug' => 'gaya-hidup', 'accent_color' => '#0891B2', 'sort_order' => 8, 'description' => 'Kesehatan, kuliner, pariwisata, dan tren kehidupan modern.'],
            ['name' => 'Opini', 'slug' => 'opini', 'accent_color' => '#374151', 'sort_order' => 9, 'description' => 'Kolom pemikiran, tajuk rencana, dan perspektif kritis para pakar.'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'accent_color' => $cat['accent_color'],
                    'sort_order' => $cat['sort_order'],
                    'description' => $cat['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
