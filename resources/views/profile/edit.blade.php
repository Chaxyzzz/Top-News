@extends('layouts.admin')

@section('title', 'Profil Pengguna')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-[#E8E8E8] pb-4">
        <div>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Pengaturan Profil Akun
            </h1>
            <p class="text-xs text-[#5F6368] mt-1">
                Kelola informasi pribadi, foto profil, dan keamanan kata sandi Anda.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Profile Info Card (Cols 1-7) -->
        <div class="lg:col-span-7 bg-white p-6 rounded-[8px] border border-[#E8E8E8] shadow-subtle space-y-6">
            <h2 class="font-headline font-bold text-base text-[#111111] border-b border-[#F2F2F2] pb-3">
                Informasi Dasar Akun
            </h2>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Avatar Preview & Upload -->
                <div class="flex items-center gap-4 pb-2">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full object-cover border border-[#E8E8E8]">
                    @else
                        <div class="w-16 h-16 rounded-full bg-[#111111] text-white flex items-center justify-center font-bold text-xl">
                            {{ $user->initials }}
                        </div>
                    @endif
                    <div>
                        <label for="avatar" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                            Foto Profil (Avatar)
                        </label>
                        <input 
                            id="avatar" 
                            name="avatar" 
                            type="file" 
                            accept="image/png,image/jpeg,image/webp"
                            class="text-xs text-[#5F6368] file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-[#F2F2F2] file:text-[#111111] hover:file:bg-[#E8E8E8] cursor-pointer"
                        />
                        <p class="text-[11px] text-[#80868B] mt-1">Format: JPG, PNG, WEBP. Maks 2MB.</p>
                    </div>
                </div>

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
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#80868B] mb-1">
                            Username
                        </label>
                        <input 
                            type="text" 
                            value="{{ $user->username }}" 
                            disabled 
                            class="w-full text-sm bg-[#F7F7F7] border border-[#E8E8E8] text-[#80868B] rounded-[4px] px-3.5 py-2 cursor-not-allowed"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                            Email <span class="text-[#E50914]">*</span>
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
                            placeholder="+62 812..."
                        />
                    </div>
                </div>

                <div class="pt-2">
                    <x-button type="submit" variant="primary" size="md">
                        Simpan Perubahan Profil
                    </x-button>
                </div>
            </form>
        </div>

        <!-- Password Change Card (Cols 8-12) -->
        <div class="lg:col-span-5 bg-white p-6 rounded-[8px] border border-[#E8E8E8] shadow-subtle space-y-6">
            <h2 class="font-headline font-bold text-base text-[#111111] border-b border-[#F2F2F2] pb-3">
                Ubah Kata Sandi
            </h2>

            <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                        Kata Sandi Saat Ini <span class="text-[#E50914]">*</span>
                    </label>
                    <input 
                        id="current_password" 
                        name="current_password" 
                        type="password" 
                        required 
                        class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2 focus:outline-none focus:border-[#E50914]"
                    />
                </div>

                <div>
                    <label for="new_password" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                        Kata Sandi Baru <span class="text-[#E50914]">*</span>
                    </label>
                    <input 
                        id="new_password" 
                        name="password" 
                        type="password" 
                        required 
                        class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2 focus:outline-none focus:border-[#E50914]"
                        placeholder="Minimal 8 karakter"
                    />
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                        Ulangi Kata Sandi Baru <span class="text-[#E50914]">*</span>
                    </label>
                    <input 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        type="password" 
                        required 
                        class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2 focus:outline-none focus:border-[#E50914]"
                    />
                </div>

                <div class="pt-2">
                    <x-button type="submit" variant="secondary" size="md" class="w-full">
                        Perbarui Kata Sandi
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
