@props([
    'media' => null,
    'variant' => 'medium',
    'alt' => null,
    'class' => 'w-full h-auto object-cover',
    'loading' => 'lazy',
    'fetchpriority' => null,
    'fallback' => null,
])

@php
    $src = null;
    $width = null;
    $height = null;
    $altText = $alt;

    if ($media instanceof \App\Models\Media) {
        $src = $media->variantUrl($variant);
        $width = $media->width;
        $height = $media->height;
        $altText = $alt ?: ($media->alt_text ?: $media->original_filename);
    } elseif (is_string($media) && !empty($media)) {
        $src = str_starts_with($media, 'http') ? $media : asset('storage/' . $media);
    } elseif (!empty($fallback)) {
        $src = $fallback;
    } else {
        $src = asset('images/placeholder.svg');
    }
@endphp

<img
    src="{{ $src }}"
    @if($media instanceof \App\Models\Media)
        srcset="{{ $media->small_url }} 640w, {{ $media->medium_url }} 1024w, {{ $media->large_url }} 1600w"
        sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
    @endif
    alt="{{ $altText ?: 'TopNews Media' }}"
    class="{{ $class }}"
    loading="{{ $loading }}"
    @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
    @if($width && $height) width="{{ $width }}" height="{{ $height }}" @endif
    {{ $attributes->except(['class', 'loading', 'fetchpriority']) }}
>
