<?php

namespace Database\Seeders;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Enums\UserStatus;
use App\Models\Article;
use App\Models\ArticleDailyStat;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::whereHas('roles', fn ($q) => $q->where('name', 'super_admin'))->first() ?? User::first();
        if (! $superAdmin) {
            $superAdmin = User::create([
                'name' => 'Redaksi TopNews',
                'username' => 'redaksi',
                'email' => 'redaksi@topnews.id',
                'password' => bcrypt('TopNews2026!'),
                'account_type' => 'staff',
                'status' => UserStatus::Active,
            ]);
            $superAdmin->assignRole('super_admin');
        }

        $editor = User::whereHas('roles', fn ($q) => $q->where('name', 'editor'))->first() ?? $superAdmin;
        $journalist = User::whereHas('roles', fn ($q) => $q->where('name', 'journalist'))->first() ?? $superAdmin;

        $categories = Category::all();
        $tags = Tag::all();

        if ($categories->isEmpty()) {
            return;
        }

        $techCat = Category::where('slug', 'teknologi')->first() ?? $categories->first();
        $economyCat = Category::where('slug', 'ekonomi')->first() ?? $categories->first();
        $politicsCat = Category::where('slug', 'politik')->first() ?? $categories->first();
        $businessCat = Category::where('slug', 'bisnis')->first() ?? $categories->first();

        // 1. Published Lead Story (Priority 100)
        $leadArticle = Article::updateOrCreate(
            ['slug' => 'peta-jalan-indonesia-digital-2030-transformasi-sektor-publik'],
            [
                'title' => 'Peta Jalan Indonesia Digital 2030: Transformasi Sektor Publik dan Efisiensi Industri Nasional',
                'subtitle' => 'Pemerintah bersama pemangku kepentingan industri teknologi luncurkan pedoman integrasi AI dan kedaulatan data nasional.',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'category_id' => $techCat->id,
                'author_id' => $journalist->id,
                'editor_id' => $editor->id,
                'excerpt' => 'Pemerintah bersama para pemangku kepentingan industri teknologi resmi meluncurkan kerangka kerja strategis untuk mempercepat adopsi kecerdasan buatan, penguatan keamanan siber terpadu, dan kedaulatan data nasional.',
                'content' => '<h2>Akselerasi Transformasi Menuju Kedaulatan Digital</h2><p>Pemerintah Indonesia secara resmi mengumumkan peta jalan strategis bertajuk <strong>Indonesia Digital 2030</strong>. Dokumen ini menjadi pedoman komprehensif bagi seluruh kementerian, lembaga, dan pelaku industri untuk mengadopsi teknologi terdepan seperti <em>Artificial Intelligence (AI)</em>, komputasi awan berdaulat, dan infrastruktur jaringan 5G terdesentralisasi.</p><blockquote>"Transformasi digital bukan sekadar otomasi proses, melainkan penciptaan nilai tambah baru dan perlindungan kedaulatan data nasional," ujar pejabat berwenang dalam konferensi pers di Jakarta.</blockquote><h3>Fokus Pilar Utama</h3><ul><li>Penguatan keamanan siber dan perlindungan privasi data warga negara.</li><li>Peningkatan kapasitas komputasi untuk riset universitas dan startup teknologi.</li><li>Integrasi satu data layanan publik antar-lembaga negara.</li></ul><p>Langkah ini disambut positif oleh berbagai asosiasi teknologi dan pemodal ventura yang melihat peluang peningkatan efisiensi ekonomi nasional hingga 25% dalam kurun waktu lima tahun ke depan.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1200&auto=format&fit=crop',
                'featured_image_caption' => 'Ilustrasi infrastruktur konektivitas data dan satelit telekomunikasi nasional. (Dok. Kemenkominfo)',
                'featured_image_alt' => 'Infrastruktur digital dan jaringan data',
                'is_featured' => true,
                'is_breaking' => false,
                'homepage_priority' => 100,
                'is_editor_choice' => true,
                'is_sponsored' => false,
                'source_name' => 'TopNews Redaksi',
                'reading_time' => 3,
                'views_count' => 12450,
                'robots_index' => true,
                'submitted_at' => now()->subHours(6),
                'approved_at' => now()->subHours(4),
                'published_at' => now()->subHours(3),
            ]
        );
        $leadArticle->tags()->sync($tags->take(3)->pluck('id'));

        // 2. Published Breaking News
        $economyArticle = Article::updateOrCreate(
            ['slug' => 'pertumbuhan-ekonomi-kuartal-kedua-melampaui-ekspektasi'],
            [
                'title' => 'Pertumbuhan Ekonomi Kuartal II Melampaui Ekspektasi Analis, Didorong Konsumsi Domestik',
                'subtitle' => 'Badan Pusat Statistik mencatat tren ekspansi sektor manufaktur dan konsumsi rumah tangga tetap solid.',
                'content_type' => ArticleType::Analysis,
                'status' => ArticleStatus::Published,
                'category_id' => $economyCat->id,
                'author_id' => $journalist->id,
                'editor_id' => $editor->id,
                'excerpt' => 'Pertumbuhan ekonomi nasional mencatatkan angka positif berkat stabilitas inflasi pangan dan peningkatan investasi hilirisasi industri.',
                'content' => '<p>Pertumbuhan ekonomi Indonesia pada kuartal kedua tahun ini melaju melampaui perkiraan konsensus analis pasar. Dorongan utama berasal dari stabilitas konsumsi domestik serta kinerja ekspor manufaktur bernilai tambah tinggi.</p><h2>Ketahanan Sektor Riil</h2><p>Gubernur Bank Sentral menegaskan bahwa bauran kebijakan moneter dan fiskal yang terkoordinasi berhasil menjaga daya beli masyarakat di tengah ketidakpastian suku bunga global.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?q=80&w=600&auto=format&fit=crop',
                'featured_image_caption' => 'Aktivitas perdagangan dan bursa saham di kawasan finansial Jakarta. (Antara Foto)',
                'featured_image_alt' => 'Gedung Bursa dan Finansial',
                'is_featured' => true,
                'is_breaking' => true,
                'homepage_priority' => 80,
                'is_editor_choice' => false,
                'views_count' => 8920,
                'reading_time' => 2,
                'submitted_at' => now()->subHours(5),
                'approved_at' => now()->subHours(2),
                'published_at' => now()->subHours(2),
            ]
        );
        $economyArticle->tags()->sync($tags->slice(2, 2)->pluck('id'));

        // 3. Supporting Published Stories
        $articlesData = [
            [
                'slug' => 'reformasi-birokrasi-dan-efisiensi-anggaran-layanan-digital',
                'title' => 'Reformasi Birokrasi dan Efisiensi Anggaran Layanan Digital Nasional',
                'category_id' => $politicsCat->id,
                'content_type' => ArticleType::News,
                'homepage_priority' => 70,
                'views_count' => 5400,
                'image' => 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?q=80&w=600&auto=format&fit=crop',
            ],
            [
                'slug' => 'investasi-energi-terbarukan-tarik-minat-konsorsium-global',
                'title' => 'Investasi Energi Terbarukan Tarik Minat Konsorsium Global di Kawasan Industri Hijau',
                'category_id' => $businessCat->id,
                'content_type' => ArticleType::News,
                'homepage_priority' => 60,
                'views_count' => 4320,
                'image' => 'https://images.unsplash.com/photo-1466611653911-95081537e5b7?q=80&w=600&auto=format&fit=crop',
            ],
            [
                'slug' => 'penguatan-sistem-keamanan-data-perbankan-nasional',
                'title' => 'Penguatan Sistem Keamanan Data Perbankan Hadapi Ancaman Siber Terdistribusi',
                'category_id' => $techCat->id,
                'content_type' => ArticleType::Analysis,
                'homepage_priority' => 50,
                'views_count' => 3800,
                'image' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?q=80&w=600&auto=format&fit=crop',
            ],
            [
                'slug' => 'urgensi-etika-dan-tata-kelola-kecerdasan-buatan-di-ruang-publik',
                'title' => 'Urgensi Etika dan Regulasi Tata Kelola Kecerdasan Buatan di Ruang Publik',
                'category_id' => $techCat->id,
                'content_type' => ArticleType::Opinion,
                'homepage_priority' => 40,
                'views_count' => 2900,
                'image' => null,
            ],
            [
                'slug' => 'arah-kebijakan-fiskal-dan-ketahanan-pangan-2027',
                'title' => 'Arah Kebijakan Fiskal dan Upaya Kemandirian Pangan Menghadapi Volatilitas Iklim',
                'category_id' => $economyCat->id,
                'content_type' => ArticleType::Opinion,
                'homepage_priority' => 30,
                'views_count' => 2100,
                'image' => null,
            ],
        ];

        foreach ($articlesData as $item) {
            $art = Article::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'title' => $item['title'],
                    'subtitle' => 'Liputan eksklusif dan analisis mendalam dari dewan redaksi TopNews.',
                    'content_type' => $item['content_type'],
                    'status' => ArticleStatus::Published,
                    'category_id' => $item['category_id'],
                    'author_id' => $journalist->id,
                    'editor_id' => $editor->id,
                    'excerpt' => 'Ulasan komprehensif mengenai '.strtolower($item['title']).' serta dampaknya bagi publik.',
                    'content' => '<p>Ulasan komprehensif mengenai perkembangan terbaru yang dihimpun tim redaksi secara faktual.</p>',
                    'featured_image' => $item['image'],
                    'homepage_priority' => $item['homepage_priority'],
                    'is_editor_choice' => ($item['content_type'] === ArticleType::Analysis),
                    'views_count' => $item['views_count'],
                    'reading_time' => 3,
                    'published_at' => now()->subHours(rand(4, 24)),
                ]
            );

            // Seed daily view stats for today and yesterday
            ArticleDailyStat::updateOrCreate(
                ['article_id' => $art->id, 'date' => Carbon::today()->toDateString()],
                ['views' => (int) ($item['views_count'] * 0.4), 'unique_views' => (int) ($item['views_count'] * 0.3)]
            );
            ArticleDailyStat::updateOrCreate(
                ['article_id' => $art->id, 'date' => Carbon::yesterday()->toDateString()],
                ['views' => (int) ($item['views_count'] * 0.6), 'unique_views' => (int) ($item['views_count'] * 0.5)]
            );
        }

        // Daily stats for lead & economy articles
        ArticleDailyStat::updateOrCreate(
            ['article_id' => $leadArticle->id, 'date' => Carbon::today()->toDateString()],
            ['views' => 4500, 'unique_views' => 3200]
        );
        ArticleDailyStat::updateOrCreate(
            ['article_id' => $leadArticle->id, 'date' => Carbon::yesterday()->toDateString()],
            ['views' => 7950, 'unique_views' => 5600]
        );

        ArticleDailyStat::updateOrCreate(
            ['article_id' => $economyArticle->id, 'date' => Carbon::today()->toDateString()],
            ['views' => 3800, 'unique_views' => 2900]
        );
    }
}
