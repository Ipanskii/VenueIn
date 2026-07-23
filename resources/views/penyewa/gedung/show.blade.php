@extends('layouts.app')

@section('title', $gedung->nama_gedung)

@section('content')

    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <a href="{{ route('beranda') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 mb-6">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Katalog
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Info gedung --}}
            <div class="lg:col-span-2 space-y-6">

                @if ($gedung->foto_url)
                    <img src="{{ $gedung->foto_url }}" alt="{{ $gedung->nama_gedung }}"
                         class="w-full h-56 sm:h-72 rounded-xl object-cover">
                @else
                    <div class="w-full h-56 sm:h-72 rounded-xl bg-gradient-to-br from-brand-teal-100 to-brand-teal-50
                                flex flex-col items-center justify-center gap-2">
                        <svg class="w-14 h-14 text-brand-teal/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                @endif

                <div>
                    <div class="flex items-start justify-between gap-3">
                        <h1 class="text-2xl font-extrabold text-gray-900">{{ $gedung->nama_gedung }}</h1>
                        @if ($gedung->status === 'Tersedia')
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-brand-teal shrink-0">
                                ✓ Tersedia
                            </span>
                        @else
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-brand-orange-50 text-brand-orange shrink-0">
                                ✗ Perbaikan
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 mt-1.5 flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ $gedung->alamat }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Kapasitas {{ number_format($gedung->kapasitas) }} orang
                    </p>
                </div>

                @if ($gedung->deskripsi)
                    <div>
                        <h2 class="font-semibold text-gray-800 text-sm mb-2">Deskripsi</h2>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $gedung->deskripsi }}</p>
                    </div>
                @endif

                @if ($gedung->fasilitas)
                    <div>
                        <h2 class="font-semibold text-gray-800 text-sm mb-2">Fasilitas</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach (explode(',', $gedung->fasilitas) as $fasilitas)
                                <span class="text-xs font-medium px-3 py-1.5 rounded-full bg-gray-100 text-gray-600">
                                    {{ trim($fasilitas) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($tanggalTerpesan->isNotEmpty())
                    <div>
                        <h2 class="font-semibold text-gray-800 text-sm mb-2">Tanggal yang Sudah Dipesan</h2>
                        <p class="text-xs text-gray-400 mb-2">Rentang tanggal berikut tidak tersedia untuk dipesan.</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($tanggalTerpesan as $rentang)
                                <span class="text-xs font-medium px-3 py-1.5 rounded-full bg-brand-orange-50 text-brand-orange">
                                    {{ \Illuminate\Support\Carbon::parse($rentang->tanggal_mulai)->format('d M Y') }}
                                    –
                                    {{ \Illuminate\Support\Carbon::parse($rentang->tanggal_selesai)->format('d M Y') }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Kartu pemesanan --}}
            <div class="lg:col-span-1">
                <div class="card p-6 sticky top-20">
                    <p class="text-brand-teal font-extrabold text-2xl">
                        Rp {{ number_format($gedung->harga_per_hari, 0, ',', '.') }}
                        <span class="text-sm font-normal text-gray-400">/ hari</span>
                    </p>

                    @if ($gedung->status === 'Tersedia')
                        <a href="{{ route('pemesanan.create', ['id_gedung' => $gedung->id_gedung]) }}"
                           class="btn-primary w-full mt-5">
                            Pesan Gedung Ini
                        </a>
                    @else
                        <button disabled class="btn-primary w-full mt-5 opacity-50 cursor-not-allowed">
                            Sedang Perbaikan
                        </button>
                    @endif

                    <p class="text-xs text-gray-400 text-center mt-3">
                        Anda tidak akan dikenakan biaya sampai pemesanan disetujui.
                    </p>
                </div>
            </div>
        </div>
    </section>

@endsection
