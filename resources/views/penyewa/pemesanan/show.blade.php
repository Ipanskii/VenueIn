@extends('layouts.app')

@section('title', 'Detail Pemesanan #' . $pemesanan->id_pemesanan)

@section('content')

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

        $durasi = $pemesanan->tanggal_mulai->diffInDays($pemesanan->tanggal_selesai) + 1;
    @endphp

    <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <a href="{{ route('pemesanan.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 mb-6">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Pemesanan Saya
        </a>

        <div class="flex items-start justify-between gap-3 mb-6">
            <div>
                <h1 class="text-xl font-extrabold text-gray-900">Pemesanan #{{ $pemesanan->id_pemesanan }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">Dibuat {{ $pemesanan->created_at->format('d M Y, H:i') }}</p>
            </div>
            <span class="{{ $statusBadge }} shrink-0">{{ $pemesanan->status_pemesanan }}</span>
        </div>

        <div class="card p-6 mb-6">
            <h2 class="font-semibold text-gray-800 text-sm mb-4">Detail Gedung</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Nama Gedung</dt>
                    <dd class="text-gray-800 font-medium">{{ $pemesanan->gedung->nama_gedung }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Alamat</dt>
                    <dd class="text-gray-800">{{ $pemesanan->gedung->alamat }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Tanggal Sewa</dt>
                    <dd class="text-gray-800">
                        {{ $pemesanan->tanggal_mulai->format('d M Y') }} – {{ $pemesanan->tanggal_selesai->format('d M Y') }}
                    </dd>
                    <dd class="text-xs text-gray-400 mt-0.5">{{ $durasi }} hari</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Total Harga</dt>
                    <dd class="font-semibold text-brand-teal text-base">
                        Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                    </dd>
                </div>
            </dl>
        </div>

        <div class="card p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-800 text-sm">Status Pembayaran</h2>
                @if ($pemesanan->pembayaran)
                    <span class="{{ $bayarBadge }}">{{ $pemesanan->pembayaran->status_pembayaran }}</span>
                @else
                    <span class="badge-neutral">Belum Bayar</span>
                @endif
            </div>

            @if ($pemesanan->pembayaran)
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                    <div>
                        <dt class="text-xs text-gray-400 mb-1">Metode</dt>
                        <dd class="text-gray-800">{{ $pemesanan->pembayaran->metode_pembayaran }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 mb-1">Jumlah</dt>
                        <dd class="font-semibold text-gray-800">
                            Rp {{ number_format($pemesanan->pembayaran->jumlah, 0, ',', '.') }}
                        </dd>
                    </div>
                </dl>
            @else
                <p class="text-sm text-gray-500 mb-4">Anda belum mengunggah bukti pembayaran untuk pemesanan ini.</p>
                @if ($pemesanan->status_pemesanan === 'Pending')
                    <a href="{{ route('pembayaran.create', $pemesanan) }}" class="btn-primary text-sm">
                        Unggah Bukti Pembayaran
                    </a>
                @endif
            @endif
        </div>

        @if ($pemesanan->status_pemesanan === 'Pending')
            <form action="{{ route('pemesanan.batal', $pemesanan) }}" method="POST"
                  onsubmit="return confirm('Batalkan pemesanan ini? Tindakan tidak dapat diurungkan.')">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="text-sm font-semibold text-brand-orange hover:text-white hover:bg-brand-orange border border-brand-orange px-5 py-2.5 rounded-lg transition-colors">
                    Batalkan Pemesanan
                </button>
            </form>
        @endif
    </section>

@endsection
