<?php

namespace App\Services;

class DemoNewsService
{
    /**
     * Get active breaking news items.
     *
     * @return array<int, array{headline: string, time: string, url: string}>
     */
    public function getBreakingNews(): array
    {
        return [
            [
                'headline' => 'Pemerintah Luncurkan Kerangka Kerja Nasional Akselerasi Kecerdasan Buatan dan Transformasi Digital 2026',
                'time' => '10 menit yang lalu',
                'url' => route('news.show', ['slug' => 'transformasi-digital-dan-ai-nasional']),
            ],
            [
                'headline' => 'Indeks Harga Saham Gabungan Menguat 1,4% Ditopang Lonjakan Saham Sektor Teknologi dan Energi Bersih',
                'time' => '25 menit yang lalu',
                'url' => route('news.show', ['slug' => 'ihsg-menguat-sektor-teknologi']),
            ],
            [
                'headline' => 'KTT Kerja Sama Ekonomi Regional Hasilkan Kesepakatan Standardisasi Sistem Pembayaran Lintas Batas',
                'time' => '45 menit yang lalu',
                'url' => route('news.show', ['slug' => 'ktt-ekonomi-regional-pembayaran-digital']),
            ],
        ];
    }

    /**
     * Get categories list.
     *
     * @return array<string, array{name: string, slug: string, description: string}>
     */
    public function getCategories(): array
    {
        return [
            'nasional' => [
                'name' => 'Nasional',
                'slug' => 'nasional',
                'description' => 'Kabar berita terkini, kebijakan publik, dan dinamika peristiwa di seluruh pelosok Indonesia.',
            ],
            'politik' => [
                'name' => 'Politik',
                'slug' => 'politik',
                'description' => 'Analisis mendalam, dinamika parlemen, dan perkembangan tata kelola pemerintahan negara.',
            ],
            'ekonomi' => [
                'name' => 'Ekonomi & Bisnis',
                'slug' => 'ekonomi',
                'description' => 'Informasi pasar modal, moneter, investasi, perbankan, dan perkembangan industri global.',
            ],
            'teknologi' => [
                'name' => 'Teknologi',
                'slug' => 'teknologi',
                'description' => 'Inovasi kecerdasan buatan, keamanan siber, startup, gadget, dan transformasi digital.',
            ],
            'olahraga' => [
                'name' => 'Olahraga',
                'slug' => 'olahraga',
                'description' => 'Kabar pertandingan sepak bola, bulu tangkis, balap, dan turnamen olahraga internasional.',
            ],
            'hiburan' => [
                'name' => 'Hiburan',
                'slug' => 'hiburan',
                'description' => 'Dunia perfilman, musik, seni pertunjukan, dan budaya populer terkini.',
            ],
            'lifestyle' => [
                'name' => 'Gaya Hidup',
                'slug' => 'lifestyle',
                'description' => 'Tren kesehatan, arsitektur, kuliner, perjalanan wisata, dan gaya hidup berkelanjutan.',
            ],
            'internasional' => [
                'name' => 'Internasional',
                'slug' => 'internasional',
                'description' => 'Dinamika geopolitik, hubungan diplomatik, dan peristiwa penting dunia.',
            ],
        ];
    }

    /**
     * Get the main lead story.
     *
     * @return array<string, mixed>
     */
    public function getLeadStory(): array
    {
        return [
            'slug' => 'peta-jalan-indonesia-digital-2030',
            'title' => 'Peta Jalan Indonesia Digital 2030: Transformasi Sektor Publik dan Efisiensi Industri Nasional',
            'subtitle' => 'Pemerintah bersama pemangku kepentingan industri teknologi luncurkan pedoman integrasi AI dan kedaulatan data nasional.',
            'category' => 'Teknologi',
            'category_slug' => 'teknologi',
            'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1200&auto=format&fit=crop',
            'excerpt' => 'Pemerintah bersama para pemangku kepentingan industri teknologi resmi meluncurkan kerangka kerja strategis untuk mempercepat adopsi kecerdasan buatan, penguatan keamanan siber terpadu, dan kedaulatan data nasional menuju kemandirian ekonomi digital.',
            'author' => 'Budi Santoso',
            'author_role' => 'Redaktur Utama Teknologi',
            'author_avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=200&auto=format&fit=crop',
            'published_at' => '28 Agustus 2026, 14.30 WIB',
            'reading_time' => 4,
            'is_breaking' => false,
            'is_featured' => true,
        ];
    }

