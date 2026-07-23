@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', 'Edit Profil')

@section('content')

    @php
        $wrapperClass = auth()->user()->isAdmin()
            ? ''
            : 'max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8';
    @endphp

    <section class="{{ $wrapperClass }}">

        <div class="mb-6">
            <h1 class="text-xl font-extrabold text-gray-900">Edit Profil</h1>
            <p class="text-sm text-gray-500 mt-0.5">Perbarui informasi akun dan kata sandi Anda.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-emerald-50 text-brand-teal text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="card p-6 sm:p-8 max-w-2xl">
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label for="nama" class="input-label">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" class="input-field">
                    @error('nama') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="input-label">Alamat Email</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           class="input-field bg-gray-50 text-gray-400 cursor-not-allowed">
                    <p class="text-xs text-gray-400 mt-1.5">Email tidak dapat diubah. Hubungi administrator jika diperlukan.</p>
                </div>

                <div>
                    <label for="no_telepon" class="input-label">No. Telepon <span class="font-normal text-gray-400">(opsional)</span></label>
                    <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}"
                           placeholder="08xxxxxxxxxx" class="input-field">
                    @error('no_telepon') <p class="text-xs text-brand-orange mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="border-t border-gray-100 pt-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">Ubah Kata Sandi</p>

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
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </section>

@endsection
