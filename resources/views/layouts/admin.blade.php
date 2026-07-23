<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>@yield('title', 'Admin') — VenueIn</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full font-sans antialiased bg-gray-50" x-data="{ sidebarOpen: false }">

    <div class="flex h-full">
        {{-- ═══════════════════════════════════
             SIDEBAR
        ════════════════════════════════════ --}}
        {{-- Overlay for mobile --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
             class="fixed inset-0 z-20 bg-black/40 md:hidden" x-transition.opacity></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-30 w-64 bg-brand-teal
                      flex flex-col transform transition-transform duration-200 ease-in-out
                      md:relative md:translate-x-0 md:flex-shrink-0">

            {{-- Sidebar brand --}}
            <div class="flex items-center gap-2.5 h-16 px-5 border-b border-white/10 shrink-0">
                <div class="w-16 h-16 rounded-lg flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo VenueIn" 
                            class="w-full h-full object-contain"
                            style="filter: drop-shadow(1px 0 0 white) drop-shadow(-1px 0 0 white) drop-shadow(0 1px 0 white) drop-shadow(0 -1px 0 white);">
                </div>
                <a href="{{ route('admin.dashboard') }}" class="text-white font-extrabold text-lg tracking-tight">
                    Venue<span class="text-brand-orange">In</span>
                    <span class="block text-[10px] font-normal text-white/50 leading-none -mt-0.5">Panel Admin</span>
                </a>
            </div>

            {{-- Sidebar navigation --}}
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">

                @php
                    $sidebarLinks = [
                        ['route' => 'admin.dashboard',         'label' => 'Dashboard',       'pattern' => 'admin.dashboard',     'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['route' => 'admin.gedung.index',      'label' => 'Kelola Gedung',    'pattern' => 'admin.gedung.*',      'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                        ['route' => 'admin.pemesanan.index',   'label' => 'Pemesanan',        'pattern' => 'admin.pemesanan.*',   'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                        ['route' => 'admin.pembayaran.index',  'label' => 'Pembayaran',       'pattern' => 'admin.pembayaran.*',  'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                        ['route' => 'admin.users.index',       'label' => 'Pengguna',         'pattern' => 'admin.users.*',       'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                    ];
                @endphp

                @foreach ($sidebarLinks as $link)
                    @php $isActive = request()->routeIs($link['pattern']); @endphp
                    <a href="{{ route($link['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ $isActive
                                  ? 'bg-white text-brand-teal shadow-sm'
                                  : 'text-white/75 hover:text-white hover:bg-white/10' }}">
                        <svg class="w-5 h-5 shrink-0 {{ $isActive ? 'text-brand-teal' : 'text-white/60' }}"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/>
                        </svg>
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Sidebar user info --}}
            <div class="shrink-0 border-t border-white/10 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold text-white uppercase shrink-0">
                        {{ substr(auth()->user()->nama, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->nama }}</p>
                        <p class="text-xs text-white/50 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Keluar"
                                class="p-1.5 rounded-lg text-white/50 hover:text-white hover:bg-white/10 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- ═══════════════════════════════════
             AREA KONTEN UTAMA
        ════════════════════════════════════ --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            {{-- Top bar mobile --}}
            <div class="md:hidden flex items-center justify-between h-14 px-4 bg-brand-teal border-b border-white/10 shrink-0">
                <button @click="sidebarOpen = true"
                        class="p-2 rounded-lg text-white hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <span class="text-white font-extrabold text-base">
                    Venue<span class="text-brand-orange">In</span>
                </span>
                <div class="w-9"></div>
            </div>

            {{-- Flash messages --}}
            @if (session('success'))
                <div id="flash-success" role="alert"
                     class="flex items-center justify-between gap-3 bg-emerald-50 border-b border-emerald-200 px-4 py-3 text-sm text-brand-teal shrink-0">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                    <button onclick="document.getElementById('flash-success').remove()" class="text-brand-teal/60 hover:text-brand-teal">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div id="flash-error" role="alert"
                     class="flex items-start justify-between gap-3 bg-brand-orange-50 border-b border-brand-orange-100 px-4 py-3 shrink-0">
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
                    <button onclick="document.getElementById('flash-error').remove()" class="text-brand-orange/60 hover:text-brand-orange shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
</body>
</html>
