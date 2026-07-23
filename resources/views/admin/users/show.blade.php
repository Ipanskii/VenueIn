@extends('layouts.admin')

@section('title', $user->nama)

@section('content')

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}"
               class="w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-gray-900">{{ $user->nama }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $user->email }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn-secondary">Ubah</a>
            @if ($user->id_pengguna !== auth()->id())
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                      onsubmit="return confirm('Hapus akun &quot;{{ $user->nama }}&quot;?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="text-sm font-semibold text-brand-orange hover:text-white hover:bg-brand-orange border border-brand-orange px-5 py-2.5 rounded-lg transition-colors">
                        Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-brand-orange-50 text-brand-orange text-sm font-medium">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Detail akun --}}
        <div class="lg:col-span-1 card p-6 h-fit">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-800 text-sm">Detail Akun</h2>
                @if ($user->role === 'Admin')
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-brand-teal-50 text-brand-teal">Admin</span>
                @else
                    <span class="badge-neutral">Penyewa</span>
                @endif
            </div>

            <dl class="space-y-4 text-sm">
                <div>
                    <dt class="text-xs text-gray-400 mb-1">No. Telepon</dt>
                    <dd class="text-gray-800">{{ $user->no_telepon ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Bergabung Sejak</dt>
                    <dd class="text-gray-800">{{ $user->created_at->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Total Pemesanan</dt>
                    <dd class="text-gray-800">{{ $user->pemesanans->count() }}</dd>
                </div>
            </dl>
        </div>

        {{-- Riwayat pemesanan (jika Penyewa) --}}
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl overflow-hidden shadow-card h-fit">
            <div class="px-4 py-3 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800 text-sm">Riwayat Pemesanan</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-brand-teal text-white">
                        <tr>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Gedung</th>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Tanggal Sewa</th>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Total</th>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($user->pemesanans as $pemesanan)
                            @php
                                $statusBadge = match ($pemesanan->status_pemesanan) {
                                    'Disetujui'  => 'badge-success',
                                    'Dibatalkan' => 'badge-danger',
                                    default      => 'badge-neutral',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3.5 font-medium text-gray-900 max-w-[180px] truncate">{{ $pemesanan->gedung->nama_gedung }}</td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-gray-600">
                                    {{ $pemesanan->tanggal_mulai->format('d M Y') }}
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
                                    Pengguna ini belum pernah membuat pemesanan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
