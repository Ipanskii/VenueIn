@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

    <div class="mb-6">
        <h1 class="text-xl font-extrabold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-0.5">
            Ringkasan aktivitas persewaan gedung hari ini, {{ now()->translatedFormat('d F Y') }}.
        </p>
    </div>

    {{-- ═══════════════════════════════════════════════
         KARTU STATISTIK
    ════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Gedung Tersedia</span>
                <div class="w-9 h-9 rounded-lg bg-brand-teal-50 flex items-center justify-center shrink-0">
                    <svg class="w-4.5 h-4.5 text-brand-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-gray-900">{{ number_format($stats['gedung_tersedia']) }}</p>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pemesanan Pending</span>
                <div class="w-9 h-9 rounded-lg bg-yellow-50 flex items-center justify-center shrink-0">
                    <svg class="w-4.5 h-4.5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-gray-900">{{ number_format($stats['pemesanan_pending']) }}</p>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pemesanan Bulan Ini</span>
                <div class="w-9 h-9 rounded-lg bg-brand-teal-50 flex items-center justify-center shrink-0">
                    <svg class="w-4.5 h-4.5 text-brand-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-gray-900">{{ number_format($stats['pemesanan_bulan_ini']) }}</p>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pendapatan Bulan Ini</span>
                <div class="w-9 h-9 rounded-lg bg-brand-orange-50 flex items-center justify-center shrink-0">
                    <svg class="w-4.5 h-4.5 text-brand-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v2m0-2c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-lg font-extrabold text-gray-900 leading-tight">
                Rp {{ number_format($stats['pendapatan_bulan_ini'], 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">dari pemesanan disetujui</p>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         PEMESANAN TERBARU
    ════════════════════════════════════════════════ --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-card">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800 text-sm">Pemesanan Terbaru</h2>
            <a href="{{ route('admin.pemesanan.index') }}" class="text-xs font-semibold text-brand-teal hover:underline">
                Lihat Semua →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-brand-teal text-white">
                    <tr>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">#</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Penyewa</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Gedung</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Tanggal Sewa</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Total</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 font-semibold text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($stats['pemesanan_terbaru'] as $pemesanan)
                        @php
                            $statusBadge = match ($pemesanan->status_pemesanan) {
                                'Disetujui'  => 'badge-success',
                                'Dibatalkan' => 'badge-danger',
                                default      => 'badge-neutral',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3.5 text-gray-400 font-mono text-xs">#{{ $pemesanan->id_pemesanan }}</td>
                            <td class="px-4 py-3.5 font-medium text-gray-900">{{ $pemesanan->user->nama }}</td>
                            <td class="px-4 py-3.5 text-gray-700 max-w-[160px] truncate">{{ $pemesanan->gedung->nama_gedung }}</td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-gray-600">
                                {{ $pemesanan->tanggal_mulai->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap font-semibold text-brand-teal">
                                Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="{{ $statusBadge }}">{{ $pemesanan->status_pemesanan }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <a href="{{ route('admin.pemesanan.show', $pemesanan) }}"
                                   class="text-brand-teal font-semibold text-xs hover:underline underline-offset-2 whitespace-nowrap">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-14 text-center text-sm text-gray-400">
                                Belum ada pemesanan yang masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