    /**
     * Get supporting top stories for hero section.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getSupportingStories(): array
    {
        return [
            [
                'slug' => 'pertumbuhan-ekonomi-kuartal-kedua',
                'title' => 'Pertumbuhan Ekonomi Kuartal II Melampaui Ekspektasi Analis, Didorong Konsumsi Domestik',
                'category' => 'Ekonomi & Bisnis',
                'category_slug' => 'ekonomi',
                'image' => 'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?q=80&w=600&auto=format&fit=crop',
                'author' => 'Dian Pratama',
                'published_at' => '28 Agustus 2026, 13.15 WIB',
                'reading_time' => 3,
            ],
            [
                'slug' => 'reformasi-tata-kelola-energi-terbarukan',
                'title' => 'Reformasi Tata Kelola Energi Terbarukan: Investasi Pembangkit Surya Capai Rekor Baru',
                'category' => 'Nasional',
                'category_slug' => 'nasional',
                'image' => 'https://images.unsplash.com/photo-1509391365360-2e959784a276?q=80&w=600&auto=format&fit=crop',
                'author' => 'Siti Rahmawati',
                'published_at' => '28 Agustus 2026, 12.45 WIB',
                'reading_time' => 3,
            ],
            [
                'slug' => 'diplomasi-regional-ktt-asean',
                'title' => 'Diplomasi Regional: KTT ASEAN Bahas Stabilitas Pasifik dan Integrasi Sistem Finansial Digital',
                'category' => 'Internasional',
                'category_slug' => 'internasional',
                'image' => 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?q=80&w=600&auto=format&fit=crop',
                'author' => 'Rizky Alamsyah',
                'published_at' => '28 Agustus 2026, 11.20 WIB',
                'reading_time' => 4,
            ],
        ];
    }

    /**
     * Get latest news feed.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getLatestNews(int $limit = 8): array
    {
        $all = [
            [
                'slug' => 'infrastruktur-kereta-cepat-tahap-lanjutan',
                'title' => 'Infrastruktur Kereta Cepat Tahap Lanjutan Memasuki Uji Kelayakan Rute Antar-Kota Baru',
                'category' => 'Nasional',
                'category_slug' => 'nasional',
                'image' => 'https://images.unsplash.com/photo-1532105956626-9569c03602f6?q=80&w=600&auto=format&fit=crop',
                'excerpt' => 'Kementerian Perhubungan memastikan studi teknis perluasan rute jaringan transportasi massal berkecepatan tinggi selesai tepat waktu.',
                'author' => 'Ahmad Fauzi',
                'published_at' => '28 Agustus 2026, 14.00 WIB',
                'reading_time' => 3,
            ],
            [
                'slug' => 'riset-ai-sektor-kesehatan',
                'title' => 'Riset Terbaru: Integrasi AI di Sektor Kesehatan Tingkatkan Akurasi Diagnosis Radiologi Hingga 40%',
                'category' => 'Teknologi',
                'category_slug' => 'teknologi',
                'image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?q=80&w=600&auto=format&fit=crop',
                'excerpt' => 'Studi kolaboratif universitas terkemuka membuktikan sistem pemindaian cerdas mampu mempercepat penanganan pasien darurat secara signifikan.',
                'author' => 'Dr. Maya Safitri',
                'published_at' => '28 Agustus 2026, 13.30 WIB',
                'reading_time' => 4,
            ],
            [
                'slug' => 'peluncuran-satelit-konektivitas-daerah-terluar',
                'title' => 'Peluncuran Satelit Komunikasi Generasi Baru Buka Akses Internet Cepat Wilayah Terluar',
                'category' => 'Teknologi',
                'category_slug' => 'teknologi',
                'image' => 'https://images.unsplash.com/photo-1517976487588-468351586a11?q=80&w=600&auto=format&fit=crop',
                'excerpt' => 'Infrastruktur konstelasi satelit orbit rendah kini mulai menjangkau ribuan titik sekolah dan fasilitas kesehatan di kawasan kepulauan.',
                'author' => 'Hendra Wijaya',
                'published_at' => '28 Agustus 2026, 12.15 WIB',
                'reading_time' => 3,
            ],
            [
                'slug' => 'festival-kebudayaan-nusantara-2026',
                'title' => 'Festival Kebudayaan Nusantara 2026 Hadirkan Ribuan Seniman dan Destinasi Unggulan',
                'category' => 'Gaya Hidup',
                'category_slug' => 'lifestyle',
                'image' => 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?q=80&w=600&auto=format&fit=crop',
                'excerpt' => 'Perhelatan akbar tahunan ini diproyeksikan menarik kunjungan jutaan wisatawan domestik maupun mancanegara.',
                'author' => 'Nurul Hidayah',
                'published_at' => '28 Agustus 2026, 11.00 WIB',
                'reading_time' => 2,
            ],
            [
                'slug' => 'kebijakan-fiskal-stimulus-umkm',
                'title' => 'Paket Kebijakan Fiskal Baru Perkuat Akses Permodalan dan Digitalisasi UMKM Ekspor',
                'category' => 'Ekonomi & Bisnis',
                'category_slug' => 'ekonomi',
                'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=600&auto=format&fit=crop',
                'excerpt' => 'Kemudahan perpajakan dan subsidi bunga pinjaman diarahkan untuk mempercepat penetrasi produk lokal di pasar internasional.',
                'author' => 'Dian Pratama',
                'published_at' => '28 Agustus 2026, 10.30 WIB',
                'reading_time' => 4,
            ],
            [
                'slug' => 'konservasi-hutan-tropis-dan-kredit-karbon',
                'title' => 'Inisiatif Konservasi Hutan Tropis Nusantara Berhasil Bukukan 15 Juta Ton Kredit Karbon',
                'category' => 'Nasional',
                'category_slug' => 'nasional',
                'image' => 'https://images.unsplash.com/photo-1511497584788-87676104235f?q=80&w=600&auto=format&fit=crop',
                'excerpt' => 'Mekanisme bursa karbon nasional menarik minat korporasi multinasional dalam mendukung restorasi ekosistem gambut.',
                'author' => 'Siti Rahmawati',
                'published_at' => '28 Agustus 2026, 09.45 WIB',
                'reading_time' => 5,
            ],
            [
                'slug' => 'inovasi-baterai-kendaraan-listrik',
                'title' => 'Laboratorium Nasional Berhasil Uji Material Baterai Padat Pengisian Kilat 5 Menit',
                'category' => 'Teknologi',
                'category_slug' => 'teknologi',
                'image' => 'https://images.unsplash.com/photo-1558441719-8b489c6f2d4b?q=80&w=600&auto=format&fit=crop',
                'excerpt' => 'Kepadatan energi meningkat 60% dibandingkan sel baterai konvensional dengan tingkat keamanan termal optimal.',
                'author' => 'Budi Santoso',
                'published_at' => '28 Agustus 2026, 09.00 WIB',
                'reading_time' => 3,
            ],
            [
                'slug' => 'persiapan-timnas-piala-dunia',
                'title' => 'Pelatih Kepala Matangkan Formasi Taktik Jelang Putaran Final Kualifikasi Utama',
                'category' => 'Olahraga',
                'category_slug' => 'olahraga',
                'image' => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=600&auto=format&fit=crop',
                'excerpt' => 'Komposisi pemain muda dan pemain berpengalaman di liga luar negeri menunjukkan kekompakan tinggi dalam laga uji coba.',
                'author' => 'Bayu Wicaksono',
                'published_at' => '28 Agustus 2026, 08.15 WIB',
                'reading_time' => 3,
            ],
        ];

        return array_slice($all, 0, $limit);
    }

    /**
     * Get trending news ranking.
     *
     * @return array<int, array{rank: string, title: string, views: string, slug: string}>
     */
    public function getTrendingNews(): array
    {
        return [
            [
                'rank' => '01',
                'title' => 'Strategi Bank Indonesia Menjaga Stabilitas Nilai Tukar di Tengah Volatilitas Global',
                'views' => '24.800',
                'slug' => 'pertumbuhan-ekonomi-kuartal-kedua',
            ],
            [
                'rank' => '02',
                'title' => 'Daftar 10 Kota Paling Layak Huni di Indonesia Berdasarkan Indeks Keberlanjutan 2026',
                'views' => '19.400',
                'slug' => 'festival-kebudayaan-nusantara-2026',
            ],
            [
                'rank' => '03',
                'title' => 'Peta Jalan Indonesia Digital 2030: Menuju Ekosistem AI dan Kedaulatan Data',
                'views' => '16.700',
                'slug' => 'peta-jalan-indonesia-digital-2030',
            ],
            [
                'rank' => '04',
                'title' => 'Menkeu Paparkan Rincian Alokasi Anggaran Riset Sains dan Teknologi Nasional',
                'views' => '14.200',
                'slug' => 'kebijakan-fiskal-stimulus-umkm',
            ],
            [
                'rank' => '05',
                'title' => 'Inovasi Baterai Padat Nasional Selesaikan Uji Sertifikasi Standar Internasional',
                'views' => '11.900',
                'slug' => 'inovasi-baterai-kendaraan-listrik',
            ],
        ];
    }

