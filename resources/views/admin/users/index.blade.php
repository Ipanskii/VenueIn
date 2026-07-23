@extends('layouts.admin')

@section('title', 'Kelola Pengguna')

@section('content')

    @php
        $roleFilter = request('role');
        $roles = ['' => 'Semua', 'Admin' => 'Admin', 'Penyewa' => 'Penyewa'];
    @endphp

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">Kelola Pengguna</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola akun Admin dan Penyewa dalam sistem.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Pengguna
        </a>
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

    <div class="flex flex-wrap gap-2 mb-4">
        @foreach ($roles as $value => $label)
            <a href="{{ route('admin.users.index', $value ? ['role' => $value] : []) }}"
               class="text-xs font-semibold px-3.5 py-1.5 rounded-full border transition-colors
                      {{ (string) $roleFilter === (string) $value
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
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Nama</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Email</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Role</th>
                        <th class="px-4 py-3 font-semibold whitespace-nowrap">Total Pemesanan</th>
                        <th class="px-4 py-3 font-semibold text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3.5 text-gray-400 font-mono text-xs">#{{ $user->id_pengguna }}</td>
                            <td class="px-4 py-3.5 font-medium text-gray-900">
                                {{ $user->nama }}
                                @if ($user->id_pengguna === auth()->id())
                                    <span class="text-xs text-gray-400">(Anda)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-gray-600">{{ $user->email }}</td>
                            <td class="px-4 py-3.5">
                                @if ($user->role === 'Admin')
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-brand-teal-50 text-brand-teal">Admin</span>
                                @else
                                    <span class="badge-neutral">Penyewa</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-gray-600">
                                {{ $user->pemesanans_count }}
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="text-brand-teal font-semibold text-xs hover:underline underline-offset-2 whitespace-nowrap">
                                        Detail
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="text-gray-500 font-semibold text-xs hover:underline underline-offset-2 whitespace-nowrap">
                                        Ubah
                                    </a>
                                    @if ($user->id_pengguna !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                              onsubmit="return confirm('Hapus akun &quot;{{ $user->nama }}&quot;?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-brand-orange font-semibold text-xs hover:underline underline-offset-2 whitespace-nowrap">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center text-sm text-gray-400">
                                Tidak ada pengguna untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-gray-100 px-4 py-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>

@endsection
