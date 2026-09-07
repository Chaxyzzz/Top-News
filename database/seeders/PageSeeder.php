<?php

namespace Database\Seeders;

use App\Enums\PageStatus;
use App\Enums\PageType;
use App\Enums\UserStatus;
use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::withTrashed()->where('username', 'zakky77')->first();
        if (! $admin) {
            $admin = User::create([
                'name' => 'Zakky Mubaraq',
                'username' => 'Zakky77',
                'email' => 'topnews90@gmail.com',
                'password' => 'Mikaliso77',
                'account_type' => 'staff',
                'status' => UserStatus::Active,
            ]);
            $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], ['label' => 'Super Admin', 'is_system' => true]);
            $admin->roles()->sync([$superAdminRole->id]);
        }
        $adminId = $admin->id;

        $pages = [
            [
                'page_type' => PageType::About,
                'title' => 'Tentang TopNews',
                'slug' => 'tentang-kami',
                'excerpt' => 'Mengenal TopNews, visi jurnalisme independen, standar etika redaksi, dan komitmen penyajian informasi terpercaya bagi publik.',
                'content' => '<h2>Misi Jurnalisme Kami</h2>
<p>TopNews didirikan sebagai portal berita digital independen yang berdedikasi menyajikan fakta secara akurat, lugas, dan mendalam. Di tengah arus informasi yang serba cepat, kami menempatkan verifikasi berlapis dan objektivitas di atas segalanya.</p>
<h2>Prinsip Independensi</h2>
<p>Redaksi TopNews bebas dari intervensi kepentingan politik, kekuasaan, maupun tekanan pemodal. Kebijakan keredaksian dipimpin sepenuhnya oleh dewan redaksi dengan menjunjung tinggi kepentingan publik dan hak masyarakat atas informasi yang benar.</p>
<h2>Teknologi dan Kecepatan</h2>
<p>Dengan mengadopsi arsitektur media digital modern, TopNews dirancang memberikan pengalaman membaca yang cepat, bersih dari kekacauan visual, serta aksesibel bagi seluruh lapisan masyarakat di berbagai perangkat.</p>',
                'status' => PageStatus::Published,
                'published_at' => now(),
            ],
            [
                'page_type' => PageType::EditorialTeam,
                'title' => 'Susunan Dewan Redaksi & Manajemen',
                'slug' => 'dewan-redaksi',
                'excerpt' => 'Struktur kepemimpinan redaksi, jajaran editor pelaksana, dan tim jurnalis independen TopNews.',
                'content' => '<p>Manajemen keredaksian TopNews dipimpin oleh para jurnalis berpengalaman yang memiliki komitmen teguh terhadap kode etik dan integritas pers nasional.</p>',
                'status' => PageStatus::Published,
                'published_at' => now(),
            ],
            [
                'page_type' => PageType::EditorialGuidelines,
                'title' => 'Pedoman & Standar Etika Redaksi',
                'slug' => 'pedoman-redaksi',
                'excerpt' => 'Standar etika peliputan, verifikasi fakta, perlindungan narasumber, dan transparansi koreksi berita TopNews.',
                'content' => '<h2>Kepatuhan Kode Etik Jurnalistik</h2>
<p>Seluruh wartawan, editor, dan kontributor TopNews wajib mematuhi Kode Etik Jurnalistik (KEJ) serta Pedoman Pemberitaan Media Siber (PPMS) yang ditetapkan Dewan Pers Indonesia.</p>
<h2>Verifikasi dan Keberimbangan (Cover Both Sides)</h2>
<p>Setiap naskah berita yang memuat tuduhan, sengketa, atau peristiwa kontroversial wajib menyertakan konfirmasi berimbang dari seluruh pihak terkait. Fakta dan opini dipisahkan secara tegas dalam penulisan.</p>
<h2>Kebijakan Koreksi dan Ralat</h2>
<p>Apabila terdapat kekeliruan data atau fakta pada berita yang telah diterbitkan, redaksi akan segera memuat ralat atau pembaruan berita dengan mencantumkan catatan koreksi secara transparan pada bagian bawah artikel.</p>
<h2>Pemisahan Konten Komersial</h2>
<p>Redaksi memisahkan secara tegas antara karya jurnalistik dan materi promosi/iklan. Seluruh artikel bersponsor atau advertorial ditandai dengan jelas menggunakan label sponsor dan tidak memengaruhi independensi pemberitaan.</p>',
                'status' => PageStatus::Published,
                'published_at' => now(),
            ],
            [
                'page_type' => PageType::PrivacyPolicy,
                'title' => 'Kebijakan Privasi',
                'slug' => 'kebijakan-privasi',
                'excerpt' => 'Kebijakan privasi dan perlindungan data pribadi pengunjung serta pembaca portal berita TopNews.',
                'content' => '<h2>Komitmen Privasi</h2>
<p>TopNews menghargai dan berkomitmen melindungi privasi data pribadi setiap pengunjung, pembaca, dan pelanggan buletin kami. Halaman ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan menjaga informasi Anda.</p>
<h2>Informasi yang Kami Kumpulkan</h2>
<p>Kami hanya mengumpulkan data yang relevan dan dibutuhkan untuk penyediaan layanan, seperti alamat email saat Anda mendaftar buletin berita atau nama dan kontak saat Anda mengirimkan pesan melalui meja redaksi.</p>
<h2>Keamanan Data</h2>
<p>Kami tidak pernah menjual, menyewakan, atau membagikan data pribadi Anda kepada pihak ketiga untuk kepentingan komersial tanpa persetujuan eksplisit dari Anda, kecuali diwajibkan oleh ketentuan perundang-undangan yang berlaku.</p>',
                'status' => PageStatus::Published,
                'published_at' => now(),
            ],
            [
                'page_type' => PageType::Terms,
                'title' => 'Syarat & Ketentuan Penggunaan',
                'slug' => 'syarat-ketentuan',
                'excerpt' => 'Ketentuan penggunaan konten, hak cipta karya jurnalistik, dan tata tertib interaksi pembaca di TopNews.',
                'content' => '<h2>Hak Cipta Konten</h2>
<p>Seluruh artikel, foto, video, grafik, dan materi editorial yang dipublikasikan di TopNews dilindungi oleh undang-undang hak cipta. Pengutipan materi untuk keperluan edukasi dan referensi wajib mencantumkan kredit dan tautan aktif ke sumber asli.</p>
<h2>Komentar dan Interaksi Pembaca</h2>
<p>Pembaca dilarang mengirimkan komentar yang mengandung ujaran kebencian, fitnah, diskriminasi SARA, pornografi, maupun pelanggaran hukum lainnya. Meja redaksi berhak memoderasi dan menghapus komentar yang melanggar ketentuan.</p>',
                'status' => PageStatus::Published,
                'published_at' => now(),
            ],
            [
                'page_type' => PageType::Disclaimer,
                'title' => 'Sanggahan (Disclaimer)',
                'slug' => 'disclaimer',
                'excerpt' => 'Batasan tanggung jawab hukum mengenai materi berita, opini penulis tamu, dan tautan pihak ketiga.',
                'content' => '<h2>Opini dan Kolom Penulis Tamu</h2>
<p>Pandangan dan opini yang tertuang dalam rubrik Opini, Kolom, atau Wacana merupakan tanggung jawab pribadi penulis yang bersangkutan dan tidak selalu mencerminkan sikap resmi institusi TopNews.</p>
<h2>Tautan ke Situs Eksternal</h2>
<p>TopNews dapat memuat tautan menuju situs pihak ketiga sebagai referensi pelengkap informasi. Kami tidak bertanggung jawab atas isi atau kebijakan privasi pada situs-situs eksternal tersebut.</p>',
                'status' => PageStatus::Published,
                'published_at' => now(),
            ],
            [
                'page_type' => PageType::AdvertisingInfo,
                'title' => 'Informasi Kerja Sama & Iklan',
                'slug' => 'pasang-iklan',
                'excerpt' => 'Peluang kolaborasi promosi, display ads, native sponsorship, dan publikasi advertorial di portal TopNews.',
                'content' => '<h2>Jangkauan dan Audiens TopNews</h2>
<p>TopNews diakses setiap hari oleh pembaca kritis, pengambil keputusan, akademisi, dan masyarakat umum di seluruh Indonesia. Kami menawarkan berbagai format promosi digital yang tertarget dan transparan.</p>
<h2>Format Iklan yang Tersedia</h2>
<ul>
    <li><strong>Display Banners:</strong> Leaderboard, In-article display, dan Sidebar MPU standar.</li>
    <li><strong>Advertorial & Sponsored Content:</strong> Ulasan produk atau inisiatif korporasi dengan tanda sponsor yang jelas dan standar narasi informatif.</li>
    <li><strong>Buletin Kemitraan:</strong> Slot sponsorship terkurasi di buletin pagi TopNews Morning Brief.</li>
</ul>
<h2>Kontak Tim Periklanan</h2>
<p>Untuk mengajukan penawaran, media kit, atau proposal kerja sama komersial, silakan hubungi tim bisnis kami melalui email <strong>iklan@topnews.id</strong> atau melalui formulir kontak redaksi.</p>',
                'status' => PageStatus::Published,
                'published_at' => now(),
            ],
            [
                'page_type' => PageType::Contact,
                'title' => 'Kontak Redaksi & Manajemen',
                'slug' => 'kontak',
                'excerpt' => 'Hubungi meja redaksi TopNews untuk pengiriman siaran pers, hak jawab, kritik, atau kerja sama bisnis.',
                'content' => '<p>Punya pertanyaan, informasi peristiwa terkini, permintaan hak jawab, atau tawaran kerja sama? Tim redaksi dan operasional kami siap menyambut komunikasi Anda.</p>',
                'status' => PageStatus::Published,
                'published_at' => now(),
            ],
        ];

        foreach ($pages as $p) {
            Page::updateOrCreate(
                ['page_type' => $p['page_type']->value],
                [
                    'title' => $p['title'],
                    'slug' => $p['slug'],
                    'excerpt' => $p['excerpt'],
                    'content' => $p['content'],
                    'status' => $p['status'],
                    'published_at' => $p['published_at'],
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );
        }
    }
}
