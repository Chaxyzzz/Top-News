@extends('layouts.admin')

@section('title', 'Edit Pengguna — ' . $user->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-[#E8E8E8] pb-4">
        <div>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Edit Pengguna Staf
            </h1>
            <p class="text-xs text-[#5F6368] mt-1">
                Ubah informasi akun, role hak akses, atau status keaktifan pengguna.
            </p>
        </div>
        <x-button variant="outline" size="sm" :href="route('admin.users.index')">
            ← Kembali
        </x-button>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-[#FDE8E9] border border-[#E50914] text-xs text-[#C8102E] rounded-[4px] space-y-1">
            <p class="font-bold">Terdapat kesalahan pengisian data:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 rounded-[8px] border border-[#E8E8E8] shadow-subtle">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                        Nama Lengkap <span class="text-[#E50914]">*</span>
                    </label>
                    <input 
                        id="name" 
                        name="name" 
                        type="text" 
                        value="{{ old('name', $user->name) }}" 
                        required 
                        class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2 focus:outline-none focus:border-[#E50914]"
                    />
                </div>

                <div>
                    <label for="username" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                        Username <span class="text-[#E50914]">*</span>
                    </label>
                    <input 
                        id="username" 
                        name="username" 
                        type="text" 
                        value="{{ old('username', $user->username) }}" 
                        required 
                        class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2 focus:outline-none focus:border-[#E50914]"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                        Email Resmi <span class="text-[#E50914]">*</span>
                    </label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        value="{{ old('email', $user->email) }}" 
                        required 
                        class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2 focus:outline-none focus:border-[#E50914]"
                    />
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                        Nomor Telepon
                    </label>
                    <input 
                        id="phone" 
                        name="phone" 
                        type="text" 
                        value="{{ old('phone', $user->phone) }}" 
                        class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2 focus:outline-none focus:border-[#E50914]"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="role_id" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                        Role Hak Akses <span class="text-[#E50914]">*</span>
                    </label>
                    <select id="role_id" name="role_id" required class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2 focus:outline-none focus:border-[#E50914] bg-white">
                        @foreach ($roles as $role)
                            @if ($role->name === 'super_admin' && !Auth::user()->isSuperAdmin())
                                @continue
                            @endif
                            <option value="{{ $role->id }}" {{ old('role_id', $user->primary_role?->id) == $role->id ? 'selected' : '' }}>
                                {{ $role->label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                        Status Akun <span class="text-[#E50914]">*</span>
                    </label>
                    <select id="status" name="status" required class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2 focus:outline-none focus:border-[#E50914] bg-white">
                        @foreach ($statuses as $st)
                            <option value="{{ $st->value }}" {{ old('status', $user->status->value) === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-[#F2F2F2]">
                <h3 class="font-bold text-xs uppercase tracking-wider text-[#111111] mb-2">
                    Ubah Kata Sandi (Opsional)
                </h3>
                <p class="text-[11px] text-[#80868B] mb-3">Kosongkan kolom berikut jika tidak ingin mengubah kata sandi pengguna.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                            Kata Sandi Baru
                        </label>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2 focus:outline-none focus:border-[#E50914]"
                            placeholder="Minimal 8 karakter"
                        />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                            Ulangi Kata Sandi Baru
                        </label>
                        <input 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2 focus:outline-none focus:border-[#E50914]"
                            placeholder="Ulangi kata sandi"
                        />
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center gap-3">
                <x-button type="submit" variant="primary" size="md">
                    Simpan Perubahan
                </x-button>
                <x-button variant="outline" size="md" :href="route('admin.users.index')">
                    Batal
                </x-button>
            </div>
        </form>
    </div>
</div>
@endsection
