<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password yang Anda masukkan tidak cocok.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return match (Auth::user()->role) {
            'Admin'   => redirect()->intended(route('admin.dashboard')),
            default   => redirect()->intended(route('beranda')),
        };
    }

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', 'confirmed', Password::min(8)],
            'no_telepon'            => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'nama'       => $validated['nama'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'no_telepon' => $validated['no_telepon'] ?? null,
            'role'       => 'Penyewa',
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('beranda')
            ->with('success', 'Akun berhasil dibuat. Selamat datang, ' . $user->nama . '!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
