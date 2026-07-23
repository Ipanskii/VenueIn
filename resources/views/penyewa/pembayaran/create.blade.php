@extends('layouts.app')

@section('title', 'Unggah Bukti Pembayaran')

@section('content')

    <section class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <a href="{{ route('pemesanan.show', $pemesanan) }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 mb-6">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Detail Pemesanan
        </a>

        <div class="mb-6">
            <h1 class="text-xl font-extrabold text-gray-900">Unggah Bukti Pembayaran</h1>
            <p class="text-sm text-gray-500 mt-0.5">Pemesanan #{{ $pemesanan->id_pemesanan }} — {{ $pemesanan->gedung->nama_gedung }}</p>
        </div>

        <div class="card p-5 mb-6 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400">Total Tagihan</p>
                <p class="text-lg font-extrabold text-brand-teal">
                    Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                </p>
            </div>
            <p class="text-xs text-gray-500 text-right">
                {{ $pemesanan->tanggal_mulai->format('d M Y') }} – {{ $pemesanan->tanggal_selesai->format('d M Y') }}
            </p>
        </div>

        @if ($pemesanan->pembayaran && $pemesanan->pembayaran->status_pembayaran === 'Belum Bayar')
            <div class="mb-6 p-4 rounded-lg bg-brand-orange-50 text-brand-orange text-sm font-medium">
                Bukti pembayaran sebelumnya ditolak. Silakan unggah ulang bukti transfer yang valid.
            </div>
        @endif

        <div class="card p-6 sm:p-8">
            <form method="POST" action="{{ route('pembayaran.store', $pemesanan) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label for="jumlah" class="input-label">Jumlah Dibayar (Rp)</label>
                    <input type="number" id="jumlah" name="jumlah" min="1"
                           value="{{ old('jumlah', (int) $pemesanan->total_harga) }}" class="input-field">
                    @error('jumlah') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="metode_pembayaran" class="input-label">Metode Pembayaran</label>
                    <select id="metode_pembayaran" name="metode_pembayaran" class="input-field bg-white">
                        <option value="">— Pilih Metode —</option>
                        @foreach (['Transfer Bank', 'QRIS', 'Virtual Account'] as $metode)
                            <option value="{{ $metode }}" {{ old('metode_pembayaran') === $metode ? 'selected' : '' }}>
                                {{ $metode }}
                            </option>
                        @endforeach
                    </select>
                    @error('metode_pembayaran') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="bukti_transfer" class="input-label">Bukti Transfer</label>
                    <input type="file" id="bukti_transfer" name="bukti_transfer" accept=".jpg,.jpeg,.png,.pdf"
                           class="input-field file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0
                                  file:text-xs file:font-semibold file:bg-brand-teal-50 file:text-brand-teal
                                  hover:file:bg-brand-teal-100">
                    <p class="text-xs text-gray-400 mt-1.5">Format JPG, PNG, atau PDF. Maksimal 2MB.</p>
                    @error('bukti_transfer') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary">Kirim Bukti Pembayaran</button>
                    <a href="{{ route('pemesanan.show', $pemesanan) }}" class="btn-ghost text-gray-500">Batal</a>
                </div>
            </form>
        </div>
    </section>

@endsection
