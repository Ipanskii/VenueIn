@extends('layouts.admin')

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

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.pemesanan.index') }}"
               class="w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-gray-900">Pemesanan #{{ $pemesanan->id_pemesanan }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">Dibuat {{ $pemesanan->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>

        @if ($pemesanan->status_pemesanan === 'Pending')
            <div class="flex items-center gap-2">
                <form action="{{ route('admin.pemesanan.approve', $pemesanan) }}" method="POST"
                      onsubmit="return confirm('Setujui pemesanan #{{ $pemesanan->id_pemesanan }}?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-primary">Setujui Pemesanan</button>
                </form>
                <form action="{{ route('admin.pemesanan.cancel', $pemesanan) }}" method="POST"
                      onsubmit="return confirm('Batalkan pemesanan ini? Tindakan tidak dapat diurungkan.')">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="text-sm font-semibold text-brand-orange hover:text-white hover:bg-brand-orange border border-brand-orange px-5 py-2.5 rounded-lg transition-colors">
                        Batalkan
                    </button>
                </form>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Info penyewa & gedung --}}
        <div class="lg:col-span-2 space-y-6">

            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-800 text-sm">Informasi Pemesanan</h2>
                    <span class="{{ $statusBadge }}">{{ $pemesanan->status_pemesanan }}</span>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                    <div>
                        <dt class="text-xs text-gray-400 mb-1">Penyewa</dt>
                        <dd class="text-gray-800 font-medium">{{ $pemesanan->user->nama }}</dd>
                        <dd class="text-xs text-gray-400 mt-0.5">{{ $pemesanan->user->email }}</dd>
                        <dd class="text-xs text-gray-400">{{ $pemesanan->user->no_telepon ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 mb-1">Gedung</dt>
                        <dd class="text-gray-800 font-medium">{{ $pemesanan->gedung->nama_gedung }}</dd>
                        <dd class="text-xs text-gray-400 mt-0.5">{{ $pemesanan->gedung->alamat }}</dd>
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
        </div>

        {{-- Info pembayaran --}}
        <div class="lg:col-span-1 card p-6 h-fit">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-800 text-sm">Pembayaran</h2>
                @if ($pemesanan->pembayaran)
                    <span class="{{ $bayarBadge }}">{{ $pemesanan->pembayaran->status_pembayaran }}</span>
                @else
                    <span class="badge-neutral">Belum Ada</span>
                @endif
            </div>

            @if ($pemesanan->pembayaran)
                <dl class="space-y-4 text-sm">
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
                    @if ($pemesanan->pembayaran->tanggal_bayar)
                        <div>
                            <dt class="text-xs text-gray-400 mb-1">Tanggal Bayar</dt>
                            <dd class="text-gray-800">{{ $pemesanan->pembayaran->tanggal_bayar->format('d M Y, H:i') }}</dd>
                        </div>
                    @endif
                </dl>

                <a href="{{ route('admin.pembayaran.show', $pemesanan->pembayaran) }}" class="btn-secondary w-full mt-5 text-xs">
                    Lihat Detail Pembayaran
                </a>
            @else
                <p class="text-sm text-gray-400">Penyewa belum mengunggah bukti pembayaran.</p>
            @endif
        </div>
    </div>

@endsection
