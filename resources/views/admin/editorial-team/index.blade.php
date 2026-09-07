@extends('layouts.admin')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold font-headline text-gray-900">Susunan Tim Redaksi Publik</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola jajaran staf dan editor yang ditampilkan pada halaman publik Dewan Redaksi (/editorial).</p>
    </div>
    <a href="{{ route('editorial.team') }}" target="_blank" class="text-xs font-bold text-[#E50914] hover:underline flex items-center gap-1">
        Lihat Halaman Publik &rarr;
    </a>
</div>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="border-b border-gray-100 pb-4 mb-6">
        <p class="text-xs text-gray-500">
            Hanya staf yang dicentang <strong>"Tampilkan di Redaksi Publik"</strong> yang akan tampil di halaman publik. Alamat email, telepon, dan hak akses internal sistem tidak akan pernah dipublikasikan.
        </p>
    </div>

    <form action="{{ route('admin.editorial-team.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            @foreach($staffUsers as $index => $staff)
                <div class="p-5 bg-gray-50 border border-gray-200 rounded-lg space-y-4">
                    <input type="hidden" name="members[{{ $index }}][id]" value="{{ $staff->id }}">

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-200 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-700 font-bold flex items-center justify-center text-sm font-headline">
                                {{ Str::substr($staff->name, 0, 2) }}
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">{{ $staff->name }}</h3>
                                <p class="text-xs text-gray-400 font-mono">{{ $staff->username }} &bull; Role: {{ $staff->roles->pluck('label')->join(', ') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="members[{{ $index }}][show_on_editorial_team]" 
                                    value="1" 
                                    {{ old("members.{$index}.show_on_editorial_team", $staff->show_on_editorial_team) ? 'checked' : '' }}
                                    class="rounded text-[#E50914] focus:ring-[#E50914] w-4 h-4"
                                >
                                <span class="text-xs font-bold text-gray-700">Tampilkan di Redaksi Publik</span>
                            </label>

                            <div class="flex items-center gap-1.5">
                                <label for="order_{{ $staff->id }}" class="text-xs text-gray-500 font-medium whitespace-nowrap">Urutan:</label>
                                <input 
                                    type="number" 
                                    id="order_{{ $staff->id }}" 
                                    name="members[{{ $index }}][editorial_team_order]" 
                                    value="{{ old("members.{$index}.editorial_team_order", $staff->editorial_team_order) }}" 
                                    min="0" 
                                    max="999" 
                                    class="w-16 text-xs border-gray-300 rounded shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">
                                Jabatan / Gelar Publik (Public Title)
                            </label>
                            <input 
                                type="text" 
                                name="members[{{ $index }}][public_title]" 
                                value="{{ old("members.{$index}.public_title", $staff->public_title) }}" 
                                placeholder="Contoh: Pemimpin Redaksi / Editor in Chief" 
                                class="w-full text-xs border-gray-300 rounded shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">
                                Bio Ringkas (Bio Summary)
                            </label>
                            <textarea 
                                name="members[{{ $index }}][bio]" 
                                rows="2" 
                                placeholder="Ringkasan pengalaman jurnalistik atau bidang fokus peliputan..." 
                                class="w-full text-xs border-gray-300 rounded shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                            >{{ old("members.{$index}.bio", $staff->bio) }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
            <button type="submit" class="bg-[#E50914] hover:bg-[#B80710] text-white font-bold text-xs px-6 py-2.5 rounded-md shadow-sm transition-colors">
                Simpan Susunan Tim Redaksi
            </button>
        </div>
    </form>
</div>
@endsection
