@extends('layouts.admin')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold font-headline text-gray-900">Pelanggan Buletin (Newsletter)</h1>
        <p class="text-sm text-gray-500 mt-1">Daftar pembaca yang berlangganan kurasi berita harian TopNews Morning Brief.</p>
    </div>
    @can('export', App\Models\NewsletterSubscriber::class)
        <a href="{{ route('admin.newsletter.export', request()->query()) }}" class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold text-xs px-4 py-2.5 rounded-md shadow-sm transition-colors">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Ekspor Data (CSV)
        </a>
    @endcan
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Quick Counts KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <p class="text-xs text-gray-500 font-medium">Total Terdaftar</p>
            <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($counts['total']) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <p class="text-xs text-emerald-600 font-medium">Pelanggan Aktif</p>
            <p class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($counts['active']) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <p class="text-xs text-amber-600 font-medium">Menunggu Verifikasi</p>
            <p class="text-2xl font-black text-amber-700 mt-1">{{ number_format($counts['pending']) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <p class="text-xs text-gray-400 font-medium">Berhenti Langganan</p>
            <p class="text-2xl font-black text-gray-600 mt-1">{{ number_format($counts['unsubscribed']) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <p class="text-xs text-rose-600 font-medium">Diblokir</p>
            <p class="text-2xl font-black text-rose-700 mt-1">{{ number_format($counts['blocked']) }}</p>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.newsletter.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Cari email atau nama..." 
                class="text-xs border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914] w-64"
            >

            <select name="status" class="text-xs border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]">
                <option value="">Semua Status</option>
                @foreach(App\Enums\NewsletterSubscriberStatus::cases() as $st)
                    <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                        {{ $st->label() }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs px-3 py-2 rounded-md transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.newsletter.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Reset</a>
            @endif
        </form>
    </div>

    <!-- Subscribers Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-6 py-3 font-bold">Alamat Email & Nama</th>
                    <th scope="col" class="px-6 py-3 font-bold">Status</th>
                    <th scope="col" class="px-6 py-3 font-bold">Sumber</th>
                    <th scope="col" class="px-6 py-3 font-bold">Tanggal Terdaftar</th>
                    <th scope="col" class="px-6 py-3 font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($subscribers as $sub)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $sub->email }}</div>
                            @if($sub->name)
                                <div class="text-xs text-gray-500">{{ $sub->name }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $sub->status->badgeClasses() }}">
                                {{ $sub->status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500 capitalize">
                            {{ $sub->source ?? 'homepage' }}
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500">
                            <div>{{ $sub->subscribed_at?->isoFormat('D MMM Y, HH:mm') ?? '-' }}</div>
                            @if($sub->verified_at)
                                <div class="text-[11px] text-emerald-600">Terverifikasi: {{ $sub->verified_at->isoFormat('D MMM Y') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                            @can('manage', $sub)
                                <form action="{{ route('admin.newsletter.toggle-block', $sub) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold {{ $sub->status === App\Enums\NewsletterSubscriberStatus::Blocked ? 'text-emerald-600 hover:text-emerald-800' : 'text-amber-600 hover:text-amber-800' }}">
                                        {{ $sub->status === App\Enums\NewsletterSubscriberStatus::Blocked ? 'Aktifkan Kembali' : 'Blokir' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.newsletter.destroy', $sub) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus data pelanggan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800">
                                        Hapus
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada data pelanggan buletin yang sesuai dengan filter pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($subscribers->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $subscribers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
