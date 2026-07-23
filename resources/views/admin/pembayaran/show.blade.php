@extends('layouts.admin')

@section('title', 'Detail Pembayaran #' . $pembayaran->id_pembayaran)

@section('content')

    @php
        $bayarBadge = match ($pembayaran->status_pembayaran) {
            'Lunas'                => 'badge-success',
            'Menunggu Verifikasi'  => 'badge-warning',
            default                => 'badge-neutral',
        };

        $isPdf = $pembayaran->bukti_transfer && str_ends_with(strtolower($pembayaran->bukti_transfer), '.pdf');
    @endphp

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.pembayaran.index') }}"
               class="w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-gray-900">Pembayaran #{{ $pembayaran->id_pembayaran }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">Untuk pemesanan #{{ $pembayaran->pemesanan->id_pemesanan }}</p>
            </div>
        </div>

        @if ($pembayaran->status_pembayaran === 'Menunggu Verifikasi')
            <div class="flex items-center gap-2">
                <form action="{{ route('admin.pembayaran.verify', $pembayaran) }}" method="POST"
                      onsubmit="return confirm('Konfirmasi pembayaran ini sebagai Lunas? Pemesanan akan otomatis disetujui.')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-primary">Konfirmasi Lunas</button>
                </form>
                <form action="{{ route('admin.pembayaran.reject', $pembayaran) }}" method="POST"
                      onsubmit="return confirm('Tolak bukti pembayaran ini? Penyewa akan diminta mengunggah ulang.')">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="text-sm font-semibold text-brand-orange hover:text-white hover:bg-brand-orange border border-brand-orange px-5 py-2.5 rounded-lg transition-colors">
                        Tolak
                    </button>
                </form>
            </div>
        @endif
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-lg bg-emerald-50 text-brand-teal text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-brand-orange-50 text-brand-orange text-sm font-medium">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Info pembayaran & pemesanan --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-800 text-sm">Informasi Pembayaran</h2>
                    <span class="{{ $bayarBadge }}">{{ $pembayaran->status_pembayaran }}</span>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                    <div>
                        <dt class="text-xs text-gray-400 mb-1">Penyewa</dt>
                        <dd class="text-gray-800 font-medium">{{ $pembayaran->pemesanan->user->nama }}</dd>
                        <dd class="text-xs text-gray-400 mt-0.5">{{ $pembayaran->pemesanan->user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 mb-1">Gedung</dt>
                        <dd class="text-gray-800 font-medium">{{ $pembayaran->pemesanan->gedung->nama_gedung }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 mb-1">Metode Pembayaran</dt>
                        <dd class="text-gray-800">{{ $pembayaran->metode_pembayaran }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 mb-1">Jumlah Dibayar</dt>
                        <dd class="font-semibold text-brand-teal text-base">
                            Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 mb-1">Total Tagihan Pemesanan</dt>
                        <dd class="text-gray-800">
                            Rp {{ number_format($pembayaran->pemesanan->total_harga, 0, ',', '.') }}
                        </dd>
                    </div>
                    @if ($pembayaran->tanggal_bayar)
                        <div>
                            <dt class="text-xs text-gray-400 mb-1">Tanggal Dikonfirmasi</dt>
                            <dd class="text-gray-800">{{ $pembayaran->tanggal_bayar->format('d M Y, H:i') }}</dd>
                        </div>
                    @endif
                </dl>

                <a href="{{ route('admin.pemesanan.show', $pembayaran->pemesanan) }}"
                   class="inline-block text-xs font-semibold text-brand-teal hover:underline mt-5">
                    Lihat Detail Pemesanan Terkait →
                </a>
            </div>
        </div>

        {{-- Bukti transfer --}}
        <div class="lg:col-span-1 card p-6 h-fit">
            <h2 class="font-semibold text-gray-800 text-sm mb-4">Bukti Transfer</h2>

            @if ($pembayaran->bukti_transfer)
                <a href="{{ asset('storage/' . $pembayaran->bukti_transfer) }}" target="_blank" rel="noopener">
                    @if ($isPdf)
                        <div class="w-full h-40 rounded-lg border border-gray-200 flex flex-col items-center justify-center gap-2 hover:bg-gray-50">
                            <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-xs font-semibold text-gray-500">Lihat File PDF</span>
                        </div>
                    @else
                        <img src="{{ asset('storage/' . $pembayaran->bukti_transfer) }}"
                             alt="Bukti transfer"
                             class="w-full rounded-lg border border-gray-200 object-cover hover:opacity-90 transition-opacity">
                    @endif
                </a>
                <p class="text-xs text-gray-400 text-center mt-2">Klik untuk memperbesar</p>
            @else
                <p class="text-sm text-gray-400">Belum ada bukti transfer yang diunggah.</p>
            @endif
        </div>
    </div>

@endsection
