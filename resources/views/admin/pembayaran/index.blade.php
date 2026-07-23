@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran')

@section('content')

    @php
        $statusFilter = request('status');
        $filters = [
            ''                     => 'Semua',
            'Menunggu Verifikasi'  => 'Menunggu Verifikasi',
            'Lunas'                => 'Lunas',
            'Belum Bayar'          => 'Belum Bayar',
        ];
    @endphp

    <div class="mb-6">
        <h1 class="text-xl font-extrabold text-gray-900">Verifikasi Pembayaran</h1>
        <p class="text-sm text-gray-500 mt-0.5">Tinjau dan konfirmasi bukti transfer yang diunggah penyewa.</p>
    </div>

    <div class="flex flex-wrap gap-2 mb-4">
        @foreach ($filters as $value => $label)
            <a href="{{ route('admin.pembayaran.index', $value ? ['status' => $value] : []) }}"
               class="text-xs font-semibold px-3.5 py-1.5 rounded-full border transition-colors
                      {{ (string) $statusFilter === (string) $value
                            ? 'bg-brand-teal text-white border-brand-teal'
                            : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-brand-teal text-white">
                    <tr>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">#</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Penyewa</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Gedung</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Metode</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Jumlah</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 font-semibold text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pembayarans as $pembayaran)
                        @php
                            $bayarBadge = match ($pembayaran->status_pembayaran) {
                                'Lunas'                => 'badge-success',
                                'Menunggu Verifikasi'  => 'badge-warning',
                                default                => 'badge-neutral',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3.5 text-gray-400 font-mono text-xs">#{{ $pembayaran->id_pembayaran }}</td>
                            <td class="px-4 py-3.5 font-medium text-gray-900">{{ $pembayaran->pemesanan->user->nama }}</td>
                            <td class="px-4 py-3.5 text-gray-700 max-w-[160px] truncate">{{ $pembayaran->pemesanan->gedung->nama_gedung }}</td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-gray-600">{{ $pembayaran->metode_pembayaran }}</td>
                            <td class="px-4 py-3.5 whitespace-nowrap font-semibold text-brand-teal">
                                Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="{{ $bayarBadge }}">{{ $pembayaran->status_pembayaran }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <a href="{{ route('admin.pembayaran.show', $pembayaran) }}"
                                   class="text-brand-teal font-semibold text-xs hover:underline underline-offset-2 whitespace-nowrap">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center text-sm text-gray-400">
                                Tidak ada data pembayaran untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pembayarans->hasPages())
            <div class="border-t border-gray-100 px-4 py-3">
                {{ $pembayarans->links() }}
            </div>
        @endif
    </div>

@endsection
