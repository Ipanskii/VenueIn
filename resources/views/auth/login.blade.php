@extends('layouts.app')

@section('title', 'Masuk')

@section('content')

    <section class="min-h-[calc(100vh-4rem-6rem)] flex items-center justify-center bg-gray-50 px-4 py-12">
        <div class="w-full max-w-md">

            <div class="bg-white border border-gray-200 rounded-2xl shadow-card p-8">

                {{-- Header --}}
                <div class="text-center mb-8">
                    <div class="w-12 h-12 rounded-xl bg-brand-teal/10 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-brand-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                    </div>
                    <h1 class="text-xl font-extrabold text-gray-900">Masuk ke VenueIn</h1>
                    <p class="text-sm text-gray-500 mt-1.5">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-brand-teal font-semibold hover:underline">
                            Daftar sekarang
                        </a>
                    </p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="input-label">Email</label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="nama@email.com"
                               autofocus
                               required
                               class="input-field @error('email') border-red-400 focus:ring-red-400 focus:border-red-400 @enderror">
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="input-label mb-0">Kata Sandi</label>
                        </div>
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder="••••••••"
                               required
                               class="input-field @error('password') border-red-400 focus:ring-red-400 focus:border-red-400 @enderror">
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-2 text-sm text-gray-600 select-none">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-teal focus:ring-brand-teal">
                        Ingat saya
                    </label>

                    <button type="submit" class="btn-primary w-full py-2.5">
                        Masuk
                    </button>
                </form>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                <a href="{{ route('beranda') }}" class="hover:text-gray-600">← Kembali ke Beranda</a>
            </p>
        </div>
    </section>

@endsection
