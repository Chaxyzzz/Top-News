<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Permissions
        $permissions = [
            // Dashboard
            ['name' => 'dashboard.view', 'label' => 'Melihat Panel Dashboard', 'group' => 'dashboard'],

            // Users Management
            ['name' => 'users.view', 'label' => 'Melihat Daftar Pengguna', 'group' => 'users'],
            ['name' => 'users.create', 'label' => 'Menambah Pengguna Baru', 'group' => 'users'],
            ['name' => 'users.update', 'label' => 'Mengubah Data Pengguna', 'group' => 'users'],
            ['name' => 'users.suspend', 'label' => 'Menangguhkan / Mengaktifkan Pengguna', 'group' => 'users'],
            ['name' => 'users.delete', 'label' => 'Menghapus Pengguna', 'group' => 'users'],

            // Roles & Permissions Management
            ['name' => 'roles.view', 'label' => 'Melihat Matriks Role & Izin', 'group' => 'roles'],
            ['name' => 'roles.manage', 'label' => 'Mengelola Izin Role', 'group' => 'roles'],

            // Audit Logs
            ['name' => 'audit.view', 'label' => 'Melihat Catatan Audit Keamanan', 'group' => 'audit'],

            // Profile
            ['name' => 'profile.update', 'label' => 'Mengubah Profil Sendiri', 'group' => 'profile'],

            // Articles & Newsroom Editorial Workflow (Phase 04)
            ['name' => 'articles.view', 'label' => 'Melihat Daftar Artikel Sendiri', 'group' => 'articles'],
            ['name' => 'articles.view_all', 'label' => 'Melihat Seluruh Artikel Newsroom', 'group' => 'articles'],
            ['name' => 'articles.create', 'label' => 'Membuat Draf Artikel Baru', 'group' => 'articles'],
            ['name' => 'articles.update_own', 'label' => 'Mengubah Artikel Sendiri', 'group' => 'articles'],
            ['name' => 'articles.update_all', 'label' => 'Mengubah Seluruh Artikel Staf', 'group' => 'articles'],
            ['name' => 'articles.delete_own', 'label' => 'Menghapus Draf Sendiri', 'group' => 'articles'],
            ['name' => 'articles.delete_all', 'label' => 'Menghapus Seluruh Artikel', 'group' => 'articles'],
            ['name' => 'articles.submit', 'label' => 'Mengajukan Artikel untuk Review', 'group' => 'articles'],
            ['name' => 'articles.review', 'label' => 'Meninjau Naskah Artikel (Review)', 'group' => 'articles'],
            ['name' => 'articles.request_revision', 'label' => 'Meminta Revisi Naskah ke Penulis', 'group' => 'articles'],
            ['name' => 'articles.approve', 'label' => 'Menyetujui Naskah Artikel (Approval)', 'group' => 'articles'],
            ['name' => 'articles.schedule', 'label' => 'Menjadwalkan Publikasi Artikel', 'group' => 'articles'],
            ['name' => 'articles.publish', 'label' => 'Menerbitkan Artikel ke Publik', 'group' => 'articles'],
            ['name' => 'articles.unpublish', 'label' => 'Menarik Kembali Artikel Terbit', 'group' => 'articles'],
            ['name' => 'articles.archive', 'label' => 'Mengarsipkan Artikel', 'group' => 'articles'],
            ['name' => 'articles.restore', 'label' => 'Memulihkan Artikel Terarsip/Terhapus', 'group' => 'articles'],
            ['name' => 'articles.assign_author', 'label' => 'Menugaskan / Mengubah Penulis Artikel', 'group' => 'articles'],
            ['name' => 'articles.change_published_slug', 'label' => 'Mengubah URL / Slug Artikel Terbit', 'group' => 'articles'],

            // Categories Management
            ['name' => 'categories.view', 'label' => 'Melihat Daftar Kategori Rubrik', 'group' => 'categories'],
            ['name' => 'categories.create', 'label' => 'Menambah Kategori Baru', 'group' => 'categories'],
            ['name' => 'categories.update', 'label' => 'Mengubah Kategori Rubrik', 'group' => 'categories'],
            ['name' => 'categories.delete', 'label' => 'Menghapus Kategori Rubrik', 'group' => 'categories'],

            // Tags Management
            ['name' => 'tags.view', 'label' => 'Melihat Daftar Tagar Berita', 'group' => 'tags'],
            ['name' => 'tags.create', 'label' => 'Menambah Tagar Baru', 'group' => 'tags'],
            ['name' => 'tags.update', 'label' => 'Mengubah Tagar Berita', 'group' => 'tags'],
            ['name' => 'tags.delete', 'label' => 'Menghapus Tagar Berita', 'group' => 'tags'],

            // Media Management (Phase 07)
            ['name' => 'media.view', 'label' => 'Melihat Galeri Media Library', 'group' => 'media'],
            ['name' => 'media.upload', 'label' => 'Mengunggah Aset Media Baru', 'group' => 'media'],
            ['name' => 'media.update', 'label' => 'Mengubah Metadata Media', 'group' => 'media'],
            ['name' => 'media.delete', 'label' => 'Menghapus Aset Media', 'group' => 'media'],
            ['name' => 'media.manage', 'label' => 'Mengelola Penuh Media Library', 'group' => 'media'],

            // Galleries Management (Phase 07)
            ['name' => 'galleries.view', 'label' => 'Melihat Daftar Galeri Foto', 'group' => 'galleries'],
            ['name' => 'galleries.create', 'label' => 'Membuat Galeri Baru', 'group' => 'galleries'],
            ['name' => 'galleries.update', 'label' => 'Mengubah Galeri & Urutan Foto', 'group' => 'galleries'],
            ['name' => 'galleries.delete', 'label' => 'Menghapus Galeri Foto', 'group' => 'galleries'],

            // Comments & Engagement Moderation (Phase 08)
            ['name' => 'comments.view', 'label' => 'Melihat Daftar Moderasi Komentar', 'group' => 'comments'],
            ['name' => 'comments.moderate', 'label' => 'Menyetujui & Menolak Komentar', 'group' => 'comments'],
            ['name' => 'comments.delete', 'label' => 'Menghapus Komentar Pembaca', 'group' => 'comments'],
            ['name' => 'comments.mark_spam', 'label' => 'Menandai Komentar sebagai Spam', 'group' => 'comments'],

            // Homepage CMS (Phase 09)
            ['name' => 'homepage.view', 'label' => 'Melihat Manajemen Bagian Homepage', 'group' => 'homepage'],
            ['name' => 'homepage.manage', 'label' => 'Mengatur Tata Letak & Bagian Homepage', 'group' => 'homepage'],
            ['name' => 'homepage.curate', 'label' => 'Mengkurasi Berita Pilihan Homepage', 'group' => 'homepage'],

            // Breaking News (Phase 09)
            ['name' => 'breaking_news.view', 'label' => 'Melihat Daftar Breaking News', 'group' => 'breaking_news'],
            ['name' => 'breaking_news.create', 'label' => 'Membuat Breaking News Baru', 'group' => 'breaking_news'],
            ['name' => 'breaking_news.update', 'label' => 'Mengubah Jadwal & Data Breaking News', 'group' => 'breaking_news'],
            ['name' => 'breaking_news.delete', 'label' => 'Menghapus Breaking News', 'group' => 'breaking_news'],
            ['name' => 'breaking_news.publish', 'label' => 'Mengaktifkan / Memublikasikan Breaking News', 'group' => 'breaking_news'],

            // Navigation CMS (Phase 09)
            ['name' => 'navigation.view', 'label' => 'Melihat Pengaturan Menu Navigasi', 'group' => 'navigation'],
            ['name' => 'navigation.manage', 'label' => 'Mengelola & Mengurutkan Item Navigasi', 'group' => 'navigation'],

            // Advertisement Management (Phase 09)
            ['name' => 'ads.view', 'label' => 'Melihat Dasbor & Daftar Periklanan', 'group' => 'ads'],
            ['name' => 'ads.manage', 'label' => 'Mengelola Slot & Kampanye Iklan', 'group' => 'ads'],
            ['name' => 'ads.create', 'label' => 'Membuat Iklan & Kampanye Baru', 'group' => 'ads'],
            ['name' => 'ads.update', 'label' => 'Mengubah Materi & Jadwal Iklan', 'group' => 'ads'],
            ['name' => 'ads.delete', 'label' => 'Menghapus / Menonaktifkan Iklan', 'group' => 'ads'],
            ['name' => 'ads.analytics', 'label' => 'Melihat Metrik & Performa Iklan (CTR)', 'group' => 'ads'],

            // Newsletter Management (Phase 10)
            ['name' => 'newsletter.view', 'label' => 'Melihat Daftar Pelanggan Buletin', 'group' => 'newsletter'],
            ['name' => 'newsletter.manage', 'label' => 'Mengelola Status Pelanggan Buletin', 'group' => 'newsletter'],
            ['name' => 'newsletter.export', 'label' => 'Mengekspor Data Pelanggan Buletin', 'group' => 'newsletter'],

            // Contact Messages / Inbox (Phase 10)
            ['name' => 'contacts.view', 'label' => 'Melihat Kotak Masuk Pesan Publik', 'group' => 'contacts'],
            ['name' => 'contacts.manage', 'label' => 'Mengubah Status & Moderasi Pesan Masuk', 'group' => 'contacts'],
            ['name' => 'contacts.assign', 'label' => 'Menugaskan Pesan ke Staf Redaksi', 'group' => 'contacts'],
            ['name' => 'contacts.delete', 'label' => 'Menghapus / Mengarsipkan Pesan Masuk', 'group' => 'contacts'],

            // Static Pages CMS (Phase 10)
            ['name' => 'pages.view', 'label' => 'Melihat Daftar Halaman Statis', 'group' => 'pages'],
            ['name' => 'pages.create', 'label' => 'Membuat Halaman Statis Baru', 'group' => 'pages'],
            ['name' => 'pages.update', 'label' => 'Mengubah Konten Halaman Statis', 'group' => 'pages'],
            ['name' => 'pages.publish', 'label' => 'Menerbitkan Halaman Statis', 'group' => 'pages'],
            ['name' => 'pages.delete', 'label' => 'Menghapus Halaman Kustom', 'group' => 'pages'],

            // Site Settings (Phase 10)
            ['name' => 'settings.view', 'label' => 'Melihat Konfigurasi Pengaturan Situs', 'group' => 'settings'],
            ['name' => 'settings.update', 'label' => 'Mengubah Pengaturan Sistem & Situs', 'group' => 'settings'],
            ['name' => 'settings.branding', 'label' => 'Mengubah Identitas Brand & Logo', 'group' => 'settings'],
            ['name' => 'settings.editorial', 'label' => 'Mengubah Pengaturan Standar Redaksi', 'group' => 'settings'],
            ['name' => 'settings.seo', 'label' => 'Mengubah Default Metadata & SEO Situs', 'group' => 'settings'],

            // Editorial Team Visibility (Phase 10)
            ['name' => 'editorial_team.manage', 'label' => 'Mengelola Susunan Tim Redaksi Publik', 'group' => 'editorial_team'],

            // Analytics & Performance (Phase 11)
            ['name' => 'analytics.view', 'label' => 'Melihat Dashboard Analitik & Ringkasan Performa', 'group' => 'analytics'],
            ['name' => 'analytics.content', 'label' => 'Melihat Analitik Performa Konten & Penulis', 'group' => 'analytics'],
            ['name' => 'analytics.traffic', 'label' => 'Melihat Analitik Sumber Trafik & Perangkat', 'group' => 'analytics'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']],
                ['label' => $perm['label'], 'group' => $perm['group']]
            );
        }

        // 2. Create Roles with Granular Permission Sets
        $allPermissions = Permission::pluck('name')->all();

        $roles = [
            'super_admin' => [
                'label' => 'Super Admin',
                'description' => 'Akses penuh dan tidak terbatas ke seluruh konfigurasi sistem, audit, dan hak kelola pengguna tertinggi.',
                'is_system' => true,
                'permissions' => $allPermissions,
            ],
            'admin' => [
                'label' => 'Admin',
                'description' => 'Akses pengelolaan operasional staf, audit log, taksonomi konten, dan pemantauan pengguna sistem.',
                'is_system' => true,
                'permissions' => [
                    'dashboard.view',
                    'users.view',
                    'users.create',
                    'users.update',
                    'users.suspend',
                    'roles.view',
                    'audit.view',
                    'profile.update',
                    'articles.view',
                    'articles.view_all',
                    'articles.create',
                    'articles.update_all',
                    'articles.delete_all',
                    'articles.archive',
                    'articles.restore',
                    'categories.view',
                    'categories.create',
                    'categories.update',
                    'categories.delete',
                    'tags.view',
                    'tags.create',
                    'tags.update',
                    'tags.delete',
                    'media.view',
                    'media.upload',
                    'media.update',
                    'media.delete',
                    'media.manage',
                    'galleries.view',
                    'galleries.create',
                    'galleries.update',
                    'galleries.delete',
                    'comments.view',
                    'comments.moderate',
                    'comments.delete',
                    'comments.mark_spam',
                    'homepage.view',
                    'homepage.manage',
                    'homepage.curate',
                    'breaking_news.view',
                    'breaking_news.create',
                    'breaking_news.update',
                    'breaking_news.delete',
                    'breaking_news.publish',
                    'navigation.view',
                    'navigation.manage',
                    'ads.view',
                    'ads.manage',
                    'ads.create',
                    'ads.update',
                    'ads.delete',
                    'ads.analytics',
                    'newsletter.view',
                    'newsletter.manage',
                    'newsletter.export',
                    'contacts.view',
                    'contacts.manage',
                    'contacts.assign',
                    'contacts.delete',
                    'pages.view',
                    'pages.create',
                    'pages.update',
                    'pages.publish',
                    'pages.delete',
                    'settings.view',
                    'settings.update',
                    'settings.branding',
                    'settings.editorial',
                    'settings.seo',
                    'editorial_team.manage',
                    'analytics.view',
                    'analytics.content',
                    'analytics.traffic',
                ],
            ],
            'editor_in_chief' => [
                'label' => 'Editor in Chief',
                'description' => 'Otoritas editorial tertinggi, memimpin dewan redaksi, persetujuan dan publikasi berita utama.',
                'is_system' => true,
                'permissions' => [
                    'dashboard.view',
                    'profile.update',
                    'articles.view',
                    'articles.view_all',
                    'articles.create',
                    'articles.update_all',
                    'articles.delete_all',
                    'articles.submit',
                    'articles.review',
                    'articles.request_revision',
                    'articles.approve',
                    'articles.schedule',
                    'articles.publish',
                    'articles.unpublish',
                    'articles.archive',
                    'articles.restore',
                    'articles.assign_author',
                    'articles.change_published_slug',
                    'categories.view',
                    'categories.create',
                    'categories.update',
                    'categories.delete',
                    'tags.view',
                    'tags.create',
                    'tags.update',
                    'tags.delete',
                    'media.view',
                    'media.upload',
                    'media.update',
                    'media.delete',
                    'media.manage',
                    'galleries.view',
                    'galleries.create',
                    'galleries.update',
                    'galleries.delete',
                    'comments.view',
                    'comments.moderate',
                    'comments.delete',
                    'comments.mark_spam',
                    'homepage.view',
                    'homepage.manage',
                    'homepage.curate',
                    'breaking_news.view',
                    'breaking_news.create',
                    'breaking_news.update',
                    'breaking_news.delete',
                    'breaking_news.publish',
                    'navigation.view',
                    'navigation.manage',
                    'pages.view',
                    'pages.create',
                    'pages.update',
                    'pages.publish',
                    'editorial_team.manage',
                    'settings.view',
                    'settings.editorial',
                    'contacts.view',
                    'contacts.manage',
                    'contacts.assign',
                    'newsletter.view',
                    'analytics.view',
                    'analytics.content',
                    'analytics.traffic',
                ],
            ],
            'editor' => [
                'label' => 'Editor',
                'description' => 'Meninjau naskah berita, menyunting, meminta revisi, dan menyetujui artikel reporter.',
                'is_system' => true,
                'permissions' => [
                    'dashboard.view',
                    'profile.update',
                    'articles.view',
                    'articles.view_all',
                    'articles.create',
                    'articles.update_own',
                    'articles.update_all',
                    'articles.submit',
                    'articles.review',
                    'articles.request_revision',
                    'articles.approve',
                    'articles.schedule',
                    'articles.publish',
                    'categories.view',
                    'categories.create',
                    'categories.update',
                    'tags.view',
                    'tags.create',
                    'tags.update',
                    'media.view',
                    'media.upload',
                    'media.update',
                    'galleries.view',
                    'galleries.create',
                    'galleries.update',
                    'comments.view',
                    'comments.moderate',
                    'comments.delete',
                    'comments.mark_spam',
                    'homepage.view',
                    'homepage.curate',
                    'breaking_news.view',
                    'pages.view',
                    'pages.update',
                    'analytics.view',
                    'analytics.content',
                ],
            ],
            'journalist' => [
                'label' => 'Journalist',
                'description' => 'Wartawan / reporter yang membuat draf artikel, liputan lapangan, dan mengajukan naskah ke meja redaksi.',
                'is_system' => true,
                'permissions' => [
                    'dashboard.view',
                    'profile.update',
                    'articles.view',
                    'articles.create',
                    'articles.update_own',
                    'articles.delete_own',
                    'articles.submit',
                    'categories.view',
                    'tags.view',
                    'media.view',
                    'media.upload',
                    'galleries.view',
                    'galleries.create',
                    'galleries.update',
                    'analytics.view',
                    'analytics.content',
                ],
            ],
            'contributor' => [
                'label' => 'Contributor',
                'description' => 'Penulis luar / kolumnis tamu dengan akses pengajuan naskah terbatas.',
                'is_system' => true,
                'permissions' => [
                    'dashboard.view',
                    'profile.update',
                    'articles.view',
                    'articles.create',
                    'articles.update_own',
                    'articles.submit',
                    'categories.view',
                    'tags.view',
                    'media.view',
                    'media.upload',
                ],
            ],
            'reader' => [
                'label' => 'Reader',
                'description' => 'Akun pembaca publik untuk menyimpan artikel, berkomentar, dan memberikan respon interaksi tanpa hak akses redaksi.',
                'is_system' => true,
                'permissions' => [],
            ],
        ];

        foreach ($roles as $name => $data) {
            $role = Role::firstOrCreate(
                ['name' => $name],
                [
                    'label' => $data['label'],
                    'description' => $data['description'],
                    'is_system' => $data['is_system'],
                ]
            );

            $role->syncPermissions($data['permissions']);
        }
    }
}
