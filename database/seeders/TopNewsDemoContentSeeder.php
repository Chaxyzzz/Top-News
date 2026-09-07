<?php

namespace Database\Seeders;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Enums\UserStatus;
use App\Models\Article;
use App\Models\ArticleDailyStat;
use App\Models\ArticleVideo;
use App\Models\BreakingNews;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\HomepageSection;
use App\Models\Media;
use App\Models\PhotoStory;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use App\Services\HomepageConfigurationService;
use App\Services\MediaService;
use App\Services\PublicContentCacheService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TopNewsDemoContentSeeder extends Seeder
{
    /**
     * Run the comprehensive demo news content seeder.
     */
    public function run(): void
    {
        $this->command->info('=== TopNews Complete Demo News Content Seeder ===');

        // 1. Authors & Users Setup
        $superAdmin = User::withTrashed()->where('username', 'zakky77')->first();
        if ($superAdmin) {
            if ($superAdmin->trashed()) {
                $superAdmin->restore();
            }
            $superAdmin->forceFill([
                'name' => 'Zakky Mubaraq',
                'email' => 'topnews90@gmail.com',
                'password' => 'Mikaliso77',
                'account_type' => 'staff',
                'status' => UserStatus::Active,
            ])->save();
        } else {
            $superAdmin = User::create([
                'uuid' => (string) Str::uuid(),
                'name' => 'Zakky Mubaraq',
                'username' => 'Zakky77',
                'email' => 'topnews90@gmail.com',
                'password' => 'Mikaliso77',
                'account_type' => 'staff',
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ]);
        }
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], ['label' => 'Super Admin', 'is_system' => true]);
        $superAdmin->roles()->sync([$superAdminRole->id]);

        $editor = User::where('username', 'editor_demo')->first();
        if (! $editor) {
            $editor = User::create([
                'uuid' => (string) Str::uuid(),
                'name' => 'Nadia Aulia',
                'username' => 'editor_demo',
                'email' => 'nadia.editor@topnews.id',
                'password' => bcrypt('DemoEditor2026!'),
                'public_title' => 'Redaktur Pelaksana',
                'bio' => 'Jurnalis senior dan redaktur pelaksana TopNews dengan keahlian di bidang liputan investigasi dan teknologi.',
                'show_on_editorial_team' => true,
                'editorial_team_order' => 2,
                'account_type' => 'staff',
                'status' => UserStatus::Active,
            ]);
            $editor->assignRole('editor');
        }

        $journalist = User::where('username', 'journalist_demo')->first();
        if (! $journalist) {
            $journalist = User::create([
                'uuid' => (string) Str::uuid(),
                'name' => 'Raka Pratama',
                'username' => 'journalist_demo',
                'email' => 'raka.journalist@topnews.id',
                'password' => bcrypt('DemoJournalist2026!'),
                'public_title' => 'Reporter Riset & Teknologi',
                'bio' => 'Wartawan sains dan teknologi digital berbasis di Bireuen, Aceh.',
                'show_on_editorial_team' => true,
                'editorial_team_order' => 3,
                'account_type' => 'staff',
                'status' => UserStatus::Active,
            ]);
            $journalist->assignRole('journalist');
        }

        // 2. Categories Setup
        $categoriesData = [
            ['name' => 'Nasional', 'slug' => 'nasional', 'description' => 'Berita dan kebijakan publik terkini skala nasional.', 'accent_color' => '#E50914', 'sort_order' => 1],
            ['name' => 'Internasional', 'slug' => 'internasional', 'description' => 'Kabar perkembangan berita global dan hubungan antarnegara.', 'accent_color' => '#0D9488', 'sort_order' => 2],
            ['name' => 'Politik', 'slug' => 'politik', 'description' => 'Dinamika politik, hukum, pemerintahan, dan tata kelola negara.', 'accent_color' => '#7C3AED', 'sort_order' => 3],
            ['name' => 'Ekonomi', 'slug' => 'ekonomi', 'description' => 'Informasi ekonomi, pasar modal, UMKM, dan keuangan publik.', 'accent_color' => '#059669', 'sort_order' => 4],
            ['name' => 'Teknologi', 'slug' => 'teknologi', 'description' => 'Inovasi kecerdasan buatan, keamanan siber, dan riset teknologi.', 'accent_color' => '#2563EB', 'sort_order' => 5],
            ['name' => 'Olahraga', 'slug' => 'olahraga', 'description' => 'Kompetisi, kesehatan fisik, dan aktivitas olahraga komunitas.', 'accent_color' => '#EA580C', 'sort_order' => 6],
            ['name' => 'Hiburan', 'slug' => 'hiburan', 'description' => 'Seni, budaya, film, musik, dan konten kreatif generasi muda.', 'accent_color' => '#DB2777', 'sort_order' => 7],
            ['name' => 'Lifestyle', 'slug' => 'lifestyle', 'description' => 'Gaya hidup, produktivitas, kuliner lokal, dan budaya urban.', 'accent_color' => '#D97706', 'sort_order' => 8],
            ['name' => 'Kesehatan', 'slug' => 'kesehatan', 'description' => 'Edukasi kesehatan publik, nutrisi, dan wawasan medis terpercaya.', 'accent_color' => '#16A34A', 'sort_order' => 9],
            ['name' => 'Opini', 'slug' => 'opini', 'description' => 'Gagasan, esai analisis, dan pemikiran independen para ahli.', 'accent_color' => '#4F46E5', 'sort_order' => 10],
        ];

        $categories = collect();
        foreach ($categoriesData as $cData) {
            $cat = Category::updateOrCreate(
                ['slug' => $cData['slug']],
                [
                    'name' => $cData['name'],
                    'description' => $cData['description'],
                    'accent_color' => $cData['accent_color'],
                    'sort_order' => $cData['sort_order'],
                    'is_active' => true,
                ]
            );
            $categories->put($cat->slug, $cat);
        }

        // 3. Tags Setup
        $tagsData = [
            'Teknologi Digital', 'Inovasi', 'Pendidikan', 'Ekonomi Kreatif', 'UMKM',
            'Kesehatan', 'Lingkungan', 'Olahraga', 'Budaya', 'Generasi Muda',
            'Keamanan Siber', 'AI', 'Cloud Computing', 'Transformasi Digital',
            'Masyarakat', 'Aceh', 'Bireuen', 'Layanan Publik',
        ];

        $tags = collect();
        foreach ($tagsData as $tName) {
            $tSlug = Str::slug($tName);
            $tag = Tag::updateOrCreate(
                ['slug' => $tSlug],
                ['name' => $tName]
            );
            $tags->put($tSlug, $tag);
        }

        // 4. Local Demo Media Generation Helper
        $mediaService = app(MediaService::class);
        $demoMediaMap = $this->generateDemoMediaCollection($mediaService, $superAdmin);

        // 5. Build 40 Comprehensive Articles
        $articlesRaw = $this->getRawArticlesDefinition($categories);

        $createdArticles = [];
        $today = Carbon::now('Asia/Jakarta');

        foreach ($articlesRaw as $idx => $artData) {
            $category = $categories->get($artData['category_slug']) ?? $categories->first();
            $author = ($idx % 3 === 0) ? $journalist : (($idx % 3 === 1) ? $editor : $superAdmin);
            $editorUser = $editor;

            // Pick appropriate media
            $mediaKey = $artData['category_slug'];
            $media = $demoMediaMap->get($mediaKey) ?? $demoMediaMap->first();

            // Calculate date offset (staggered from today back to 45 days ago)
            $publishedAt = match ($artData['status']) {
                ArticleStatus::Published => $today->copy()->subMinutes($idx * 720 + rand(10, 300)),
                ArticleStatus::Scheduled => $today->copy()->addDays(2),
                default => null,
            };

            $submittedAt = $publishedAt ? $publishedAt->copy()->subHours(6) : null;
            $approvedAt = $publishedAt ? $publishedAt->copy()->subHours(2) : null;

            $article = Article::updateOrCreate(
                ['slug' => $artData['slug']],
                [
                    'uuid' => (string) Str::uuid(),
                    'title' => $artData['title'],
                    'subtitle' => $artData['subtitle'],
                    'excerpt' => $artData['excerpt'],
                    'content' => $artData['content'],
                    'content_type' => $artData['content_type'],
                    'status' => $artData['status'],
                    'category_id' => $category->id,
                    'author_id' => $author->id,
                    'editor_id' => $editorUser->id,
                    'featured_media_id' => $media?->id,
                    'featured_image' => $media?->path,
                    'featured_image_caption' => $artData['caption'] ?? "Dokumentasi visual liputan redaksi TopNews di {$category->name}.",
                    'featured_image_alt' => $artData['alt_text'] ?? "Ilustrasi liputan {$category->name}",
                    'is_featured' => $artData['is_featured'] ?? false,
                    'is_breaking' => $artData['is_breaking'] ?? false,
                    'homepage_priority' => $artData['homepage_priority'] ?? 0,
                    'is_editor_choice' => $artData['is_editor_choice'] ?? false,
                    'is_sponsored' => false,
                    'allow_comments' => true,
                    'source_name' => 'Redaksi TopNews',
                    'reading_time' => (int) ceil(str_word_count(strip_tags($artData['content'])) / 200),
                    'views_count' => $artData['views_count'] ?? rand(250, 4800),
                    'robots_index' => true,
                    'submitted_at' => $submittedAt,
                    'approved_at' => $approvedAt,
                    'published_at' => $publishedAt,
                ]
            );

            // Sync 2-4 relevant tags
            $tagSlugs = $artData['tag_slugs'] ?? ['inovasi', 'masyarakat'];
            $tagIds = collect($tagSlugs)->map(fn ($s) => $tags->get($s)?->id)->filter()->all();
            $article->tags()->sync($tagIds);

            // Populate Daily View Stats for Published Articles
            if ($article->status === ArticleStatus::Published) {
                $createdArticles[] = $article;
                $this->seedArticleStats($article);
            }

            // Handle Video Article metadata
            if ($artData['content_type'] === ArticleType::Video) {
                ArticleVideo::updateOrCreate(
                    ['article_id' => $article->id],
                    [
                        'provider' => 'youtube',
                        'video_id' => 'dQw4w9WgXcQ',
                        'embed_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                        'thumbnail_media_id' => $media?->id,
                        'duration_seconds' => rand(180, 480),
                    ]
                );
            }

            // Handle Photo Story metadata
            if ($artData['content_type'] === ArticleType::PhotoStory) {
                $gallery = Gallery::updateOrCreate(
                    ['slug' => $article->slug.'-gallery'],
                    [
                        'title' => 'Galeri Foto: '.$article->title,
                        'description' => $article->excerpt,
                        'cover_media_id' => $media?->id,
                        'author_id' => $author->id,
                        'photographer_name' => 'Tim Foto TopNews',
                        'status' => 'published',
                        'published_at' => $publishedAt,
                    ]
                );

                // Attach 4 media items to gallery
                $galleryMediaIds = $demoMediaMap->take(4)->pluck('id')->all();
                $pivotData = [];
                foreach ($galleryMediaIds as $gOrder => $gMediaId) {
                    $pivotData[$gMediaId] = [
                        'sort_order' => $gOrder + 1,
                        'caption_override' => 'Dokumentasi momen ke-'.($gOrder + 1).' dari peristiwa liputan redaksi.',
                        'credit_override' => 'Foto: TopNews / Redaksi',
                    ];
                }
                $gallery->media()->sync($pivotData);

                PhotoStory::updateOrCreate(
                    ['article_id' => $article->id],
                    ['gallery_id' => $gallery->id]
                );
            }
        }

        // 6. Active Breaking News Banner Setup
        $breakingArticle = Article::where('is_breaking', true)->where('status', ArticleStatus::Published)->first();
        if ($breakingArticle) {
            BreakingNews::updateOrCreate(
                ['article_id' => $breakingArticle->id],
                [
                    'headline' => 'BREAKING: '.$breakingArticle->title,
                    'is_active' => true,
                    'starts_at' => now()->subHours(1),
                    'ends_at' => now()->addHours(12),
                    'priority' => 10,
                    'created_by' => $superAdmin->id,
                ]
            );
        }

        // 7. Curate Manual Homepage Sections
        $manualSection = HomepageSection::where('source_type', 'manual')->first();
        if ($manualSection && ! empty($createdArticles)) {
            $curatedIds = collect($createdArticles)->take(5)->pluck('id')->all();
            $syncData = [];
            foreach ($curatedIds as $cOrder => $cId) {
                $syncData[$cId] = ['sort_order' => $cOrder + 1, 'created_at' => now()];
            }
            $manualSection->curatedArticles()->sync($syncData);
        }

        // 8. Invalidate Caches
        app(HomepageConfigurationService::class)->clearCache();
        app(PublicContentCacheService::class)->invalidateHomepage();

        $this->command->info('✓ Seeded 10 Categories, 18 Tags, 16 Local Media Assets, 40 Articles, Breaking News, Galleries, and Daily View Stats successfully.');
    }

    /**
     * Seed daily stats for an article across recent days.
     */
    protected function seedArticleStats(Article $article): void
    {
        $baseViews = $article->views_count ?: rand(300, 2500);

        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::today()->subDays($i)->toDateString();
            $dailyViews = (int) max(10, round(($baseViews / 7) * (1 + (rand(-30, 30) / 100))));
            $uniqueViews = (int) round($dailyViews * 0.75);

            ArticleDailyStat::updateOrCreate(
                ['article_id' => $article->id, 'date' => $date],
                ['views' => $dailyViews, 'unique_views' => $uniqueViews]
            );
        }
    }

    /**
     * Programmatically generate local PNG media assets using GD library and register Media models.
     *
     * @return Collection<string, Media>
     */
    protected function generateDemoMediaCollection(MediaService $mediaService, User $uploader)
    {
        $disk = 'public';
        $themes = [
            'nasional' => ['label' => 'NASIONAL & PUBLIK', 'bg1' => [200, 20, 30], 'bg2' => [30, 40, 60]],
            'internasional' => ['label' => 'KABAR INTERNASIONAL', 'bg1' => [13, 148, 136], 'bg2' => [15, 23, 42]],
            'politik' => ['label' => 'POLITIK & REGULASI', 'bg1' => [124, 58, 237], 'bg2' => [30, 27, 75]],
            'ekonomi' => ['label' => 'EKONOMI & UMKM', 'bg1' => [5, 150, 105], 'bg2' => [6, 78, 59]],
            'teknologi' => ['label' => 'INOVASI TEKNOLOGI', 'bg1' => [37, 99, 235], 'bg2' => [17, 24, 39]],
            'olahraga' => ['label' => 'OLAHRAGA & KOMUNITAS', 'bg1' => [234, 88, 12], 'bg2' => [67, 20, 7]],
            'hiburan' => ['label' => 'SENI & HIBURAN', 'bg1' => [219, 39, 119], 'bg2' => [74, 4, 38]],
            'lifestyle' => ['label' => 'GAYA HIDUP URBAN', 'bg1' => [217, 119, 6], 'bg2' => [69, 26, 3]],
            'kesehatan' => ['label' => 'KESEHATAN MASYARAKAT', 'bg1' => [22, 163, 74], 'bg2' => [20, 83, 45]],
            'opini' => ['label' => 'OPINI & GAGASAN', 'bg1' => [79, 70, 229], 'bg2' => [30, 27, 75]],
            'aceh' => ['label' => 'BIREUEN & ACEH', 'bg1' => [13, 148, 136], 'bg2' => [180, 83, 9]],
            'feature' => ['label' => 'LIPUTAN KHUSUS', 'bg1' => [229, 9, 20], 'bg2' => [17, 17, 17]],
        ];

        $mediaCollection = collect();
        $datePath = date('Y/m');

        foreach ($themes as $slug => $theme) {
            $uuid = (string) Str::uuid();
            $filename = "demo_{$slug}_{$uuid}.png";
            $relativePath = "media/{$datePath}/{$filename}";

            // Check if existing media record exists for this theme slug
            $existing = Media::where('original_filename', "demo_{$slug}.png")->first();
            if ($existing) {
                $mediaCollection->put($slug, $existing);

                continue;
            }

            // Create image canvas 1200x675 (16:9 ratio)
            $width = 1200;
            $height = 675;
            $image = imagecreatetruecolor($width, $height);

            // Draw gradient background
            for ($y = 0; $y < $height; $y++) {
                $ratio = $y / $height;
                $r = (int) ($theme['bg1'][0] * (1 - $ratio) + $theme['bg2'][0] * $ratio);
                $g = (int) ($theme['bg1'][1] * (1 - $ratio) + $theme['bg2'][1] * $ratio);
                $b = (int) ($theme['bg1'][2] * (1 - $ratio) + $theme['bg2'][2] * $ratio);
                $color = imagecolorallocate($image, $r, $g, $b);
                imageline($image, 0, $y, $width, $y, $color);
            }

            // Draw decorative grid lines
            $gridColor = imagecolorallocatealpha($image, 255, 255, 255, 115);
            for ($x = 0; $x < $width; $x += 80) {
                imageline($image, $x, 0, $x, $height, $gridColor);
            }
            for ($y = 0; $y < $height; $y += 80) {
                imageline($image, 0, $y, $width, $y, $gridColor);
            }

            // Draw Dark Card Overlay
            $cardBg = imagecolorallocatealpha($image, 0, 0, 0, 45);
            imagefilledrectangle($image, 60, 420, $width - 60, $height - 60, $cardBg);

            // Draw Red Brand Accent Bar
            $redBar = imagecolorallocate($image, 229, 9, 20);
            imagefilledrectangle($image, 60, 420, 72, $height - 60, $redBar);

            // Add Text Label
            $textColor = imagecolorallocate($image, 255, 255, 255);
            $subColor = imagecolorallocate($image, 200, 200, 200);

            imagestring($image, 5, 90, 440, $theme['label'], $textColor);
            imagestring($image, 4, 90, 480, 'TopNews Editorial Visual & Dokumentasi Media Digital', $subColor);
            imagestring($image, 3, 90, 520, 'TopNews Media Platform | Bireuen, Aceh, Indonesia', $subColor);

            // Save to temporary path
            $tempDir = storage_path('app/temp_seeder');
            if (! is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $tempFilePath = "{$tempDir}/{$filename}";
            imagepng($image, $tempFilePath);
            imagedestroy($image);

            // Create UploadedFile instance
            $uploadedFile = new UploadedFile(
                $tempFilePath,
                "demo_{$slug}.png",
                'image/png',
                null,
                true
            );

            // Store via MediaService
            $media = $mediaService->storeUploadedFile(
                $uploadedFile,
                $uploader,
                [
                    'alt_text' => "Ilustrasi visual untuk kategori {$theme['label']}",
                    'caption' => "Dokumentasi visual liputan redaksi TopNews pada rubrik {$theme['label']}.",
                    'credit' => 'Visual: TopNews',
                ],
                $disk
            );

            @unlink($tempFilePath);
            $mediaCollection->put($slug, $media);
        }

        return $mediaCollection;
    }

    /**
     * Comprehensive Raw Articles Dataset (40 Professional Fictional Stories)
     *
     * @param  Collection  $categories
     * @return list<array<string, mixed>>
     */
    protected function getRawArticlesDefinition($categories): array
    {
        return [
            // 1. Lead Featured Story (Nasional)
            [
                'slug' => 'peta-jalan-transformasi-layanan-publik-digital-2030',
                'title' => 'Peta Jalan Transformasi Layanan Publik Digital 2030: Menuju Tata Kelola yang Efisien dan Kredibel',
                'subtitle' => 'Pemerintah bersama pemangku kepentingan industri luncurkan pedoman integrasi satu data dan kedaulatan data nasional.',
                'excerpt' => 'Kerangka kerja strategis nasional resmi diluncurkan untuk mempercepat adopsi tata kelola berbasis digital, integrasi data pelayanan publik, dan perlindungan privasi warga negara.',
                'category_slug' => 'nasional',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'is_featured' => true,
                'is_breaking' => false,
                'homepage_priority' => 100,
                'is_editor_choice' => true,
                'views_count' => 14850,
                'tag_slugs' => ['transformasi-digital', 'layanan-publik', 'masyarakat'],
                'content' => '<h2>Akselerasi Tata Kelola Publik Berbasis Teknologi</h2><p>Langkah besar dalam modernisasi administrasi pemerintahan kembali ditegaskan melalui peluncuran <strong>Peta Jalan Transformasi Layanan Publik Digital 2030</strong>. Dokumen panduan nasional ini dirancang untuk menyelaraskan sistem informasi lintas kementerian, pemerintah daerah, dan lembaga pelayanan umum agar bekerja dalam satu ekosistem yang terpadu.</p><blockquote>"Digitalisasi sektor publik bukan sekadar memindahkan formulir fisik ke layar komputer, melainkan menyederhanakan birokrasi, menghemat anggaran negara, dan memberikan kepastian layanan bagi seluruh lapisan masyarakat," ungkap pejabat berwenang dalam acara peluncuran resmi.</blockquote><h3>Pilar Utama Implementasi</h3><ul><li><strong>Integrasi Satu Data:</strong> Penghapusan duplikasi basis data antar-instansi demi akurasi verifikasi penerima program bantuan sosial dan izin usaha.</li><li><strong>Keamanan Siber Terpadu:</strong> Penerapan standar enkripsi tinggi dan audit berkala untuk menjamin perlindungan data pribadi publik.</li><li><strong>Inklusivitas Wilayah:</strong> Penataan konektivitas internet pita lebar hingga kawasan pelosok dan perbatasan negara.</li></ul><p>Para pengamat kebijakan publik menilai bahwa efisiensi yang dihasilkan dari integrasi ini berpotensi menghemat alokasi operasional birokrasi hingga 20 persen dalam kurun lima tahun mendatang.</p>',
            ],

            // 2. Breaking News Story (Bireuen / Aceh)
            [
                'slug' => 'komunitas-pemuda-bireuen-kembangkan-ruang-belajar-literasi-digital',
                'title' => 'Komunitas Pemuda Bireuen Kembangkan Ruang Belajar Literasi Digital untuk Pelajar dan UMKM',
                'subtitle' => 'Inisiatif swadaya masyarakat menghadirkan pelatihan pemrogaman dasar, keamanan internet, dan pemasaran digital gratis.',
                'excerpt' => 'Gerakan literasi digital independen di Kabupaten Bireuen menjadi ruang kolaborasi kreatif anak muda untuk meningkatkan kecakapan teknologi dan daya saing ekonomi lokal.',
                'category_slug' => 'nasional',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'is_featured' => true,
                'is_breaking' => true,
                'homepage_priority' => 90,
                'is_editor_choice' => false,
                'views_count' => 9820,
                'tag_slugs' => ['bireuen', 'aceh', 'generasi-muda', 'pendidikan'],
                'content' => '<h2>Inisiatif Swadaya Membangun Kecakapan Digital Regional</h2><p>Di tengah pesatnya perkembangan teknologi informasi, sekumpulan pemuda kreatif di Kabupaten Bireuen, Aceh, mengambil langkah nyata dengan mendirikan ruang belajar terbuka bertajuk <strong>Kanal Literasi Digital Bireuen</strong>. Program ini memfasilitasi anak sekolah, mahasiswa, serta pelaku usaha mikro untuk mempelajari keterampilan digital terapan secara cuma-cuma.</p><h2>Materi Pelatihan Aksi Nyata</h2><p>Fasilitator kegiatan menjelaskan bahwa materi yang diajarkan mencakup fondasi etika berinternet, pemrosesan dokumen digital, hingga pembuatan konten kreatif yang aman dari risiko penipuan siber.</p><blockquote>"Kami ingin memastikan generasi muda di Bireuen tidak hanya menjadi konsumen teknologi, tetapi mampu memanfaatkan internet secara produktif untuk karya dan ekonomi," ujar koordinator kegiatan.</blockquote><p>Program ini mendapat sambutan hangat dari berbagai pihak dan direncanakan meluas ke beberapa kecamatan sekitarnya.</p>',
            ],

            // 3. Ekonomi & UMKM
            [
                'slug' => 'pelaku-umkm-daerah-manfaatkan-platform-digital-perluas-pasar',
                'title' => 'Pelaku UMKM Daerah Mulai Memanfaatkan Platform Digital untuk Memperluas Jangkauan Pasar',
                'subtitle' => 'Penggunaan sistem pembayaran nontunai dan etalase daring mendorong kenaikan omzet usaha kecil di berbagai daerah.',
                'excerpt' => 'Digitalisasi rantai pasok dan adopsi kanal pemasaran daring menjadi kunci ketahanan usaha mikro menengah dalam menghadapi tantangan ekonomi global.',
                'category_slug' => 'ekonomi',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'is_featured' => true,
                'is_breaking' => false,
                'homepage_priority' => 80,
                'is_editor_choice' => true,
                'views_count' => 8400,
                'tag_slugs' => ['umkm', 'ekonomi-kreatif', 'inovasi'],
                'content' => '<h2>Lompatan Usaha Lokal ke Ekosistem Nasional</h2><p>Sektor Usaha Mikro, Kecil, dan Menengah (UMKM) terus menunjukkan daya adaptasi tinggi di tengah perubahan perilaku konsumen. Banyak pelaku usaha lokal di kawasan pelosok kini aktif mengadopsi etalase pemasaran digital dan dompet elektronik untuk melayani pembeli lintas pulau.</p><h3>Manfaat Utama Digitalisasi UMKM</h3><ul><li>Peningkatan jangkauan pelanggan tanpa terbatas wilayah geografis.</li><li>Pencatatan transaksi keuangan secara otomatis dan efisien.</li><li>Kemudahan dalam mengakses kemitraan serta permodalan usaha.</li></ul><p>Pemerintah daerah mengapresiasi keaktifan para pengusaha muda yang menjadi pelopor pembuka jalan pemasaran digital bagi produk unggulan daerah.</p>',
            ],

            // 4. Teknologi AI
            [
                'slug' => 'pemanfaatan-kecerdasan-buatan-dalam-mendukung-proses-belajar',
                'title' => 'Pemanfaatan Kecerdasan Buatan dalam Mendukung Proses Belajar dan Riset Akademik',
                'subtitle' => 'Dosen dan peneliti tekankan pentingnya panduan etika agar penggunaan AI generatif tetap menjunjung integritas ilmiah.',
                'excerpt' => 'Integrasi perangkat AI di lingkungan pendidikan tinggi membuka peluang pembelajaran kustomisasi, namun memerlukan batasan etis yang jelas agar tidak mengikis pemikiran kritis.',
                'category_slug' => 'teknologi',
                'content_type' => ArticleType::Analysis,
                'status' => ArticleStatus::Published,
                'is_featured' => true,
                'is_breaking' => false,
                'homepage_priority' => 75,
                'is_editor_choice' => true,
                'views_count' => 7650,
                'tag_slugs' => ['ai', 'pendidikan', 'teknologi-digital'],
                'content' => '<h2>Menyeimbangkan Inovasi dan Etika Akademik</h2><p>Kehadiran teknologi kecerdasan buatan (AI) membawa perubahan signifikan dalam metode pembelajaran di ruang kuliah maupun perpustakaan digital. Mahasiswa kini dapat mengakses rangkuman literatur dan analisis data awal dengan waktu yang jauh lebih singkat.</p><blockquote>"AI adalah alat bantu penjelajahan informasi yang sangat luar biasa. Namun keberadaan alat ini tidak pernah bisa menggantikan validasi fakta, penalaran analitis, dan etika peneliti," tegas salah seorang akademisi senior.</blockquote><p>Lembaga pendidikan pun disarankan segera menyusun koridor penggunaan AI agar nilai akademik tetap terjaga secara kredibel.</p>',
            ],

            // 5. Keamanan Siber
            [
                'slug' => 'keamanan-siber-menjadi-prioritas-utama-di-tengah-pertumbuhan-layanan-digital',
                'title' => 'Keamanan Siber Menjadi Prioritas Utama di Tengah Pertumbuhan Layanan Digital Modern',
                'subtitle' => 'Pakar ingatkan pentingnya edukasi kata sandi berlapis dan kesadaran bahaya phising bagi pengguna internet.',
                'excerpt' => 'Seiring pesatnya transaksi elektronik, ancaman kebocoran data dan kejahatan siber memerlukan proteksi sistem yang mutakhir serta kewaspadaan kolektif pengguna.',
                'category_slug' => 'teknologi',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'is_featured' => false,
                'is_breaking' => false,
                'homepage_priority' => 60,
                'is_editor_choice' => false,
                'views_count' => 6200,
                'tag_slugs' => ['keamanan-siber', 'teknologi-digital'],
                'content' => '<h2>Membangun Pertahanan Siber Nasional yang Tangguh</h2><p>Pertumbuhan ekosistem aplikasi keuangan dan portal publik menjadikan isu keamanan siber sebagai salah satu pilar krusial. Penyerang siber kerap memanfaatkan kelalaian autentikasi sederhana untuk meretas akun pengguna.</p><h3>Langkah Praktis Perlindungan Akun</h3><ul><li>Aktifkan fitur Authenticator Dua Faktor (2FA) di seluruh platform penting.</li><li>Hindari mengeklik tautan asing yang diterima via pesan singkat atau email tak dikenal.</li><li>Perbarui sistem operasi dan perangkat lunak antivirus secara berkala.</li></ul>',
            ],

            // 6. Opini 1
            [
                'slug' => 'mengapa-kecepatan-informasi-harus-tetap-berjalan-bersama-akurasi',
                'title' => 'Mengapa Kecepatan Informasi Harus Tetap Berjalan Bersama Akurasi di Era Media Digital',
                'subtitle' => 'Opini Redaksi: Jurnalistik berkualitas tidak boleh mengorbankan konfirmasi demi mengejar klik emosional semata.',
                'excerpt' => 'Di tengah arus kabar media sosial yang cepat dan terkadang simpang siur, jurnalisme berimbang dengan standar verifikasi fakta ketat tetap menjadi benteng kebenaran publik.',
                'category_slug' => 'opini',
                'content_type' => ArticleType::Opinion,
                'status' => ArticleStatus::Published,
                'is_featured' => false,
                'is_breaking' => false,
                'homepage_priority' => 65,
                'is_editor_choice' => true,
                'views_count' => 5900,
                'tag_slugs' => ['masyarakat', 'pendidikan'],
                'content' => '<h2>Esai Opini Redaksi TopNews</h2><p>Dalam dinamika media modern, kecepatan sering kali dipandang sebagai nilai tertinggi. Informasi terbaru harus segera diunggah dalam hitungan detik agar tidak kehilangan momen dibicarakan warganet. Namun, apakah kecepatan tanpa akurasi sungguh memberikan manfaat nyata bagi publik?</p><blockquote>"Tugas utama jurnalisme bukan hanya menjadi yang pertama mengabarkan, melainkan menjamin bahwa apa yang dikabarkan adalah kebenaran yang telah diperiksa ulangnya."</blockquote><p>Verifikasi berlapis adalah jaminan kehormatan antara media independen dan pembacanya.</p>',
            ],

            // 7. Kesehatan Public
            [
                'slug' => 'kebiasaan-sederhana-menjaga-kesehatan-di-tengah-aktivitas-padat',
                'title' => 'Kebiasaan Sederhana yang Membantu Menjaga Kesehatan di Tengah Aktivitas Padat',
                'subtitle' => 'Dokter sarankan istirahat mata berkala, hidrasi cukup, dan olahraga ringan untuk pekerja kantoran.',
                'excerpt' => 'Menjaga stamina dan kesehatan mental di era kerja cepat dapat dimulai dari kebiasaan rutin yang mudah diterapkan sehari-hari.',
                'category_slug' => 'kesehatan',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'is_featured' => false,
                'is_breaking' => false,
                'homepage_priority' => 50,
                'views_count' => 4800,
                'tag_slugs' => ['kesehatan', 'generasi-muda'],
                'content' => '<h2>Menjaga Keseimbangan Fisik dan Pikiran</h2><p>Bekerja di depan layar komputer selama bertumpu jam sering kali memicu kelelahan fisik dan ketegangan mata. Para ahli kesehatan merekomendasikan aturan 20-20-20, yaitu mengalihkan pandangan sejauh 20 kaki selama 20 detik setiap 20 menit bekerja.</p>',
            ],

            // 8. Olahraga Komunitas
            [
                'slug' => 'aktivitas-olahraga-komunitas-tumbuh-sebagai-ruang-kreatif-pemuda',
                'title' => 'Aktivitas Olahraga Komunitas Tumbuh sebagai Ruang Kreatif dan Interaksi Pemuda',
                'subtitle' => 'Kegiatan lari bersama dan bersepeda mingguan jadi wadah silaturahmi positif anak muda di daerah.',
                'excerpt' => 'Semangat hidup sehat mendorong terbentuknya pelbagai klub olahraga swadaya yang inklusif dan mempromosikan gaya hidup aktif.',
                'category_slug' => 'olahraga',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'is_featured' => false,
                'is_breaking' => false,
                'homepage_priority' => 45,
                'views_count' => 4100,
                'tag_slugs' => ['olahraga', 'generasi-muda'],
                'content' => '<h2>Gaya Hidup Sehat Melalui Olahraga Bersama</h2><p>Geliat kegiatan fisik di ruang publik terbuka semakin semarak dengan hadirnya beragam komunitas lari pagi dan bersepeda santai. Selain bugar, kegiatan ini menjadi wahana berjejaring secara sehat dan positif bagi para remaja dan profesional muda.</p>',
            ],

            // 9. Video News Demo 1
            [
                'slug' => 'video-liputan-khusus-tumbuhnya-ekosistem-kreatif-di-bireuen',
                'title' => 'VIDEO: Liputan Khusus Tumbuhnya Ekosistem Kreatif Anak Muda di Kabupaten Bireuen',
                'subtitle' => 'Simak wawancara dan liputan lapangan tim multimedia TopNews melihat geliat wirausaha muda.',
                'excerpt' => 'Dokumentasi video singkat mengenai bagaimana generasi muda Bireuen merintis usaha produk kreatif berbasis kearifan lokal.',
                'category_slug' => 'teknologi',
                'content_type' => ArticleType::Video,
                'status' => ArticleStatus::Published,
                'is_featured' => false,
                'is_breaking' => false,
                'homepage_priority' => 40,
                'views_count' => 5300,
                'tag_slugs' => ['bireuen', 'aceh', 'ekonomi-kreatif'],
                'content' => '<p>Tayangan dokumenter eksklusif dari tim multimedia TopNews meliput perjuangan komunitatif anak muda di Bireuen dalam membangun jejaring usaha kreatif. Tonton video selengkapnya melalui pemutar interaktif di atas.</p>',
            ],

            // 10. Photo Story Demo 1
            [
                'slug' => 'foto-cerita-potret-kehidupan-ruang-publik-dan-kreativitas-anak-muda',
                'title' => 'FOTO CERITA: Potret Kehidupan Ruang Publik dan Kreativitas Anak Muda di Aceh',
                'subtitle' => 'Rangkaian lima foto lensa dokumenter merekam dinamika sore hari di pusat kegiatan warga.',
                'excerpt' => 'Kumpulan potret estetik dari fotografer jurnalis TopNews yang memperlihatkan kehangatan aktivitas warga lokal.',
                'category_slug' => 'hiburan',
                'content_type' => ArticleType::PhotoStory,
                'status' => ArticleStatus::Published,
                'is_featured' => false,
                'is_breaking' => false,
                'homepage_priority' => 35,
                'views_count' => 4900,
                'tag_slugs' => ['budaya', 'aceh', 'bireuen'],
                'content' => '<p>Melalui lensa kamera jurnalis TopNews, simak rangkaian narasi visual yang menangkap keceriaan, kerja keras, dan keindahan interaksi sosial warga lokal di Aceh.</p>',
            ],

            // 11-40: Remaining Diverse Fictional Articles
            [
                'slug' => 'cloud-computing-bantu-organisasi-kelola-infrastruktur-fleksibel',
                'title' => 'Cloud Computing Membantu Organisasi Mengelola Infrastruktur Data Secara Fleksibel',
                'subtitle' => 'Adopsi komputasi awan hibrida menekan biaya operasional dan tingkatkan keandalan layanan digital.',
                'excerpt' => 'Pemanfaatan server cloud berteknologi tinggi memberikan keleluasaan bagi tim IT dalam mengembangkan aplikasi tanpa hambatan perangkat fisik.',
                'category_slug' => 'teknologi',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['cloud-computing', 'teknologi-digital'],
                'content' => '<p>Dunia teknologi terus berinovasi dalam memfasilitasi penyimpanan dan pengolahan data terdistribusi secara aman.</p>',
            ],
            [
                'slug' => 'ekonomi-kreatif-buka-peluang-baru-anak-muda-di-daerah',
                'title' => 'Ekonomi Kreatif Membuka Peluang Usaha Baru bagi Anak Muda di Wilayah Daerah',
                'subtitle' => 'Bidang desain grafis, pembuatan konten, dan kopi spesialti menjadi penggerak baru ekonomi lokal.',
                'excerpt' => 'Kreativitas pemuda di tingkat kabupaten terbukti mampu mencetak produk bernilai tambah tinggi yang diminati konsumen luar daerah.',
                'category_slug' => 'ekonomi',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['ekonomi-kreatif', 'umkm'],
                'content' => '<p>Usaha kreatif yang digerakkan generasi muda terus berkembang pesat seiring kemudahan akses promosi digital.</p>',
            ],
            [
                'slug' => 'opini-ruang-kreatif-anak-muda-dan-masa-depan-ekonomi-daerah',
                'title' => 'Ruang Kreatif Anak Muda dan Masa Depan Kemandirian Ekonomi Daerah',
                'subtitle' => 'Catatan Kritis: Pemerintah daerah perlu memfasilitasi hub komunitas sebagai tempat inkubasi gagasan.',
                'excerpt' => 'Dukungan fasilitas publik yang memadai bagi komunitas kreatif akan memicu lahirnya inovasi bisnis lokal berdaya saing.',
                'category_slug' => 'opini',
                'content_type' => ArticleType::Opinion,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['ekonomi-kreatif', 'generasi-muda'],
                'content' => '<p>Setiap wilayah memiliki potensi unik yang dapat diolah oleh kreativitas generasi mudanya jika diberikan ruang bertumbuh.</p>',
            ],
            [
                'slug' => 'inovasi-pendidikan-hubungkan-mahasiswa-dengan-kebutuhan-dunia-kerja',
                'title' => 'Inovasi Pendidikan Menghubungkan Mahasiswa dengan Kebutuhan Kerja Modern',
                'subtitle' => 'Program magang berbasis proyek nyata persiapkan lulusan perguruan tinggi hadapi tantangan industri.',
                'excerpt' => 'Kolaborasi dunia akademik dan praktisi industri memastikan kurikulum pembelajaran tetap relevan dengan dinamika pekerjaan terkini.',
                'category_slug' => 'nasional',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['pendidikan', 'generasi-muda'],
                'content' => '<p>Kurikulum interaktif yang memadukan teori akademik dan praktik lapangan terbukti meningkatkan kesiapan kerja lulusan baru.</p>',
            ],
            [
                'slug' => 'fotografi-jalanan-menjadi-cara-baru-merekam-dinamika-ruang-kota',
                'title' => 'Fotografi Jalanan Menjadi Cara Baru Merekam Dinamika Ruang dan Sosial Kota',
                'subtitle' => 'Kamera saku dan ponsel cerdas dimanfaatkan pegiat visual untuk mengabadikan momen keseharian warga.',
                'excerpt' => 'Seni fotografi jalanan makin diminati pemuda karena menyajikan sudut pandang otentik mengenai kehidupan masyarakat.',
                'category_slug' => 'hiburan',
                'content_type' => ArticleType::Feature,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['budaya', 'generasi-muda'],
                'content' => '<p>Melalui jepretan spontan di jalanan, kisah-kisah kecil manusia dapat terekam secara estetik dan penuh makna.</p>',
            ],
            [
                'slug' => 'kebiasaan-minum-air-putih-cukup-bantu-menjaga-fokus-kerja',
                'title' => 'Pentingnya Konsumsi Air Putih Cukup untuk Menjaga Fokus dan Produktivitas Kerja',
                'subtitle' => 'Dehidrasi ringan dapat menurunkan konsentrasi dan memicu kelelahan tubuh saat beraktivitas.',
                'excerpt' => 'Menjaga cairan tubuh adalah langkah dasar yang sangat berpengaruh terhadap performa kognitif dan kesehatan organ.',
                'category_slug' => 'kesehatan',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['kesehatan'],
                'content' => '<p>Ahli gizi mengimbau masyarakat untuk selalu menyediakan air minum di meja kerja agar terhindar dari gejala dehidrasi.</p>',
            ],
            [
                'slug' => 'wawancara-raka-pratama-pentingnya-literasi-teknologi-generasi-muda',
                'title' => 'Wawancara Eksklusif: Pentingnya Literasi Teknologi bagi Generasi Muda Indonesia',
                'subtitle' => 'Bincang redaksi bersama pegiat edukasi digital mengenai tantangan dan peluang masa depan.',
                'excerpt' => 'Wawancara mendalam membahas pentingnya pemahaman etika internet, pemrograman dasar, dan perlindungan data pribadi.',
                'category_slug' => 'teknologi',
                'content_type' => ArticleType::Interview,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['teknologi-digital', 'generasi-muda'],
                'content' => '<p>Redaksi TopNews berkesempatan berbincang mengenai arah perkembangan keterampilan digital di kalangan remaja Indonesia.</p>',
            ],
            [
                'slug' => 'perkembangan-teknologi-ramah-lingkungan-di-sektor-energi',
                'title' => 'Perkembangan Teknologi Ramah Lingkungan untuk Keberlanjutan Energi Masa Depan',
                'subtitle' => 'Panel surya murah dan pengelolaan sampah mandiri mulai diterapkan di berbagai pemukiman.',
                'excerpt' => 'Solusi teknologi berbasis energi bersih membantu mengurangi jejak karbon dan efisiensi konsumsi listrik rumah tangga.',
                'category_slug' => 'internasional',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['lingkungan', 'inovasi'],
                'content' => '<p>Penerapan teknologi ramah lingkungan terbukti makin terjangkau dan memberikan manfaat nyata bagi kelestarian alam.</p>',
            ],
            [
                'slug' => 'analisis-arah-kebijakan-ekonomi-digital-regional-2026',
                'title' => 'Analisis Arah Kebijakan Ekonomi Digital Regional dan Implikasinya bagi Pasar Lokal',
                'subtitle' => 'Tinjauan mendalam terhadap regulasi transaksi elektronik dan perlindungan konsumen daerah.',
                'excerpt' => 'Kesiapan infrastruktur dan regulasi daerah sangat menentukan keberhasilan daya saing komoditas lokal di tingkat global.',
                'category_slug' => 'ekonomi',
                'content_type' => ArticleType::Analysis,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['ekonomi-kreatif', 'transformasi-digital'],
                'content' => '<p>Kajian menyeluruh mengenai harmonisasi regulasi perdagangan digital demi mendukung efisiensi transaksi ekonomi.</p>',
            ],
            [
                'slug' => 'opini-teknologi-seharusnya-membantu-manusia-berpikir-lebih-baik',
                'title' => 'Teknologi Seharusnya Membantu Manusia Berpikir Lebih Baik, Bukan Menggantikannya',
                'subtitle' => 'Gagasan: Perangkat digital harus mempermudah pekerjaan tanpa menghilangkan nilai kemanusiaan.',
                'excerpt' => 'Peran kemajuan teknologi adalah memperluas kapasitas intelektual manusia untuk menciptakan solusi kemasyarakatan.',
                'category_slug' => 'opini',
                'content_type' => ArticleType::Opinion,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['ai', 'masyarakat'],
                'content' => '<p>Refleksi filosofis tentang hubungan harmoni antara kemajuan alat kecerdasan buatan dan nurani manusia.</p>',
            ],

            // Additional 20 Stories across all categories to hit 40 target
            [
                'slug' => 'peran-pemuda-dalam-pelestarian-budaya-lokal-aceh',
                'title' => 'Peran Penting Komunitas Pemuda dalam Pelestarian Seni dan Budaya Lokal Aceh',
                'subtitle' => 'Generasi muda memanfaatkan platform media sosial untuk mendokumentasikan warisan seni pertunjukan.',
                'excerpt' => 'Semangat pelestarian budaya lokal makin marak berkat inisiatif pembuatan arsip digital dan pentas seni berkala.',
                'category_slug' => 'lifestyle',
                'content_type' => ArticleType::Feature,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['aceh', 'budaya', 'generasi-muda'],
                'content' => '<p>Generasi muda di Aceh aktif mengangkat kembali ragam kesenian tradisional agar dikenal luas oleh publik internasional.</p>',
            ],
            [
                'slug' => 'diplomasi-kreatif-dan-pertukaran-budaya-antarnegara',
                'title' => 'Diplomasi Kreatif dan Pertukaran Budaya Antarnegara Pererat Hubungan Internasional',
                'subtitle' => 'Festival film dan pameran seni digital tingkat internasional sukses selenggarakan kolaborasi lintas budaya.',
                'excerpt' => 'Seni dan konten digital terbukti efektif menjadi jembatan pemahaman diplomasi budaya antar-bangsa di era modern.',
                'category_slug' => 'internasional',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['budaya', 'inovasi'],
                'content' => '<p>Kerja sama kebudayaan internasional terus membuka ruang apresiasi antar-seniman dari pelbagai benua.</p>',
            ],
            [
                'slug' => 'perkembangan-dunia-politik-hukum-dan-tata-kelola-publik',
                'title' => 'Transparansi Publik dan Keterbukaan Informasi Hukum dalam Tata Kelola Modern',
                'subtitle' => 'Lembaga pengawas tekankan akses publik terhadap dokumen perundang-undangan secara daring.',
                'excerpt' => 'Kemudahan masyarakat mengakses draft aturan hukum secara transparan meningkatkan partisipasi publik yang bermakna.',
                'category_slug' => 'politik',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['layanan-publik', 'masyarakat'],
                'content' => '<p>Partisipasi publik dalam pengawalan kebijakan hukum menjadi fondasi utama tata kelola negara yang akuntabel.</p>',
            ],
            [
                'slug' => 'panduan-memilih-makanan-sehat-untuk-menjaga-stamina',
                'title' => 'Panduan Memilih Pola Makan Gizi Seimbang untuk Menjaga Stamina Tubuh',
                'subtitle' => 'Edukasi konsumsi sayur segar dan pengurangan gula berlebih untuk pola hidup sehat.',
                'excerpt' => 'Kombinasi nutrisi tepat dan pola makan teratur berpengaruh langsung terhadap daya tahan tubuh dalam beraktivitas harian.',
                'category_slug' => 'kesehatan',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['kesehatan'],
                'content' => '<p>Penyuluhan gizi seimbang terus digalakkan agar masyarakat makin sadar pentingnya mutu makanan sehari-hari.</p>',
            ],
            [
                'slug' => 'tumbuhnya-klub-sepeda-dan-gaya-hidup-aktif-di-kota',
                'title' => 'Tumbuhnya Komunitas Bersepeda dan Gaya Hidup Sehat di Kawasan Perkotaan',
                'subtitle' => 'Komunitas sepeda rutin adakan gowes santai akhir pekan melintasi jalur hijau kota.',
                'excerpt' => 'Selain olahraga, tren bersepeda menjadi wahana rekreasi ramah lingkungan yang menyehatkan bagi keluarga.',
                'category_slug' => 'olahraga',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['olahraga', 'lingkungan'],
                'content' => '<p>Aktivitas bersepeda bersama kian populer sebagai alternatif sarana transportasi bersih di kawasan pemukiman.</p>',
            ],
            [
                'slug' => 'pameran-seni-kreatif-muda-tampilkan-karya-desain-digital',
                'title' => 'Pameran Seni Kreatif Pemuda Tampilkan Ragam Karya Desain Digital dan Animasi',
                'subtitle' => 'Puluhan kreator muda unjuk gigi menyajikan visual animasi dan tipografi artistik.',
                'excerpt' => 'Ajang pameran kreatif menjadi pembuktian tingginya kualitas talenta muda di industri konten visual nasional.',
                'category_slug' => 'hiburan',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['ekonomi-kreatif', 'generasi-muda'],
                'content' => '<p>Karya visual digital anak bangsa mendapat apresiasi positif dari kalangan profesional dan penikmat seni.</p>',
            ],
            [
                'slug' => 'inovasi-pengolahan-sampah-organik-berbasis-masyarakat-bireuen',
                'title' => 'Inovasi Pengolahan Sampah Organik Berbasis Masyarakat Mandiri di Bireuen',
                'subtitle' => 'Warga desa mengolah limbah rumah tangga menjadi pupuk kompos berkualitas untuk pertanian.',
                'excerpt' => 'Gerakan peduli lingkungan di Bireuen berhasil mengurangi volume sampah ke TPA sekaligus menghasilkan kompos bernilai ekonomi.',
                'category_slug' => 'lifestyle',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['bireuen', 'aceh', 'lingkungan', 'masyarakat'],
                'content' => '<p>Inisiatif pengolahan sampah mandiri menjadi contoh sukses pemberdayaan warga dalam menjaga kebersihan lingkungan.</p>',
            ],
            [
                'slug' => 'opini-media-digital-perlu-menjaga-kecepatan-tanpa-kehilangan-akurasi',
                'title' => 'Media Digital Perlu Menjaga Kecepatan Tanpa Kehilangan Etika dan Akurasi Berita',
                'subtitle' => 'Tinjauan Etika: Kepercayaan pembaca adalah modal utama kelangsungan media massa berkualitas.',
                'excerpt' => 'Mengabaikan verifikasi fakta demi kejar tayang hanya akan merugikan kredibilitas pers dalam jangka panjang.',
                'category_slug' => 'opini',
                'content_type' => ArticleType::Opinion,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['pendidikan', 'masyarakat'],
                'content' => '<p>Kualitas produk jurnalistik diukur dari kejujuran dalam menyampaikan fakta serta keberanian mengoreksi kesalahan secara terbuka.</p>',
            ],
            [
                'slug' => 'video-inovasi-teknologi-pertanian-presisi-petani-muda',
                'title' => 'VIDEO: Inovasi Teknologi Pertanian Presisi Karya Kelompok Petani Muda',
                'subtitle' => 'Penggunaan sensor kelembaban tanah dan penyiraman otomatis meningkatkan hasil panen.',
                'excerpt' => 'Saksikan tayangan liputan teknologi pintar di bidang agrikultur yang dikembangkan oleh wirausaha muda.',
                'category_slug' => 'teknologi',
                'content_type' => ArticleType::Video,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['teknologi-digital', 'inovasi'],
                'content' => '<p>Penerapan sensor berbasis IoT di lahan pertanian membantu efisiensi penggunaan air dan pupuk secara terukur.</p>',
            ],
            [
                'slug' => 'foto-cerita-keindahan-alam-dan-kehidupan-pesisir-aceh',
                'title' => 'FOTO CERITA: Pesona Alam dan Keseharian Nelayan Tradisional di Pesisir Aceh',
                'subtitle' => 'Dokumentasi empat lanskap fotografi memotret keteguhan nelayan menjemput rezeki di laut.',
                'excerpt' => 'Visualisasi foto bercerita mengenai keharmonisan manusia dan alam pesisir pantai Sumatera.',
                'category_slug' => 'hiburan',
                'content_type' => ArticleType::PhotoStory,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['aceh', 'budaya'],
                'content' => '<p>Keindahan lanskap pantai dan aktivitas nelayan tradisional terekam indah dalam galeri potret jurnalis TopNews.</p>',
            ],

            // Non-published workflow demo articles (Draft, Submitted, Review, Approved, Scheduled, Archived)
            [
                'slug' => 'draft-rencana-pengembangan-taman-teknologi-daerah',
                'title' => '[DRAFT] Rencana Pengembangan Taman Teknologi dan Inkubator Startup Daerah',
                'subtitle' => 'Konsep perancangan kawasan riset terpadu bagi pelajar dan wirausaha digital.',
                'excerpt' => 'Naskah draf berita internal mengenai rencana pembangunan fasilitas taman teknologi daerah.',
                'category_slug' => 'teknologi',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Draft,
                'tag_slugs' => ['inovasi', 'teknologi-digital'],
                'content' => '<p>Naskah ini masih dalam tahap penyusunan draf oleh tim reporter redaksi.</p>',
            ],
            [
                'slug' => 'draft-kajian-potensi-ekspor-produk-kerajinan-lokal',
                'title' => '[DRAFT] Kajian Potensi Ekspor Produk Kerajinan dan Konveksi Lokal',
                'subtitle' => 'Riset bahan baku dan peluang rantai pasok ekonomi kreatif.',
                'excerpt' => 'Catatan draf awal laporan potensi pemasaran ekspor kerajinan tangan daerah.',
                'category_slug' => 'ekonomi',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Draft,
                'tag_slugs' => ['ekonomi-kreatif', 'umkm'],
                'content' => '<p>Draf tulisan laporan penelitian potensi ekonomi daerah.</p>',
            ],
            [
                'slug' => 'submitted-perkembangan-fasilitas-kesehatan-puskesmas-bireuen',
                'title' => '[SUBMITTED] Perkembangan Fasilitas Layanan Kesehatan di Puskesmas Kabupaten Bireuen',
                'subtitle' => 'Laporan lapangan mengenai peningkatan mutu alat medis dan pelayanan pasien.',
                'excerpt' => 'Naskah berita yang telah dikirimkan reporter untuk diperiksa oleh redaktur piket.',
                'category_slug' => 'kesehatan',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Submitted,
                'tag_slugs' => ['bireuen', 'kesehatan', 'layanan-publik'],
                'content' => '<p>Naskah liputan kesehatan telah diajukan ke meja penyuntingan redaksi.</p>',
            ],
            [
                'slug' => 'in-review-evaluasi-efektivitas-program-pelatihan-kerja',
                'title' => '[IN REVIEW] Evaluasi Efektivitas Program Pelatihan Keterampilan Kerja Remaja',
                'subtitle' => 'Pemeriksaan naskah analisis evaluasi lulusan balai latihan kerja.',
                'excerpt' => 'Artikel sedang dalam proses penelaahan substansi dan kebahasaan oleh penyunting.',
                'category_slug' => 'nasional',
                'content_type' => ArticleType::Analysis,
                'status' => ArticleStatus::InReview,
                'tag_slugs' => ['pendidikan', 'generasi-muda'],
                'content' => '<p>Naskah dalam proses review mendalam oleh Redaktur Pelaksana.</p>',
            ],
            [
                'slug' => 'approved-penataan-kawasan-ruang-terbuka-hijau-kota',
                'title' => '[APPROVED] Penataan Kawasan Ruang Terbuka Hijau demi Kenyamanan Masyarakat',
                'subtitle' => 'Naskah telah disetujui redaksi dan siap dipublikasikan sesuai jadwal.',
                'excerpt' => 'Artikel siap terbit mengenai penambahan taman umum dan arena olahraga warga.',
                'category_slug' => 'nasional',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Approved,
                'tag_slugs' => ['lingkungan', 'masyarakat'],
                'content' => '<p>Naskah berita ini telah disetujui dan siap diterbitkan secara publik.</p>',
            ],
            [
                'slug' => 'scheduled-peluncuran-portal-layanan-informasi-terpadu',
                'title' => '[SCHEDULED] Peluncuran Portal Layanan Informasi dan Pengaduan Warga Terpadu',
                'subtitle' => 'Artikel dijadwalkan terbit otomatis pada jadwal mendatang.',
                'excerpt' => 'Pengumuman peluncuran aplikasi kanal pengaduan publik yang terkoneksi langsung dengan tim respon cepat.',
                'category_slug' => 'nasional',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Scheduled,
                'tag_slugs' => ['layanan-publik', 'transformasi-digital'],
                'content' => '<p>Naskah berita dijadwalkan terbit otomatis sesuai waktu tayang yang dikonfigurasi.</p>',
            ],
            [
                'slug' => 'archived-laporan-arsip-kegiatan-tahun-lalu',
                'title' => '[ARCHIVED] Arsip Catatan Ringkasan Perkembangan Infrastruktur Publik Tahun Lalu',
                'subtitle' => 'Dokumen berita terdahulu yang telah diarsipkan dari peredaran halaman depan.',
                'excerpt' => 'Catatan liputan terdahulu yang disimpan dalam arsip dokumentasi redaksi.',
                'category_slug' => 'nasional',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Archived,
                'tag_slugs' => ['layanan-publik'],
                'content' => '<p>Artikel ini berstatus arsip dan tidak ditampilkan pada indeks utama public.</p>',
            ],
            [
                'slug' => 'tantangan-dan-peluang-keamanan-data-di-era-layanan-digital-terpadu',
                'title' => 'Tantangan dan Peluang Keamanan Data di Era Layanan Digital Terpadu Modern',
                'subtitle' => 'Penguatan infrastruktur enkripsi data pribadi menjadi syarat mutlak kepercayaan publik.',
                'excerpt' => 'Analis keamanan siber tekankan pentingnya audit berkala dan pelatihan kesadaran privasi bagi pengelola data.',
                'category_slug' => 'teknologi',
                'content_type' => ArticleType::Analysis,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['keamanan-siber', 'teknologi-digital'],
                'content' => '<p>Seiring luasnya pemanfaatan platform elektronik, pengelolaan data pribadi publik menuntut standar keamanan berlapis.</p>',
            ],
            [
                'slug' => 'upaya-peningkatan-kualitas-pendidikan-vokasi-di-wilayah-daerah',
                'title' => 'Upaya Peningkatan Kualitas Pendidikan Vokasi dan Keterampilan Kerja di Daerah',
                'subtitle' => 'Kerja sama balai latihan dengan industri lokal tingkatkan penyerapan lulusan sekolah kejuruan.',
                'excerpt' => 'Pendidikan vokasi yang adaptif dengan kebutuhan industri lokal terbukti mempercepat kemandirian ekonomi pemuda.',
                'category_slug' => 'nasional',
                'content_type' => ArticleType::News,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['pendidikan', 'generasi-muda'],
                'content' => '<p>Pelatihan berbasis praktik terapan membantu lulusan muda menguasai keahlian yang dibutuhkan pasar kerja modern.</p>',
            ],
            [
                'slug' => 'perkembangan-sektor-pariwisata-berbasis-komunitas-di-aceh',
                'title' => 'Perkembangan Sektor Ekowisata Berbasis Komunitas Lokal dan Pelestarian Alam di Aceh',
                'subtitle' => 'Pengelolaan potensi wisata alam berbasis masyarakat ciptakan lapangan kerja baru.',
                'excerpt' => 'Ekowisata yang mengedepankan kelestarian lingkungan dan budaya lokal mendapat respon positif dari wisatawan.',
                'category_slug' => 'lifestyle',
                'content_type' => ArticleType::Feature,
                'status' => ArticleStatus::Published,
                'tag_slugs' => ['aceh', 'lingkungan', 'budaya'],
                'content' => '<p>Pendekatan ekowisata berbasis warga terbukti efektif dalam mempromosikan pesona alam sekaligus menjaga kebersihan lingkungan.</p>',
            ],
        ];
    }
}
