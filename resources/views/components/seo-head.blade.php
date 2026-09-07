@props(['seo' => null])

@php
    if (! $seo) {
        $seo = app(\App\Services\SeoService::class)->forHome();
    }
@endphp

<!-- Primary Meta Tags -->
<title>{{ $seo->title }}</title>
<meta name="title" content="{{ $seo->title }}">
<meta name="description" content="{{ $seo->description }}">
<link rel="canonical" href="{{ $seo->canonicalUrl }}">
<meta name="robots" content="{{ $seo->robots }}">

<!-- Open Graph / Facebook / WhatsApp -->
@foreach($seo->openGraph as $property => $content)
    @if(!empty($content))
        <meta property="{{ $property }}" content="{{ $content }}">
    @endif
@endforeach

<!-- Article Specific Open Graph Meta -->
@foreach($seo->articleMeta as $property => $content)
    @if(is_array($content))
        @foreach($content as $item)
            <meta property="{{ $property }}" content="{{ $item }}">
        @endforeach
    @elseif(!empty($content))
        <meta property="{{ $property }}" content="{{ $content }}">
    @endif
@endforeach

<!-- Twitter / X Cards -->
@foreach($seo->twitter as $name => $content)
    @if(!empty($content))
        <meta name="{{ $name }}" content="{{ $content }}">
    @endif
@endforeach

<!-- JSON-LD Structured Data -->
@php
    $jsonLd = $seo->renderJsonLd();
@endphp
@if(!empty($jsonLd))
<script type="application/ld+json">
{!! $jsonLd !!}
</script>
@endif
