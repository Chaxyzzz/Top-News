<?php

return [
    'name' => env('APP_NAME', 'TopNews'),
    'tagline' => 'Fast information. Clear perspective. Trusted news.',
    'tagline_id' => 'Informasi Cepat. Perspektif Jelas. Berita Terpercaya.',
    'description' => 'TopNews adalah platform berita dan media digital independen menyajikan berita terkini, analisis mendalam, opini, dan jurnalisme berkualitas tinggi.',

    'contact' => [
        'email' => env('TOPNEWS_CONTACT_EMAIL', 'topnews90@gmail.com'),
        'phone' => env('TOPNEWS_CONTACT_PHONE', '085262135190'),
        'address' => 'Bireuen, Aceh, Indonesia',
        'location' => 'Bireuen, Aceh, Indonesia',
    ],

    'social' => [
        'x' => 'https://x.com/topnews_id',
        'facebook' => 'https://facebook.com/topnewsid',
        'instagram' => 'https://instagram.com/topnews_id',
        'youtube' => 'https://youtube.com/@topnewsid',
        'telegram' => 'https://t.me/topnewsofficial',
        'tiktok' => 'https://tiktok.com/@topnews_id',
    ],

    'navigation' => [
        ['label' => 'Beranda', 'route' => 'home', 'url' => '/'],
        ['label' => 'Terbaru', 'route' => 'latest', 'url' => '/latest'],
        ['label' => 'Nasional', 'route' => 'category.show', 'slug' => 'nasional', 'url' => '/category/nasional'],
        ['label' => 'Politik', 'route' => 'category.show', 'slug' => 'politik', 'url' => '/category/politik'],
        ['label' => 'Ekonomi & Bisnis', 'route' => 'category.show', 'slug' => 'ekonomi', 'url' => '/category/ekonomi'],
        ['label' => 'Teknologi', 'route' => 'category.show', 'slug' => 'teknologi', 'url' => '/category/teknologi'],
        ['label' => 'Olahraga', 'route' => 'category.show', 'slug' => 'olahraga', 'url' => '/category/olahraga'],
        ['label' => 'Hiburan', 'route' => 'category.show', 'slug' => 'hiburan', 'url' => '/category/hiburan'],
        ['label' => 'Gaya Hidup', 'route' => 'category.show', 'slug' => 'lifestyle', 'url' => '/category/lifestyle'],
        ['label' => 'Internasional', 'route' => 'category.show', 'slug' => 'internasional', 'url' => '/category/internasional'],
    ],

    'reading_wpm' => 200,
];
