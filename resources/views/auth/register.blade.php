@extends('layouts.app')

@section('title', 'Daftar Akun')

@section('content')

    <section class="min-h-[calc(100vh-4rem-6rem)] flex items-center justify-center bg-gray-50 px-4 py-12">
        <div class="w-full max-w-md">

            <div class="bg-white border border-gray-200 rounded-2xl shadow-card p-8">

                {{-- Header --}}
                <div class="text-center mb-8">
                    <div class="w-12 h-12 rounded-xl bg-brand-orange/10 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-brand-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <h1 class="text-xl font-extrabold text-gray-900">Buat Akun Baru</h1>
                    <p class="text-sm text-gray-500 mt-1.5">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-brand-teal font-semibold hover:underline">
                            Masuk di sini
                        </a>
                    </p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="nama" class="input-label">Nama Lengkap</label>
                        <input type="text"
                               id="nama"
                               name="nama"
                               value="{{ old('nama') }}"
                               placeholder="Nama lengkap Anda"
                               autofocus
                               required
                               class="input-field @error('nama') border-red-400 focus:ring-red-400 focus:border-red-400 @enderror">
                        @error('nama')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="input-label">Email</label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="nama@email.com"
                               required
                               class="input-field @error('email') border-red-400 focus:ring-red-400 focus:border-red-400 @enderror">
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no_telepon" class="input-label">No. Telepon <span class="font-normal text-gray-400">(opsional)</span></label>
                        <input type="text"
                               id="no_telepon"
                               name="no_telepon"
                               value="{{ old('no_telepon') }}"
                               placeholder="08xxxxxxxxxx"
                               class="input-field @error('no_telepon') border-red-400 focus:ring-red-400 focus:border-red-400 @enderror">
                        @error('no_telepon')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="input-label">Kata Sandi</label>
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder="Minimal 8 karakter"
                               required
                               class="input-field @error('password') border-red-400 focus:ring-red-400 focus:border-red-400 @enderror">
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="input-label">Konfirmasi Kata Sandi</label>
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               placeholder="Ulangi kata sandi"
                               required
                               class="input-field">
                    </div>

                    <button type="submit" class="btn-primary w-full py-2.5">
                        Daftar Sekarang
                    </button>
                </form>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                <a href="{{ route('beranda') }}" class="hover:text-gray-600">← Kembali ke Beranda</a>
            </p>
        </div>
    </section>

@endsection
