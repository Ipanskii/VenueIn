@extends('layouts.app')

@section('title', 'Temukan Gedung')

@section('content')

    {{-- ═══════════════════════════════════════════════
         HERO — bg brand-teal (30% proporsi)
    ════════════════════════════════════════════════ --}}
    <section class="bg-brand-teal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20">
            <div class="text-center max-w-2xl mx-auto">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">
                    Temukan Gedung Impian <br class="hidden sm:block">untuk Acara Anda
                </h1>
                <p class="mt-4 text-white/70 text-base sm:text-lg">
                    Pilih dari ratusan gedung berkualitas. Pesan dengan mudah, acara berjalan lancar.
                </p>
            </div>

            {{-- Search bar --}}
            <form method="GET" action="{{ route('beranda') }}"
                  class="mt-8 max-w-2xl mx-auto">
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text"
                               name="q"
                               value="{{ request('q') }}"
                               placeholder="Cari nama gedung, lokasi, atau fasilitas..."
                               class="input-field pl-10 shadow-sm">
                    </div>
                    <button type="submit" class="btn-primary px-6 shadow-sm">Cari</button>
                </div>
            </form>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════
         FILTER & KATALOG — bg white (60% proporsi)
    ════════════════════════════════════════════════ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Filter lanjutan --}}
        <form method="GET" action="{{ route('beranda') }}"
              class="flex flex-wrap items-end gap-3 mb-8 p-4 bg-gray-50 rounded-xl border border-gray-200">
            <input type="hidden" name="q" value="{{ request('q') }}">

            <div class="flex-1 min-w-[160px]">
                <label class="input-label">Kapasitas Min. (orang)</label>
                <input type="number"
                       name="kapasitas"
                       min="1"
                       value="{{ request('kapasitas') }}"
                       placeholder="Contoh: 100"
                       class="input-field">
            </div>

            <div class="flex-1 min-w-[160px]">
                <label class="input-label">Harga Maks. (Rp/hari)</label>
                <input type="number"
                       name="harga_max"
                       min="0"
                       value="{{ request('harga_max') }}"
                       placeholder="Contoh: 5000000"
                       class="input-field">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="btn-secondary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 4a1 1 0 011-1h16a1 1 0 010 2H4a1 1 0 01-1-1zm3 4a1 1 0 011-1h10a1 1 0 010 2H7a1 1 0 01-1-1zm3 4a1 1 0 011-1h4a1 1 0 010 2h-4a1 1 0 01-1-1z"/>
                    </svg>
                    Filter
                </button>

                @if (request()->anyFilled(['q', 'kapasitas', 'harga_max']))
                    <a href="{{ route('beranda') }}" class="btn-ghost text-gray-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- Result count --}}
        <div class="flex items-center justify-between mb-5">
            <p class="text-sm text-gray-500">
                Menampilkan <span class="font-semibold text-gray-800">{{ $gedungs->total() }}</span> gedung tersedia
                @if (request()->anyFilled(['q', 'kapasitas', 'harga_max']))
                    (difilter)
                @endif
            </p>
        </div>

        {{-- ── Grid Kartu Gedung ────────────────────────────────────────── --}}
        {{--
            Mengikuti spesifikasi PRD Bagian 5.3.
            Elemen aksen oranye (10%) hanya pada tombol "Pesan".
        --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($gedungs as $gedung)
                <article class="bg-white border border-gray-200 rounded-xl shadow-card overflow-hidden
                                hover:shadow-card-hover hover:-translate-y-0.5 transition-all duration-200 flex flex-col">

                    {{-- Gambar gedung / placeholder --}}
                    @if (isset($gedung->foto_url) && $gedung->foto_url)
                        <img src="{{ $gedung->foto_url }}"
                             class="w-full h-44 object-cover"
                             alt="{{ $gedung->nama_gedung }}"
                             loading="lazy">
                    @else
                        <div class="w-full h-44 bg-gradient-to-br from-brand-teal-100 to-brand-teal-50
                                    flex flex-col items-center justify-center gap-2">
                            <svg class="w-10 h-10 text-brand-teal/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="text-xs text-brand-teal/40 font-medium">Foto Gorong Onok</span>
                        </div>
                    @endif

                    <div class="p-4 flex flex-col flex-1">
                        <h3 class="font-sans font-semibold text-lg text-gray-900 leading-snug line-clamp-1">
                            {{ $gedung->nama_gedung }}
                        </h3>

                        <p class="text-sm text-gray-500 mt-1 line-clamp-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="truncate min-w-0" title="{{ $gedung->alamat }}">{{ $gedung->alamat }}</span>
                        </p>

                        {{-- Kapasitas --}}
                        <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Kapasitas {{ number_format($gedung->kapasitas) }} orang
                        </p>

                        <div class="flex items-center justify-between mt-3">
                            <span class="text-brand-teal font-bold text-sm sm:text-base">
                                Rp {{ number_format($gedung->harga_per_hari, 0, ',', '.') }}
                                <span class="text-xs font-normal text-gray-400">/ hari</span>
                            </span>

                            @if ($gedung->status === 'Tersedia')
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-brand-teal">
                                    ✓ Tersedia
                                </span>
                            @else
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-brand-orange-50 text-brand-orange">
                                    ✗ Perbaikan
                                </span>
                            @endif
                        </div>

                        {{-- CTA Pesan — aksen oranye (10% proporsi) --}}
                        <a href="{{ route('gedung.show', $gedung) }}"
                           class="mt-auto pt-4 btn-primary w-full">
                            Pesan Gedung Ini
                        </a>
                    </div>
                </article>

            @empty
                {{-- Empty state --}}
                <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                        <svg class="w-9 h-9 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700">Tidak ada gedung ditemukan</h3>
                    <p class="text-sm text-gray-400 mt-1 max-w-xs">
                        Coba ubah kata kunci pencarian atau hapus filter yang aktif.
                    </p>
                    @if (request()->anyFilled(['q', 'kapasitas', 'harga_max']))
                        <a href="{{ route('beranda') }}" class="btn-secondary mt-5">
                            Tampilkan Semua Gedung
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($gedungs->hasPages())
            <div class="mt-10">
                {{ $gedungs->links() }}
            </div>
        @endif

    </section>

@endsection
