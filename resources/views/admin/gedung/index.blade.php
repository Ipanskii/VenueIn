@extends('layouts.admin')

@section('title', 'Kelola Gedung')

@section('content')

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">Kelola Gedung</h1>
            <p class="text-sm text-gray-500 mt-0.5">Tambah, ubah, dan kelola daftar gedung yang disewakan.</p>
        </div>

        <a href="{{ route('admin.gedung.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Gedung
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-card">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <p class="text-sm text-gray-500">
                Total <span class="font-semibold text-gray-800">{{ $gedungs->total() }}</span> gedung
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-brand-teal text-white">
                    <tr>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">#</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap"></th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Nama Gedung</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Kapasitas</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Harga / Hari</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Dikelola Oleh</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 font-semibold text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($gedungs as $gedung)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3.5 text-gray-400 font-mono text-xs">#{{ $gedung->id_gedung }}</td>
                            <td class="px-4 py-3.5">
                                @if ($gedung->foto_url)
                                    <img src="{{ $gedung->foto_url }}" alt="{{ $gedung->nama_gedung }}"
                                         class="w-12 h-12 rounded-lg object-cover border border-gray-200">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-gray-50 border border-dashed border-gray-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-medium text-gray-900 max-w-[220px] truncate">{{ $gedung->nama_gedung }}</div>
                                <div class="text-xs text-gray-400 mt-0.5 max-w-[220px] truncate">{{ $gedung->alamat }}</div>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-gray-600">
                                {{ number_format($gedung->kapasitas) }} orang
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap font-semibold text-brand-teal">
                                Rp {{ number_format($gedung->harga_per_hari, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-gray-600">
                                {{ $gedung->admin->nama ?? '—' }}
                            </td>
                            <td class="px-4 py-3.5">
                                @if ($gedung->status === 'Tersedia')
                                    <span class="badge-success">Tersedia</span>
                                @else
                                    <span class="badge-warning">Perbaikan</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.gedung.show', $gedung) }}"
                                       class="text-brand-teal font-semibold text-xs hover:underline underline-offset-2 whitespace-nowrap">
                                        Detail
                                    </a>
                                    <a href="{{ route('admin.gedung.edit', $gedung) }}"
                                       class="text-gray-500 font-semibold text-xs hover:underline underline-offset-2 whitespace-nowrap">
                                        Ubah
                                    </a>
                                    <form action="{{ route('admin.gedung.destroy', $gedung) }}" method="POST"
                                          onsubmit="return confirm('Hapus gedung &quot;{{ $gedung->nama_gedung }}&quot;? Tindakan ini tidak dapat diurungkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-brand-orange font-semibold text-xs hover:underline underline-offset-2 whitespace-nowrap">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">Belum ada gedung yang ditambahkan</p>
                                    <a href="{{ route('admin.gedung.create') }}" class="btn-secondary text-xs mt-1">
                                        Tambah Gedung Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($gedungs->hasPages())
            <div class="border-t border-gray-100 px-4 py-3">
                {{ $gedungs->links() }}
            </div>
        @endif
    </div>

@endsection
