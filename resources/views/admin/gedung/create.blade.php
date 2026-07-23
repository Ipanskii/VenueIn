@extends('layouts.admin')

@section('title', 'Tambah Gedung')

@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.gedung.index') }}"
           class="w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">Tambah Gedung</h1>
            <p class="text-sm text-gray-500 mt-0.5">Isi detail gedung baru yang akan disewakan.</p>
        </div>
    </div>

    <div class="card p-6 sm:p-8 max-w-3xl">
        <form method="POST" action="{{ route('admin.gedung.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="foto" class="input-label">Foto Gedung <span class="font-normal text-gray-400">(opsional)</span></label>
                <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png,.webp"
                       class="input-field file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0
                              file:text-xs file:font-semibold file:bg-brand-teal-50 file:text-brand-teal
                              hover:file:bg-brand-teal-100">
                <p class="text-xs text-gray-400 mt-1.5">Format JPG, PNG, atau WEBP. Maksimal 2MB.</p>
                @error('foto') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="nama_gedung" class="input-label">Nama Gedung</label>
                <input type="text" id="nama_gedung" name="nama_gedung" value="{{ old('nama_gedung') }}"
                       placeholder="Contoh: Grand Ballroom Sudirman" class="input-field">
                @error('nama_gedung') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="alamat" class="input-label">Alamat</label>
                <textarea id="alamat" name="alamat" rows="2"
                          placeholder="Alamat lengkap gedung" class="input-field">{{ old('alamat') }}</textarea>
                @error('alamat') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="deskripsi" class="input-label">Deskripsi <span class="font-normal text-gray-400">(opsional)</span></label>
                <textarea id="deskripsi" name="deskripsi" rows="4"
                          placeholder="Ceritakan keunggulan gedung ini..." class="input-field">{{ old('deskripsi') }}</textarea>
                @error('deskripsi') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="kapasitas" class="input-label">Kapasitas (orang)</label>
                    <input type="number" id="kapasitas" name="kapasitas" min="1" value="{{ old('kapasitas') }}"
                           placeholder="Contoh: 300" class="input-field">
                    @error('kapasitas') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="harga_per_hari" class="input-label">Harga per Hari (Rp)</label>
                    <input type="number" id="harga_per_hari" name="harga_per_hari" min="0" value="{{ old('harga_per_hari') }}"
                           placeholder="Contoh: 5000000" class="input-field">
                    @error('harga_per_hari') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="fasilitas" class="input-label">Fasilitas <span class="font-normal text-gray-400">(opsional, pisahkan dengan koma)</span></label>
                <textarea id="fasilitas" name="fasilitas" rows="2"
                          placeholder="Contoh: AC, Sound System, Proyektor, Parkir Luas" class="input-field">{{ old('fasilitas') }}</textarea>
                @error('fasilitas') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="status" class="input-label">Status</label>
                <select id="status" name="status" class="input-field bg-white">
                    <option value="Tersedia" {{ old('status') === 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="Perbaikan" {{ old('status') === 'Perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                </select>
                @error('status') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">Simpan Gedung</button>
                <a href="{{ route('admin.gedung.index') }}" class="btn-ghost text-gray-500">Batal</a>
            </div>
        </form>
    </div>

@endsection