    /**
     * Get opinion columns.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getOpinionColumns(): array
    {
        return [
            [
                'title' => 'Menavigasi Kedaulatan Digital di Tengah Dominasi Model Kecerdasan Buatan Global',
                'author' => 'Prof. Dr. Irwan Susanto',
                'author_role' => 'Guru Besar Ilmu Komputer & Kebijakan Digital',
                'author_avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=200&auto=format&fit=crop',
                'published_at' => '28 Agustus 2026',
                'slug' => 'menavigasi-kedaulatan-digital-ai',
            ],
            [
                'title' => 'Ketahanan Pangan dan Inovasi Agriteknologi: Solusi Nyata Menghadapi Perubahan Iklim',
                'author' => 'Dr. Lestari Purnomo',
                'author_role' => 'Pakar Ekonomi Pertanian & Pembangunan Berkelanjutan',
                'author_avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?q=80&w=200&auto=format&fit=crop',
                'published_at' => '27 Agustus 2026',
                'slug' => 'ketahanan-pangan-agriteknologi',
            ],
            [
                'title' => 'Masa Depan Jurnalisme Berkualitas di Era Kebisingan Algoritma dan Media Sosial',
                'author' => 'Bambang Wicaksono',
                'author_role' => 'Jurnalis Senior & Pengamat Media Massa',
                'author_avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=200&auto=format&fit=crop',
                'published_at' => '26 Agustus 2026',
                'slug' => 'masa-depan-jurnalisme-berkualitas',
            ],
        ];
    }

    /**
     * Get editor's choice curated stories.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getEditorsChoice(): array
    {
        return [
            [
                'slug' => 'investigasi-dana-hijau-konservasi',
                'title' => 'Laporan Khusus: Melacak Efektivitas Investasi Hijau di Kawasan Konservasi Tropis',
                'category' => 'Liputan Khusus',
                'category_slug' => 'nasional',
                'image' => 'https://images.unsplash.com/photo-1511497584788-87676104235f?q=80&w=600&auto=format&fit=crop',
                'excerpt' => 'Bagaimana skema pendanaan karbon internasional mulai mengubah wajah perekonomian masyarakat lokal secara berkelanjutan.',
                'author' => 'Tim Investigasi TopNews',
                'published_at' => '28 Agustus 2026',
                'reading_time' => 7,
            ],
            [
                'slug' => 'wawancara-eksklusif-gubernur-bank-sentral',
                'title' => 'Wawancara Eksklusif: Visi Gubernur Bank Sentral Menjaga Resiliensi Moneter Asia',
                'category' => 'Wawancara',
                'category_slug' => 'ekonomi',
                'image' => 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?q=80&w=600&auto=format&fit=crop',
                'excerpt' => 'Langkah-langkah strategis dalam menyeimbangkan likuiditas pasar valas dan dorongan kredit produktif.',
                'author' => 'Raden Kusuma',
                'published_at' => '27 Agustus 2026',
                'reading_time' => 5,
            ],
            [
                'slug' => 'desain-kota-pintar-berkelanjutan',
                'title' => 'Eksplorasi Arsitektur Berkelanjutan: Masa Depan Desain Kota Pintar Rendah Emisi',
                'category' => 'Arsitektur',
                'category_slug' => 'lifestyle',
                'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=600&auto=format&fit=crop',
                'excerpt' => 'Integrasi material ramah lingkungan dan sistem sirkulasi alami pada gedung pencakar langit modern.',
                'author' => 'Jessica Chandra',
                'published_at' => '26 Agustus 2026',
                'reading_time' => 4,
            ],
        ];
    }

    /**
     * Get article by slug for article prototype reading page.
     *
     * @return array<string, mixed>|null
     */
    public function getArticleBySlug(string $slug): ?array
    {
        $lead = $this->getLeadStory();
        if ($slug === $lead['slug']) {
            return $lead;
        }

        if ($slug === 'transformasi-digital-dorong-pertumbuhan-ekonomi-kreatif-indonesia-2026') {
            return array_merge($lead, [
                'slug' => $slug,
                'title' => 'Transformasi Digital Dorong Pertumbuhan Ekonomi Kreatif Indonesia 2026',
            ]);
        }

        foreach (array_merge($this->getSupportingStories(), $this->getLatestNews(20)) as $item) {
            if ($item['slug'] === $slug) {
                return array_merge($lead, $item, [
                    'subtitle' => 'Laporan perkembangan mendalam dan komprehensif dari redaksi TopNews.',
                ]);
            }
        }

        return null;
    }

    /**
     * Search articles by query.
     *
     * @return array<int, array<string, mixed>>
     */
    public function searchArticles(?string $query): array
    {
        if (empty(trim($query ?? ''))) {
            return [];
        }

        $q = strtolower(trim($query));
        $all = array_merge([$this->getLeadStory()], $this->getSupportingStories(), $this->getLatestNews(10));

        $results = [];
        foreach ($all as $item) {
            if (
                str_contains(strtolower($item['title']), $q) ||
                str_contains(strtolower($item['category'] ?? ''), $q) ||
                str_contains(strtolower($item['excerpt'] ?? ''), $q)
            ) {
                $results[] = $item;
            }
        }

        // If no strict match found in demo data, return top 3 relevant sample articles so UI state is rich
        if (empty($results)) {
            return array_slice($this->getLatestNews(3), 0, 3);
        }

        return $results;
    }
}
