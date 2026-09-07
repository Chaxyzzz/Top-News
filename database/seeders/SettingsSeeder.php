<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Group: general
            ['group' => 'general', 'key' => 'site_name', 'value' => 'TopNews', 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'tagline', 'value' => 'Informasi Cepat. Perspektif Jelas. Berita Terpercaya.', 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'site_description', 'value' => 'Portal berita independen terdepan yang menyajikan liputan peristiwa terkini, investigasi mendalam, dan jurnalisme berimbang.', 'type' => 'text', 'is_public' => true],
            ['group' => 'general', 'key' => 'default_locale', 'value' => 'id', 'type' => 'string', 'is_public' => false],
            ['group' => 'general', 'key' => 'timezone', 'value' => 'Asia/Jakarta', 'type' => 'string', 'is_public' => false],

            // Group: branding
            ['group' => 'branding', 'key' => 'brand_name', 'value' => 'TopNews', 'type' => 'string', 'is_public' => true],
            ['group' => 'branding', 'key' => 'logo_media_id', 'value' => null, 'type' => 'media', 'is_public' => true],
            ['group' => 'branding', 'key' => 'compact_logo_media_id', 'value' => null, 'type' => 'media', 'is_public' => true],
            ['group' => 'branding', 'key' => 'favicon_media_id', 'value' => null, 'type' => 'media', 'is_public' => true],
            ['group' => 'branding', 'key' => 'footer_logo_media_id', 'value' => null, 'type' => 'media', 'is_public' => true],

            // Group: contact
            ['group' => 'contact', 'key' => 'public_email', 'value' => 'topnews90@gmail.com', 'type' => 'string', 'is_public' => true],
            ['group' => 'contact', 'key' => 'public_phone', 'value' => '085262135190', 'type' => 'string', 'is_public' => true],
            ['group' => 'contact', 'key' => 'whatsapp', 'value' => null, 'type' => 'string', 'is_public' => true],
            ['group' => 'contact', 'key' => 'office_address', 'value' => 'Bireuen, Aceh, Indonesia', 'type' => 'text', 'is_public' => true],
            ['group' => 'contact', 'key' => 'business_hours', 'value' => 'Senin - Jumat: 08:00 - 18:00 WIB', 'type' => 'string', 'is_public' => true],
            ['group' => 'contact', 'key' => 'google_maps_url', 'value' => null, 'type' => 'url', 'is_public' => true],

            // Group: social
            ['group' => 'social', 'key' => 'instagram', 'value' => 'https://instagram.com/topnews_id', 'type' => 'url', 'is_public' => true],
            ['group' => 'social', 'key' => 'facebook', 'value' => 'https://facebook.com/topnewsid', 'type' => 'url', 'is_public' => true],
            ['group' => 'social', 'key' => 'x', 'value' => 'https://x.com/topnews_id', 'type' => 'url', 'is_public' => true],
            ['group' => 'social', 'key' => 'youtube', 'value' => 'https://youtube.com/@topnews_id', 'type' => 'url', 'is_public' => true],
            ['group' => 'social', 'key' => 'tiktok', 'value' => 'https://tiktok.com/@topnews_id', 'type' => 'url', 'is_public' => true],
            ['group' => 'social', 'key' => 'linkedin', 'value' => null, 'type' => 'url', 'is_public' => true],

            // Group: footer
            ['group' => 'footer', 'key' => 'about_text', 'value' => 'TopNews adalah portal berita digital independen yang berpegang teguh pada prinsip ketepatan fakta, integritas redaksi, dan kepentingan publik.', 'type' => 'text', 'is_public' => true],
            ['group' => 'footer', 'key' => 'copyright_text', 'value' => 'Hak cipta dilindungi undang-undang. Redaksi mematuhi Kode Etik Jurnalistik Dewan Pers.', 'type' => 'text', 'is_public' => true],

            // Group: editorial
            ['group' => 'editorial', 'key' => 'default_allow_comments', 'value' => '1', 'type' => 'boolean', 'is_public' => false],
            ['group' => 'editorial', 'key' => 'default_reading_words_per_minute', 'value' => '200', 'type' => 'integer', 'is_public' => false],
            ['group' => 'editorial', 'key' => 'default_articles_per_page', 'value' => '12', 'type' => 'integer', 'is_public' => false],

            // Group: seo
            ['group' => 'seo', 'key' => 'default_meta_title', 'value' => 'TopNews — Berita Terkini, Akurat & Terpercaya', 'type' => 'string', 'is_public' => true],
            ['group' => 'seo', 'key' => 'default_meta_description', 'value' => 'TopNews menyajikan informasi aktual terverifikasi, investigasi mendalam, rubrik opini, video liputan, dan foto cerita.', 'type' => 'text', 'is_public' => true],
            ['group' => 'seo', 'key' => 'default_social_image_id', 'value' => null, 'type' => 'media', 'is_public' => true],
            ['group' => 'seo', 'key' => 'organization_name', 'value' => 'PT TopNews Media Nusantara', 'type' => 'string', 'is_public' => true],

            // Group: newsletter
            ['group' => 'newsletter', 'key' => 'enabled', 'value' => '1', 'type' => 'boolean', 'is_public' => true],
            ['group' => 'newsletter', 'key' => 'double_opt_in', 'value' => '1', 'type' => 'boolean', 'is_public' => false],
            ['group' => 'newsletter', 'key' => 'default_sender_name', 'value' => 'Redaksi TopNews', 'type' => 'string', 'is_public' => false],
            ['group' => 'newsletter', 'key' => 'default_reply_to', 'value' => 'newsletter@topnews.id', 'type' => 'string', 'is_public' => false],
            ['group' => 'newsletter', 'key' => 'cta_title', 'value' => 'Berita Penting, Langsung ke Inbox Anda.', 'type' => 'string', 'is_public' => true],
            ['group' => 'newsletter', 'key' => 'cta_description', 'value' => 'Dapatkan kurasi liputan terbaik, investigasi eksklusif, dan analisis harian dari meja redaksi TopNews setiap pagi.', 'type' => 'text', 'is_public' => true],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(
                ['group' => $s['group'], 'key' => $s['key']],
                ['value' => $s['value'], 'type' => $s['type'], 'is_public' => $s['is_public']]
            );
        }
    }
}
