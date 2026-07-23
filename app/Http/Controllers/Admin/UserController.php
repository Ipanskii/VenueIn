<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::withCount(['pemesanans'])
            ->when(request()->filled('role'), fn ($q) => $q->where('role', request('role')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'confirmed', Password::min(8)],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'role'       => ['required', 'in:Admin,Penyewa'],
        ]);

        $user = User::create([
            'nama'       => $validated['nama'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'no_telepon' => $validated['no_telepon'] ?? null,
            'role'       => $validated['role'],
        ]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Akun "' . $user->nama . '" berhasil dibuat.');
    }

    public function show(User $user): View
    {
        $user->load(['pemesanans.gedung']);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'nama'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->getKey() . ',id_pengguna'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'role'       => ['required', 'in:Admin,Penyewa'],
            'password'   => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $data = [
            'nama'       => $validated['nama'],
            'email'      => $validated['email'],
            'no_telepon' => $validated['no_telepon'] ?? null,
            'role'       => $validated['role'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Data pengguna "' . $user->nama . '" berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->getKey() === auth()->id()) {
            return back()->withErrors([
                'user' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ]);
        }

        $adaPemesananAktif = $user->pemesanans()
            ->whereIn('status_pemesanan', ['Pending', 'Disetujui'])
            ->exists();

        if ($adaPemesananAktif) {
            return back()->withErrors([
                'user' => 'Pengguna tidak dapat dihapus karena masih memiliki pemesanan aktif.',
            ]);
        }

        $namaUser = $user->nama;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun "' . $namaUser . '" berhasil dihapus.');
    }
}
