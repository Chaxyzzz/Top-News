<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Pemilu', 'KPK', 'IKN Nusantara', 'Kecerdasan Buatan', 'Bank Indonesia',
            'IHSG', 'Timnas Indonesia', 'Piala Dunia', 'Kesehatan', 'Transformasi Digital',
            'Iklim & Lingkungan', 'Hukum', 'Infrastruktur', 'Investasi', 'Startup',
        ];

        foreach ($tags as $tagName) {
            Tag::firstOrCreate(
                ['name' => $tagName],
                ['slug' => Str::slug($tagName)]
            );
        }
    }
}
