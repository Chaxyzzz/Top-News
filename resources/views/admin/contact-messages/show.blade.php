@extends('layouts.admin')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold font-headline text-gray-900">Detail Pesan Masuk</h1>
        <p class="text-sm text-gray-500 mt-1">Komunikasi masuk dari formulir kontak publik TopNews.</p>
    </div>
    <a href="{{ route('admin.contacts.index') }}" class="text-xs font-bold text-gray-600 hover:text-gray-900 flex items-center gap-1">
        &larr; Kembali ke Kotak Masuk
    </a>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    <!-- Message Content (Cols 1-8) -->
    <div class="lg:col-span-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-6">
        <div class="border-b border-gray-100 pb-5">
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $contactMessage->status->badgeClasses() }}">
                    {{ $contactMessage->status->label() }}
                </span>
                <span class="inline-block px-2.5 py-0.5 bg-gray-100 text-gray-700 text-xs rounded capitalize">
                    Kategori: {{ $contactMessage->category ?? 'Umum' }}
                </span>
                <span class="text-xs text-gray-400">
                    ID: {{ $contactMessage->uuid }}
                </span>
            </div>

            <h2 class="text-xl font-bold font-headline text-gray-900 leading-snug">
                {{ $contactMessage->subject }}
            </h2>

            <div class="mt-3 text-xs text-gray-600 space-y-1">
                <p>
                    <strong class="text-gray-900">Dari:</strong> {{ $contactMessage->name }} 
                    <a href="mailto:{{ $contactMessage->email }}?subject=Re: {{ urlencode($contactMessage->subject) }}" class="text-[#E50914] hover:underline ml-1">
                        &lt;{{ $contactMessage->email }}&gt;
                    </a>
                </p>
                <p>
                    <strong class="text-gray-900">Waktu Masuk:</strong> {{ $contactMessage->created_at->isoFormat('dddd, D MMMM Y - HH:mm:ss') }} WIB
                </p>
                @if($contactMessage->read_at)
                    <p class="text-gray-400">
                        Pertama dibaca: {{ $contactMessage->read_at->isoFormat('D MMM Y, HH:mm') }}
                    </p>
                @endif
            </div>
        </div>

        <!-- Safe Escaped Message Body -->
        <div class="text-sm text-gray-800 leading-relaxed font-sans bg-gray-50 p-6 rounded-lg border border-gray-200 whitespace-pre-wrap">
            {{ $contactMessage->message }}
        </div>

        <!-- Quick Reply Action -->
        <div class="pt-2 flex items-center justify-between border-t border-gray-100">
            <a href="mailto:{{ $contactMessage->email }}?subject=Re: {{ urlencode($contactMessage->subject) }}" class="inline-flex items-center gap-2 bg-[#E50914] hover:bg-[#B80710] text-white font-bold text-xs px-5 py-2.5 rounded-md shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Balas via Email Klien
            </a>

            @can('delete', $contactMessage)
                <form action="{{ route('admin.contacts.destroy', $contactMessage) }}" method="POST" onsubmit="return confirm('Hapus pesan ini secara permanen?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800">
                        Hapus Pesan
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <!-- Management Sidebar (Cols 9-12) -->
    <div class="lg:col-span-4 space-y-6">
        <!-- Status Management Card -->
        @can('manage', $contactMessage)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900">Ubah Status Pesan</h3>
                
                <form action="{{ route('admin.contacts.update-status', $contactMessage) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <select name="status" class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]">
                        @foreach(App\Enums\ContactMessageStatus::cases() as $st)
                            <option value="{{ $st->value }}" {{ $contactMessage->status === $st ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold text-xs py-2 px-3 rounded-md transition-colors">
                        Simpan Status
                    </button>
                </form>

                @if($contactMessage->status !== App\Enums\ContactMessageStatus::Spam)
                    <form action="{{ route('admin.contacts.spam', $contactMessage) }}" method="POST" class="pt-2 border-t border-gray-100">
                        @csrf
                        <button type="submit" class="w-full text-center text-xs font-semibold text-rose-600 hover:text-rose-800 py-1">
                            Tandai sebagai Spam
                        </button>
                    </form>
                @endif
            </div>
        @endcan

        <!-- Staff Assignment Card -->
        @can('assign', $contactMessage)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900">Tugaskan ke Staf</h3>
                
                <form action="{{ route('admin.contacts.assign', $contactMessage) }}" method="POST" class="space-y-3">
                    @csrf

                    <select name="assigned_to" class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]">
                        <option value="">-- Belum Ditugaskan --</option>
                        @foreach($staffUsers as $staff)
                            <option value="{{ $staff->id }}" {{ $contactMessage->assigned_to === $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }} ({{ $staff->username }})
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold text-xs py-2 px-3 rounded-md transition-colors">
                        Simpan Penugasan
                    </button>
                </form>
            </div>
        @endcan
    </div>
</div>
@endsection
