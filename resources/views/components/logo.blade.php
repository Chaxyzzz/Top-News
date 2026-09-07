@props([
    'variant' => 'default', // 'default', 'white', 'compact'
    'size' => 'md', // 'sm', 'md', 'lg'
])

@php
    $settings = app(\App\Services\SettingsService::class);
    $mediaKey = $variant === 'compact' 
        ? ($settings->get('branding.compact_logo_media_id') ? 'branding.compact_logo_media_id' : 'branding.logo_media_id')
        : ($variant === 'white' ? ($settings->get('branding.footer_logo_media_id') ? 'branding.footer_logo_media_id' : 'branding.logo_media_id') : 'branding.logo_media_id');
    
    $logoMedia = $settings->getMedia($mediaKey);
    $brandName = $settings->get('branding.brand_name', config('topnews.name', 'TopNews'));

    $sizes = [
        'sm' => ['text' => 'text-xl', 'height' => 'h-7'],
        'md' => ['text' => 'text-2xl md:text-3xl', 'height' => 'h-9 md:h-10'],
        'lg' => ['text' => 'text-3xl md:text-4xl', 'height' => 'h-12 md:h-14'],
    ];
    $sizeConfig = $sizes[$size] ?? $sizes['md'];
    $textColor = $variant === 'white' ? 'text-white' : 'text-[#111111]';
@endphp

<a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 select-none group transition-opacity hover:opacity-95" aria-label="{{ $brandName }} Homepage">
    @if($logoMedia && $logoMedia->url)
        <img 
            src="{{ $logoMedia->url }}" 
            alt="{{ $logoMedia->alt_text ?: $brandName }}" 
            class="{{ $sizeConfig['height'] }} w-auto object-contain"
        >
    @else
        <span class="inline-flex items-center gap-1.5 font-black tracking-tighter uppercase font-headline {{ $sizeConfig['text'] }} {{ $textColor }}">
            <span class="inline-block px-1.5 py-0.5 bg-[#E50914] text-white rounded-[3px] font-black text-[0.85em] leading-none tracking-normal">TOP</span>
            <span class="tracking-tight font-extrabold">NEWS</span>
            <span class="w-1.5 h-1.5 rounded-full bg-[#E50914] inline-block -ml-0.5"></span>
        </span>
    @endif
</a>
