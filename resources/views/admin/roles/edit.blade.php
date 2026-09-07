@extends('layouts.admin')

@section('title', 'Atur Izin Role — ' . $role->label)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-[#E8E8E8] pb-4">
        <div>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Pengaturan Izin: {{ $role->label }}
            </h1>
            <p class="text-xs text-[#5F6368] mt-1">
                Tentukan hak akses dan perizinan operasional untuk pengguna dengan role ini.
            </p>
        </div>
        <x-button variant="outline" size="sm" :href="route('admin.roles.index')">
            ← Kembali ke Matriks
        </x-button>
    </div>

    @if ($role->name === 'super_admin')
        <div class="p-4 bg-[#FFF8E1] border border-[#FFE082] rounded-[6px] text-xs text-[#B78103]">
            <strong>Role Terproteksi:</strong> Role Super Admin memiliki seluruh hak akses sistem secara permanen dan tidak dapat dibatasi.
        </div>
    @endif

    <div class="bg-white p-6 rounded-[8px] border border-[#E8E8E8] shadow-subtle">
        <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                    Deskripsi Role
                </label>
                <input 
                    id="description" 
                    name="description" 
                    type="text" 
                    value="{{ old('description', $role->description) }}" 
                    class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2 focus:outline-none focus:border-[#E50914]"
                />
            </div>

            <div>
                <h3 class="font-headline font-bold text-sm uppercase tracking-wider text-[#111111] mb-3 pb-2 border-b border-[#F2F2F2]">
                    Daftar Izin Sistem
                </h3>

                <div class="space-y-6">
                    @foreach ($permissions as $group => $perms)
                        <div class="bg-[#F7F7F7] p-4 rounded-[6px] border border-[#E8E8E8] space-y-3">
                            <h4 class="font-bold text-xs uppercase tracking-wider text-[#E50914]">
                                Grup: {{ strtoupper($group) }}
                            </h4>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach ($perms as $perm)
                                    <label class="flex items-start gap-2 bg-white p-2.5 rounded border border-[#E8E8E8] cursor-pointer select-none text-xs hover:border-[#111111] transition-colors">
                                        <input 
                                            type="checkbox" 
                                            name="permissions[]" 
                                            value="{{ $perm->name }}"
                                            {{ ($role->name === 'super_admin' || $role->hasPermission($perm->name)) ? 'checked' : '' }}
                                            {{ $role->name === 'super_admin' ? 'disabled' : '' }}
                                            class="rounded border-[#CCCCCC] text-[#E50914] focus:ring-[#E50914] mt-0.5"
                                        />
                                        <div>
                                            <span class="font-bold text-[#111111] block">{{ $perm->label }}</span>
                                            <span class="text-[10px] font-mono text-[#80868B]">{{ $perm->name }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($role->name !== 'super_admin')
                <div class="pt-4 border-t border-[#F2F2F2] flex items-center gap-3">
                    <x-button type="submit" variant="primary" size="md">
                        Simpan Perubahan Izin
                    </x-button>
                    <x-button variant="outline" size="md" :href="route('admin.roles.index')">
                        Batal
                    </x-button>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
