@extends('layouts.admin')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold font-headline text-gray-900">Kotak Masuk Pesan Publik</h1>
        <p class="text-sm text-gray-500 mt-1">Daftar pertanyaan, hak jawab, siaran pers, dan permintaan kerja sama dari pembaca.</p>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Quick Counts KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <p class="text-xs text-gray-500 font-medium">Total Pesan</p>
            <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($counts['total']) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <p class="text-xs text-blue-600 font-medium">Pesan Baru</p>
            <p class="text-2xl font-black text-blue-700 mt-1">{{ number_format($counts['new']) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <p class="text-xs text-amber-600 font-medium">Sedang Diproses</p>
            <p class="text-2xl font-black text-amber-700 mt-1">{{ number_format($counts['in_progress']) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <p class="text-xs text-emerald-600 font-medium">Terselesaikan</p>
            <p class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($counts['resolved']) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <p class="text-xs text-rose-600 font-medium">Spam</p>
            <p class="text-2xl font-black text-rose-700 mt-1">{{ number_format($counts['spam']) }}</p>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.contacts.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Cari pengirim, email, atau subjek..." 
                class="text-xs border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914] w-64"
            >

            <select name="status" class="text-xs border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]">
                <option value="">Semua Status</option>
                @foreach(App\Enums\ContactMessageStatus::cases() as $st)
                    <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                        {{ $st->label() }}
                    </option>
                @endforeach
            </select>

            <select name="assigned_to" class="text-xs border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]">
                <option value="">Semua Penugasan</option>
                <option value="unassigned" {{ request('assigned_to') === 'unassigned' ? 'selected' : '' }}>Belum Ditugaskan</option>
                @foreach($staffUsers as $staff)
                    <option value="{{ $staff->id }}" {{ request('assigned_to') == $staff->id ? 'selected' : '' }}>
                        {{ $staff->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs px-3 py-2 rounded-md transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'assigned_to']))
                <a href="{{ route('admin.contacts.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Reset</a>
            @endif
        </form>
    </div>

    <!-- Messages Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-6 py-3 font-bold">Pengirim & Subjek</th>
                    <th scope="col" class="px-6 py-3 font-bold">Kategori</th>
                    <th scope="col" class="px-6 py-3 font-bold">Status</th>
                    <th scope="col" class="px-6 py-3 font-bold">Penugasan</th>
                    <th scope="col" class="px-6 py-3 font-bold">Tanggal</th>
                    <th scope="col" class="px-6 py-3 font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($messages as $msg)
                    <tr class="hover:bg-gray-50 {{ $msg->status === App\Enums\ContactMessageStatus::New ? 'bg-blue-50/40 font-semibold' : '' }}">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.contacts.show', $msg) }}" class="block font-bold text-gray-900 hover:text-[#E50914]">
                                {{ $msg->subject }}
                            </a>
                            <div class="text-xs text-gray-500 font-normal mt-0.5">
                                {{ $msg->name }} &lt;{{ $msg->email }}&gt;
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded capitalize">
                                {{ $msg->category ?? 'Umum' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $msg->status->badgeClasses() }}">
                                {{ $msg->status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-600">
                            {{ $msg->assignedTo ? $msg->assignedTo->name : '—' }}
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                            {{ $msg->created_at->isoFormat('D MMM Y, HH:mm') }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.contacts.show', $msg) }}" class="text-xs font-bold text-[#E50914] hover:text-[#B80710]">
                                Buka Pesan
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada pesan masuk yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($messages->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
