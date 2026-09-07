@extends('layouts.admin')

@section('title', 'Pengaturan Situs')

@section('content')
<div class="space-y-6">
    <x-admin.page-header 
        title="Pengaturan Portal TopNews" 
        description="Konfigurasi identitas media, kontak redaksi, integrasi media sosial, dan kebijakan publikasi."
    >
        <x-slot name="breadcrumbs">
            <x-admin.breadcrumb :items="[
                ['label' => 'Pengaturan Situs']
            ]" />
        </x-slot>
    </x-admin.page-header>

    <div class="p-4 bg-[#FFF1F2] border border-[#FECDD3] rounded-[6px] text-xs text-[#C8102E] flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 bg-[#E50914] text-white font-bold rounded text-[10px] uppercase">Phase 03 Foundation</span>
            <span class="font-medium">Formulir konfigurasi penyimpanan database dinamis akan diimplementasikan pada <strong>Phase 10</strong>.</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-admin.card title="Identitas & Branding Portal" subtitle="Konfigurasi nama, tagline, dan logo">
            <div class="space-y-3 text-xs text-[#6B7280]">
                <p><strong>Nama Media:</strong> {{ config('topnews.name') }}</p>
                <p><strong>Tagline:</strong> {{ config('topnews.tagline_id') }}</p>
                <p><strong>Deskripsi:</strong> {{ config('topnews.description_id') }}</p>
            </div>
        </x-admin.card>

        <x-admin.card title="Kontak Redaksi & Kantor" subtitle="Informasi korespondensi resmi">
            <div class="space-y-3 text-xs text-[#6B7280]">
                <p><strong>Email Redaksi:</strong> {{ config('topnews.editorial_email') }}</p>
                <p><strong>Email Iklan & Bisnis:</strong> {{ config('topnews.commercial_email') }}</p>
                <p><strong>Telepon Kantor:</strong> {{ config('topnews.phone') }}</p>
            </div>
        </x-admin.card>
    </div>
</div>
@endsection
