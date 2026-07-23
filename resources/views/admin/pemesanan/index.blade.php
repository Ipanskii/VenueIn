@extends('layouts.admin')

@section('title', 'Kelola Pemesanan')

@section('content')

    {{-- ═══════════════════════════════════════════════
         PAGE HEADER
    ════════════════════════════════════════════════ --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">Kelola Pemesanan</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Tinjau, setujui, atau batalkan pemesanan yang masuk.
            </p>
        </div>

        {{-- Summary badges --}}
        <div class="flex items-center gap-2 text-xs font-semibold">
            <span class="badge-warning px-3 py-1.5">
                {{ $pemesanans->where('status_pemesanan', 'Pending')->count() }} Pending
            </span>
            <span class="badge-success px-3 py-1.5">
                {{ $pemesanans->where('status_pemesanan', 'Disetujui')->count() }} Disetujui
            </span>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         FILTER BAR
    ════════════════════════════════════════════════ --}}
    <form method="GET" action="{{ route('admin.pemesanan.index') }}"
          class="flex flex-wrap items-end gap-3 mb-5 p-4 bg-gray-50 rounded-xl border border-gray-200">

        <div class="flex-1 min-w-[180px]">
            <label class="input-label">Status Pemesanan</label>
            <select name="status" class="input-field bg-white">
                <option value="">— Semua Status —</option>
                <option value="Pending"    {{ request('status') === 'Pending'    ? 'selected' : '' }}>Pending</option>
                <option value="Disetujui"  {{ request('status') === 'Disetujui'  ? 'selected' : '' }}>Disetujui</option>
                <option value="Dibatalkan" {{ request('status') === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>

        <div class="flex-1 min-w-[180px]">
            <label class="input-label">Gedung</label>
            <input type="number"
                   name="id_gedung"
                   value="{{ request('id_gedung') }}"
                   placeholder="ID Gedung..."
                   class="input-field">
        </div>

        <div class="flex-1 min-w-[160px]">
            <label class="input-label">Tanggal Mulai ≥</label>
            <input type="date"
                   name="tanggal_mulai"
                   value="{{ request('tanggal_mulai') }}"
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
            @if (request()->anyFilled(['status', 'id_gedung', 'tanggal_mulai']))
                <a href="{{ route('admin.pemesanan.index') }}" class="btn-ghost text-gray-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- ═══════════════════════════════════════════════
         TABEL PEMESANAN
         Mengikuti spesifikasi PRD Bagian 5.4.
         Badge warna: hijau = Disetujui, oranye = Dibatalkan, abu = Pending
    ════════════════════════════════════════════════ --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-card">

        {{-- Table header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <p class="text-sm text-gray-500">
                Total <span class="font-semibold text-gray-800">{{ $pemesanans->total() }}</span> pemesanan
                @if (request()->anyFilled(['status', 'id_gedung', 'tanggal_mulai']))
                    (difilter)
                @endif
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">

                {{-- Header tabel — bg brand-teal (30% proporsi) --}}
                <thead class="bg-brand-teal text-white">
                    <tr>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">#</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Penyewa</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Gedung</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Tanggal Sewa</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Durasi</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Total Harga</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Pembayaran</th>
                        <th class="px-4 py-3 font-semibold text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
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

                            $durasi = $pemesanan->tanggal_mulai->diffInDays($pemesanan->tanggal_selesai) + 1;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">

                            <td class="px-4 py-3.5 text-gray-400 font-mono text-xs">
                                #{{ $pemesanan->id_pemesanan }}
                            </td>

                            <td class="px-4 py-3.5">
                                <div class="font-medium text-gray-900">{{ $pemesanan->user->nama }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $pemesanan->user->no_telepon ?? '—' }}</div>
                            </td>

                            <td class="px-4 py-3.5">
                                <div class="font-medium text-gray-900 max-w-[160px] truncate">
                                    {{ $pemesanan->gedung->nama_gedung }}
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5 max-w-[160px] truncate">
                                    {{ $pemesanan->gedung->alamat }}
                                </div>
                            </td>

                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="text-gray-900">{{ $pemesanan->tanggal_mulai->format('d M Y') }}</div>
                                <div class="text-xs text-gray-400 flex items-center gap-1 mt-0.5">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                    {{ $pemesanan->tanggal_selesai->format('d M Y') }}
                                </div>
                            </td>

                            <td class="px-4 py-3.5 whitespace-nowrap text-gray-600">
                                {{ $durasi }} hari
                            </td>

                            <td class="px-4 py-3.5 whitespace-nowrap font-semibold text-brand-teal">
                                Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                            </td>

                            <td class="px-4 py-3.5">
                                <span class="{{ $statusBadge }}">
                                    {{ $pemesanan->status_pemesanan }}
                                </span>
                            </td>

                            <td class="px-4 py-3.5">
                                @if ($pemesanan->pembayaran)
                                    <span class="{{ $bayarBadge }}">
                                        {{ $pemesanan->pembayaran->status_pembayaran }}
                                    </span>
                                @else
                                    <span class="badge-neutral">Belum Ada</span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.pemesanan.show', $pemesanan) }}"
                                       class="text-brand-teal font-semibold text-xs hover:underline underline-offset-2 whitespace-nowrap">
                                        Detail →
                                    </a>

                                    @if ($pemesanan->status_pemesanan === 'Pending')
                                        <form action="{{ route('admin.pemesanan.approve', $pemesanan) }}" method="POST"
                                              onsubmit="return confirm('Setujui pemesanan #{{ $pemesanan->id_pemesanan }}?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="text-xs font-semibold text-white bg-brand-teal hover:bg-brand-teal-700
                                                           px-2.5 py-1 rounded-md transition-colors whitespace-nowrap">
                                                Setujui
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.pemesanan.cancel', $pemesanan) }}" method="POST"
                                              onsubmit="return confirm('Batalkan pemesanan ini? Tindakan tidak dapat diurungkan.')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="text-xs font-semibold text-brand-orange hover:text-white hover:bg-brand-orange
                                                           border border-brand-orange px-2.5 py-1 rounded-md transition-colors whitespace-nowrap">
                                                Batalkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Tidak ada pemesanan ditemukan</p>
                                    @if (request()->anyFilled(['status', 'id_gedung', 'tanggal_mulai']))
                                        <a href="{{ route('admin.pemesanan.index') }}" class="btn-ghost text-xs text-brand-teal">
                                            Hapus semua filter
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($pemesanans->hasPages())
            <div class="border-t border-gray-100 px-4 py-3">
                {{ $pemesanans->links() }}
            </div>
        @endif
    </div>

@endsection
