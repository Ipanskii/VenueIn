@extends('layouts.admin')

@section('title', 'Ubah Gedung')

@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.gedung.show', $gedung) }}"
           class="w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">Ubah Gedung</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $gedung->nama_gedung }}</p>
        </div>
    </div>

    <div class="card p-6 sm:p-8 max-w-3xl">
        <form method="POST" action="{{ route('admin.gedung.update', $gedung) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label for="foto" class="input-label">Foto Gedung <span class="font-normal text-gray-400">(opsional)</span></label>

                @if ($gedung->foto_url)
                    <img src="{{ $gedung->foto_url }}" alt="{{ $gedung->nama_gedung }}"
                         class="w-full h-40 object-cover rounded-lg border border-gray-200 mb-3">
                @endif

                <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png,.webp"
                       class="input-field file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0
                              file:text-xs file:font-semibold file:bg-brand-teal-50 file:text-brand-teal
                              hover:file:bg-brand-teal-100">
                <p class="text-xs text-gray-400 mt-1.5">
                    {{ $gedung->foto_url ? 'Unggah file baru untuk mengganti foto saat ini.' : 'Format JPG, PNG, atau WEBP. Maksimal 2MB.' }}
                </p>
                @error('foto') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="nama_gedung" class="input-label">Nama Gedung</label>
                <input type="text" id="nama_gedung" name="nama_gedung" value="{{ old('nama_gedung', $gedung->nama_gedung) }}"
                       class="input-field">
                @error('nama_gedung') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="alamat" class="input-label">Alamat</label>
                <textarea id="alamat" name="alamat" rows="2" class="input-field">{{ old('alamat', $gedung->alamat) }}</textarea>
                @error('alamat') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="deskripsi" class="input-label">Deskripsi <span class="font-normal text-gray-400">(opsional)</span></label>
                <textarea id="deskripsi" name="deskripsi" rows="4" class="input-field">{{ old('deskripsi', $gedung->deskripsi) }}</textarea>
                @error('deskripsi') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="kapasitas" class="input-label">Kapasitas (orang)</label>
                    <input type="number" id="kapasitas" name="kapasitas" min="1" value="{{ old('kapasitas', $gedung->kapasitas) }}"
                           class="input-field">
                    @error('kapasitas') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="harga_per_hari" class="input-label">Harga per Hari (Rp)</label>
                    <input type="number" id="harga_per_hari" name="harga_per_hari" min="0"
                           value="{{ old('harga_per_hari', (int) $gedung->harga_per_hari) }}" class="input-field">
                    @error('harga_per_hari') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="fasilitas" class="input-label">Fasilitas <span class="font-normal text-gray-400">(opsional, pisahkan dengan koma)</span></label>
                <textarea id="fasilitas" name="fasilitas" rows="2" class="input-field">{{ old('fasilitas', $gedung->fasilitas) }}</textarea>
                @error('fasilitas') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="status" class="input-label">Status</label>
                <select id="status" name="status" class="input-field bg-white">
                    <option value="Tersedia" {{ old('status', $gedung->status) === 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="Perbaikan" {{ old('status', $gedung->status) === 'Perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                </select>
                @error('status') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
                <a href="{{ route('admin.gedung.show', $gedung) }}" class="btn-ghost text-gray-500">Batal</a>
            </div>
        </form>
    </div>

@endsection
