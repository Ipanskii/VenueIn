@extends('layouts.admin')

@section('title', $gedung->nama_gedung)

@section('content')

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.gedung.index') }}"
               class="w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-gray-900">{{ $gedung->nama_gedung }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">Dikelola oleh {{ $gedung->admin->nama ?? '—' }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.gedung.edit', $gedung) }}" class="btn-secondary">Ubah</a>
            <form action="{{ route('admin.gedung.destroy', $gedung) }}" method="POST"
                  onsubmit="return confirm('Hapus gedung &quot;{{ $gedung->nama_gedung }}&quot;? Tindakan ini tidak dapat diurungkan.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="text-sm font-semibold text-brand-orange hover:text-white hover:bg-brand-orange border border-brand-orange px-5 py-2.5 rounded-lg transition-colors">
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Detail gedung --}}
        <div class="lg:col-span-1 card p-6 h-fit">
            @if ($gedung->foto_url)
                <img src="{{ $gedung->foto_url }}" alt="{{ $gedung->nama_gedung }}"
                     class="w-full h-40 object-cover rounded-lg border border-gray-200 mb-4">
            @else
                <div class="w-full h-40 rounded-lg bg-gray-50 border border-dashed border-gray-200
                            flex flex-col items-center justify-center gap-1.5 mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span class="text-xs text-gray-400">Belum ada foto</span>
                </div>
            @endif

            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-800 text-sm">Detail Gedung</h2>
                @if ($gedung->status === 'Tersedia')
                    <span class="badge-success">Tersedia</span>
                @else
                    <span class="badge-warning">Perbaikan</span>
                @endif
            </div>

            <dl class="space-y-4 text-sm">
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Alamat</dt>
                    <dd class="text-gray-800">{{ $gedung->alamat }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Kapasitas</dt>
                    <dd class="text-gray-800">{{ number_format($gedung->kapasitas) }} orang</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Harga per Hari</dt>
                    <dd class="font-semibold text-brand-teal">Rp {{ number_format($gedung->harga_per_hari, 0, ',', '.') }}</dd>
                </div>
                @if ($gedung->fasilitas)
                    <div>
                        <dt class="text-xs text-gray-400 mb-1">Fasilitas</dt>
                        <dd class="text-gray-800">{{ $gedung->fasilitas }}</dd>
                    </div>
                @endif
                @if ($gedung->deskripsi)
                    <div>
                        <dt class="text-xs text-gray-400 mb-1">Deskripsi</dt>
                        <dd class="text-gray-600 leading-relaxed">{{ $gedung->deskripsi }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Riwayat pemesanan terbaru --}}
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl overflow-hidden shadow-card h-fit">
            <div class="px-4 py-3 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800 text-sm">Pemesanan Terbaru pada Gedung Ini</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-brand-teal text-white">
                        <tr>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Penyewa</th>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Tanggal Sewa</th>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Total</th>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($gedung->pemesanans as $pemesanan)
                            @php
                                $statusBadge = match ($pemesanan->status_pemesanan) {
                                    'Disetujui'  => 'badge-success',
                                    'Dibatalkan' => 'badge-danger',
                                    default      => 'badge-neutral',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3.5 font-medium text-gray-900">{{ $pemesanan->user->nama }}</td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-gray-600">
                                    {{ $pemesanan->tanggal_mulai->format('d M Y') }} – {{ $pemesanan->tanggal_selesai->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap font-semibold text-brand-teal">
                                    Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="{{ $statusBadge }}">{{ $pemesanan->status_pemesanan }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-14 text-center text-sm text-gray-400">
                                    Gedung ini belum pernah dipesan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
