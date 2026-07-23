@extends('layouts.admin')

@section('title', 'Ubah Pengguna')

@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.users.show', $user) }}"
           class="w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">Ubah Pengguna</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $user->nama }}</p>
        </div>
    </div>

    <div class="card p-6 sm:p-8 max-w-2xl">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label for="nama" class="input-label">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" class="input-field">
                @error('nama') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="input-label">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="input-field">
                @error('email') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="no_telepon" class="input-label">No. Telepon <span class="font-normal text-gray-400">(opsional)</span></label>
                <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}" class="input-field">
                @error('no_telepon') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="role" class="input-label">Role</label>
                <select id="role" name="role" class="input-field bg-white"
                        {{ $user->id_pengguna === auth()->id() ? 'disabled' : '' }}>
                    <option value="Penyewa" {{ old('role', $user->role) === 'Penyewa' ? 'selected' : '' }}>Penyewa</option>
                    <option value="Admin" {{ old('role', $user->role) === 'Admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @if ($user->id_pengguna === auth()->id())
                    <input type="hidden" name="role" value="{{ $user->role }}">
                    <p class="text-xs text-gray-400 mt-1.5">Anda tidak dapat mengubah role akun Anda sendiri.</p>
                @endif
                @error('role') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="input-label">Kata Sandi Baru <span class="font-normal text-gray-400">(opsional)</span></label>
                    <input type="password" id="password" name="password"
                           placeholder="Kosongkan jika tidak diubah" class="input-field">
                    @error('password') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="input-label">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           placeholder="Ulangi kata sandi baru" class="input-field">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
                <a href="{{ route('admin.users.show', $user) }}" class="btn-ghost text-gray-500">Batal</a>
            </div>
        </form>
    </div>

@endsection
