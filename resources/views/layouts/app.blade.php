<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>@yield('title', 'VenueIn') — Sistem Persewaan Gedung</title>

    {{-- Plus Jakarta Sans via Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full flex flex-col bg-white">

    {{-- ═══════════════════════════════════════════════
         NAVBAR
    ════════════════════════════════════════════════ --}}
    <header class="bg-brand-teal shadow-md sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ route('beranda') }}" class="flex items-center gap-2 shrink-0">
                    <div class="w-16 h-16 rounded-lg flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo VenueIn" 
                            class="w-full h-full object-contain"
                            style="filter: drop-shadow(1px 0 0 white) drop-shadow(-1px 0 0 white) drop-shadow(0 1px 0 white) drop-shadow(0 -1px 0 white);">
                    </div>
                    <span class="text-white font-extrabold text-lg tracking-tight">
                        Venue<span class="text-brand-orange">In</span>
                    </span>
                </a>

                {{-- Desktop Navigation --}}
                <div class="hidden md:flex items-center gap-1">
                    @guest
                        <a href="{{ route('beranda') }}" class="nav-link px-3 py-2 rounded-lg hover:bg-white/10">Beranda</a>
                        <div class="w-px h-5 bg-white/25 mx-2"></div>
                        <a href="{{ route('login') }}" class="nav-link px-4 py-2 rounded-lg hover:bg-white/10">Masuk</a>
                        <a href="{{ route('register') }}" class="btn-primary ml-2">Daftar Sekarang</a>
                    @endguest

                    @auth
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                               class="nav-link px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : '' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('admin.gedung.index') }}"
                               class="nav-link px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.gedung.*') ? 'nav-link-active' : '' }}">
                                Gedung
                            </a>
                            <a href="{{ route('admin.pemesanan.index') }}"
                               class="nav-link px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.pemesanan.*') ? 'nav-link-active' : '' }}">
                                Pemesanan
                            </a>
                            <a href="{{ route('admin.pembayaran.index') }}"
                               class="nav-link px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.pembayaran.*') ? 'nav-link-active' : '' }}">
                                Pembayaran
                            </a>
                            <a href="{{ route('admin.users.index') }}"
                               class="nav-link px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.users.*') ? 'nav-link-active' : '' }}">
                                Pengguna
                            </a>
                        @else
                            <a href="{{ route('beranda') }}"
                               class="nav-link px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('beranda') ? 'nav-link-active' : '' }}">
                                Beranda
                            </a>
                            <a href="{{ route('pemesanan.index') }}"
                               class="nav-link px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('pemesanan.*') ? 'nav-link-active' : '' }}">
                                Pemesanan Saya
                            </a>
                        @endif

                        {{-- User dropdown --}}
                        <div class="relative ml-3" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center gap-2 text-white/80 hover:text-white text-sm font-medium px-3 py-2 rounded-lg hover:bg-white/10 transition-colors">
                                <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold uppercase">
                                    {{ substr(auth()->user()->nama, 0, 1) }}
                                </div>
                                <span class="hidden lg:block max-w-[120px] truncate">{{ auth()->user()->nama }}</span>
                                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" @click.outside="open = false" x-transition
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                                <a href="{{ route('profile.edit') }}"
                                   class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Edit Profil
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-brand-orange hover:bg-brand-orange-50 text-left">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>

                {{-- Mobile hamburger --}}
                <button id="nav-toggle" type="button" aria-label="Buka menu"
                        class="md:hidden p-2 rounded-lg text-white hover:bg-white/10 transition-colors">
                    <svg id="icon-menu" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="icon-close" class="w-6 h-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </nav>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-white/10">
            <div class="max-w-7xl mx-auto px-4 py-3 space-y-1">
                @guest
                    <a href="{{ route('beranda') }}" class="block nav-link px-3 py-2.5 rounded-lg hover:bg-white/10">Beranda</a>
                    <a href="{{ route('login') }}" class="block nav-link px-3 py-2.5 rounded-lg hover:bg-white/10">Masuk</a>
                    <a href="{{ route('register') }}" class="block nav-link px-3 py-2.5 rounded-lg hover:bg-white/10 font-semibold text-brand-orange">Daftar Sekarang</a>
                @endguest

                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block nav-link px-3 py-2.5 rounded-lg hover:bg-white/10">Dashboard</a>
                        <a href="{{ route('admin.gedung.index') }}" class="block nav-link px-3 py-2.5 rounded-lg hover:bg-white/10">Gedung</a>
                        <a href="{{ route('admin.pemesanan.index') }}" class="block nav-link px-3 py-2.5 rounded-lg hover:bg-white/10">Pemesanan</a>
                        <a href="{{ route('admin.pembayaran.index') }}" class="block nav-link px-3 py-2.5 rounded-lg hover:bg-white/10">Pembayaran</a>
                        <a href="{{ route('admin.users.index') }}" class="block nav-link px-3 py-2.5 rounded-lg hover:bg-white/10">Pengguna</a>
                    @else
                        <a href="{{ route('beranda') }}" class="block nav-link px-3 py-2.5 rounded-lg hover:bg-white/10">Beranda</a>
                        <a href="{{ route('pemesanan.index') }}" class="block nav-link px-3 py-2.5 rounded-lg hover:bg-white/10">Pemesanan Saya</a>
                    @endif

                    <div class="border-t border-white/10 pt-2 mt-2">
                        <a href="{{ route('profile.edit') }}" class="block nav-link px-3 py-2.5 rounded-lg hover:bg-white/10">Edit Profil</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="block w-full text-left nav-link px-3 py-2.5 rounded-lg hover:bg-white/10 text-brand-orange font-medium">
                                Keluar
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    {{-- ═══════════════════════════════════════════════
         FLASH MESSAGES
    ════════════════════════════════════════════════ --}}
    @if (session('success'))
        <div id="flash-success" role="alert"
             class="flex items-center justify-between gap-3 bg-emerald-50 border-b border-emerald-200 px-4 py-3 text-sm text-brand-teal">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
            <button onclick="document.getElementById('flash-success').remove()"
                    class="text-brand-teal/60 hover:text-brand-teal shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div id="flash-error" role="alert"
             class="flex items-start justify-between gap-3 bg-brand-orange-50 border-b border-brand-orange-100 px-4 py-3">
            <div class="flex items-start gap-2 text-sm text-brand-orange">
                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <ul class="space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button onclick="document.getElementById('flash-error').remove()"
                    class="text-brand-orange/60 hover:text-brand-orange shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         KONTEN UTAMA
    ════════════════════════════════════════════════ --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- ═══════════════════════════════════════════════
         FOOTER
    ════════════════════════════════════════════════ --}}
    <footer class="bg-brand-teal mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <span class="text-white font-extrabold text-base tracking-tight">
                    Venue<span class="text-brand-orange">In</span>
                </span>
                <p class="text-white/50 text-xs text-center">
                    &copy; {{ date('Y') }} VenueIn. Sistem Informasi Persewaan Gedung.
                </p>
            </div>
        </div>
    </footer>

    {{-- Mobile menu toggle (vanilla JS, tanpa dependensi) --}}
    <script>
        const navToggle    = document.getElementById('nav-toggle');
        const mobileMenu   = document.getElementById('mobile-menu');
        const iconMenu     = document.getElementById('icon-menu');
        const iconClose    = document.getElementById('icon-close');

        navToggle.addEventListener('click', () => {
            const isOpen = !mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden', isOpen);
            iconMenu.classList.toggle('hidden', !isOpen);
            iconClose.classList.toggle('hidden', isOpen);
            navToggle.setAttribute('aria-label', isOpen ? 'Buka menu' : 'Tutup menu');
        });
    </script>

    {{-- Alpine.js CDN (untuk dropdown user desktop) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')
</body>
</html>
