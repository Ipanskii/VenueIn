@extends('layouts.app')

@section('title', 'Pemesanan Saya')

@section('content')

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-extrabold text-gray-900">Pemesanan Saya</h1>
                <p class="text-sm text-gray-500 mt-0.5">Riwayat dan status pemesanan gedung Anda.</p>
            </div>
            <a href="{{ route('beranda') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Pesan Gedung Baru
            </a>
        </div>

        <div class="space-y-4">
            @forelse ($pemesanans as $pemesanan)
                @php
                    $statusBadge = match ($pemesanan->status_pemesanan) {
                        'Disetujui'  => 'badge-success',
                        'Dibatalkan' => 'badge-danger',
                        default      => 'badge-neutral',
                    };

                    $bayarBadge = match ($pemesanan->pembayaran?->status_pembayaran) {
                        'Lunas'                => 'badge-success',
                        'Menunggu Verifikasi'  => 'badge-warning',
                        default                => 'badge-neutral',
                    };
                @endphp

                <div class="card p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="font-semibold text-gray-900">{{ $pemesanan->gedung->nama_gedung }}</h2>
                            <span class="{{ $statusBadge }}">{{ $pemesanan->status_pemesanan }}</span>
                            @if ($pemesanan->pembayaran)
                                <span class="{{ $bayarBadge }}">{{ $pemesanan->pembayaran->status_pembayaran }}</span>
                            @else
                                <span class="badge-neutral">Belum Bayar</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $pemesanan->tanggal_mulai->format('d M Y') }} – {{ $pemesanan->tanggal_selesai->format('d M Y') }}
                        </p>
                        <p class="text-sm font-semibold text-brand-teal mt-1">
                            Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('pemesanan.show', $pemesanan) }}" class="btn-secondary text-xs">
                            Lihat Detail
                        </a>

                        @if ($pemesanan->status_pemesanan === 'Pending' && ! $pemesanan->pembayaran)
                            <a href="{{ route('pembayaran.create', $pemesanan) }}" class="btn-primary text-xs">
                                Bayar Sekarang
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                        <svg class="w-9 h-9 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700">Belum ada pemesanan</h3>
                    <p class="text-sm text-gray-400 mt-1 max-w-xs">
                        Cari gedung yang cocok untuk acara Anda dan buat pemesanan pertama.
                    </p>
                    <a href="{{ route('beranda') }}" class="btn-primary mt-5">Cari Gedung</a>
                </div>
            @endforelse
        </div>

        @if ($pemesanans->hasPages())
            <div class="mt-8">
                {{ $pemesanans->links() }}
            </div>
        @endif
    </section>

@endsection
