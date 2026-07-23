@extends('layouts.app')

@section('title', 'Buat Pemesanan')

@section('content')

    <section class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="mb-6">
            <h1 class="text-xl font-extrabold text-gray-900">Buat Pemesanan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Pilih gedung dan tentukan tanggal sewa Anda.</p>
        </div>

        <div class="card p-6 sm:p-8">
            <form method="POST" action="{{ route('pemesanan.store') }}" class="space-y-5">
                @csrf

                @if ($gedungDipilih)
                    <input type="hidden" name="id_gedung" value="{{ $gedungDipilih->id_gedung }}">

                    <div>
                        <label class="input-label">Gedung Dipilih</label>
                        <div class="flex items-center justify-between gap-3 p-4 rounded-lg border border-brand-teal-100 bg-brand-teal-50">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 truncate">{{ $gedungDipilih->nama_gedung }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $gedungDipilih->alamat }}</p>
                                <p class="text-sm font-semibold text-brand-teal mt-1">
                                    Rp {{ number_format($gedungDipilih->harga_per_hari, 0, ',', '.') }} / hari
                                </p>
                            </div>
                            <a href="{{ route('beranda') }}" class="text-xs font-semibold text-brand-teal hover:underline whitespace-nowrap">
                                Ganti
                            </a>
                        </div>
                    </div>
                @else
                    <div>
                        <label for="id_gedung" class="input-label">Pilih Gedung</label>
                        <select id="id_gedung" name="id_gedung" class="input-field bg-white">
                            <option value="">— Pilih Gedung —</option>
                            @foreach ($gedungs as $gedung)
                                <option value="{{ $gedung->id_gedung }}" data-harga="{{ (int) $gedung->harga_per_hari }}"
                                        {{ old('id_gedung') == $gedung->id_gedung ? 'selected' : '' }}>
                                    {{ $gedung->nama_gedung }} — Rp {{ number_format($gedung->harga_per_hari, 0, ',', '.') }}/hari
                                </option>
                            @endforeach
                        </select>
                        @error('id_gedung') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="tanggal_mulai" class="input-label">Tanggal Mulai</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                               min="{{ now()->toDateString() }}"
                               value="{{ old('tanggal_mulai') }}" class="input-field">
                        @error('tanggal_mulai') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="tanggal_selesai" class="input-label">Tanggal Selesai</label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai"
                               min="{{ now()->toDateString() }}"
                               value="{{ old('tanggal_selesai') }}" class="input-field">
                        @error('tanggal_selesai') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
                    </div>
                </div>

                <p class="text-xs text-gray-400">
                    Total harga akan dihitung otomatis (harga per hari × jumlah hari sewa, inklusif) setelah Anda mengirim formulir ini.
                </p>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary">Buat Pemesanan</button>
                    <a href="{{ route('beranda') }}" class="btn-ghost text-gray-500">Batal</a>
                </div>
            </form>
        </div>
    </section>

@endsection
