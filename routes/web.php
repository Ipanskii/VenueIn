<?php

use App\Http\Controllers\Admin\GedungController as AdminGedungController;
use App\Http\Controllers\Admin\PembayaranController as AdminPembayaranController;
use App\Http\Controllers\Admin\PemesananController as AdminPemesananController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Penyewa\GedungController as PenyewaGedungController;
use App\Http\Controllers\Penyewa\PembayaranController as PenyewaPembayaranController;
use App\Http\Controllers\Penyewa\PemesananController as PenyewaPemesananController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| GRUP 1 — PUBLIK (tidak memerlukan autentikasi)
|--------------------------------------------------------------------------
*/

Route::get('/', [PenyewaGedungController::class, 'index'])
    ->name('beranda');

Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('guest');

Route::get('/register', [AuthController::class, 'showRegisterForm'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| GRUP 2 — PROFIL (semua role yang terautentikasi)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function (): void {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| GRUP 3 — PENYEWA
| Middleware: auth + role:Penyewa
|--------------------------------------------------------------------------
|
| PERHATIAN — Casing role wajib kapital ('Penyewa'), bukan 'penyewa'.
| CheckRole::handle() membandingkan secara case-sensitive terhadap nilai
| enum kolom `role` di tabel `users`: ['Admin', 'Penyewa'].
|
*/

Route::middleware(['auth', 'role:Penyewa'])->group(function (): void {

    // ── Katalog gedung (detail) ───────────────────────────────────────────
    // Index katalog sengaja diletakkan di Grup 1 (publik) agar mesin
    // pencari dapat mengindeksnya. Detail gedung — yang berisi kalender
    // ketersediaan dinamis — memerlukan autentikasi.
    Route::get('/gedung/{gedung}', [PenyewaGedungController::class, 'show'])
        ->name('gedung.show');

    // ── Pemesanan ─────────────────────────────────────────────────────────
    Route::resource('pemesanan', PenyewaPemesananController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::patch('/pemesanan/{pemesanan}/batal', [PenyewaPemesananController::class, 'cancel'])
        ->name('pemesanan.batal');

    // ── Pembayaran (upload bukti) ─────────────────────────────────────────
    Route::get('/pemesanan/{pemesanan}/pembayaran', [PenyewaPembayaranController::class, 'create'])
        ->name('pembayaran.create');

    Route::post('/pemesanan/{pemesanan}/pembayaran', [PenyewaPembayaranController::class, 'store'])
        ->name('pembayaran.store');
});

/*
|--------------------------------------------------------------------------
| GRUP 4 — ADMIN
| Middleware: auth + role:Admin
| Prefix URL  : /admin
| Prefix nama : admin.
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {

        // ── Dashboard ─────────────────────────────────────────────────────
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // ── Kelola Gedung (resource penuh: index/create/store/show/edit/update/destroy) ───
        // Pemanggilan ->parameters() memastikan parameter URL konsisten
        // menggunakan 'gedung' (sesuai dengan getRouteKeyName() model)
        // alih-alih 'gedung' yang kadang digenerate berbeda oleh resource helper.
        Route::resource('gedung', AdminGedungController::class)
            ->parameters(['gedung' => 'gedung']);

        // ── Kelola Pemesanan ──────────────────────────────────────────────
        Route::get('/pemesanan', [AdminPemesananController::class, 'index'])
            ->name('pemesanan.index');

        Route::get('/pemesanan/{pemesanan}', [AdminPemesananController::class, 'show'])
            ->name('pemesanan.show');

        Route::patch('/pemesanan/{pemesanan}/approve', [AdminPemesananController::class, 'approve'])
            ->name('pemesanan.approve');

        Route::patch('/pemesanan/{pemesanan}/cancel', [AdminPemesananController::class, 'cancel'])
            ->name('pemesanan.cancel');

        // ── Verifikasi Pembayaran ─────────────────────────────────────────
        Route::get('/pembayaran', [AdminPembayaranController::class, 'index'])
            ->name('pembayaran.index');

        // Rute show untuk admin: menampilkan detail pembayaran + bukti transfer
        // sebelum admin memutuskan verify/reject. Tidak tercantum eksplisit di
        // PRD namun wajib ada agar alur verifikasi berjalan.
        Route::get('/pembayaran/{pembayaran}', [AdminPembayaranController::class, 'show'])
            ->name('pembayaran.show');

        Route::patch('/pembayaran/{pembayaran}/verify', [AdminPembayaranController::class, 'verify'])
            ->name('pembayaran.verify');

        Route::patch('/pembayaran/{pembayaran}/reject', [AdminPembayaranController::class, 'reject'])
            ->name('pembayaran.reject');

        // ── Kelola Pengguna (opsional, untuk superadmin) ──────────────────
        Route::resource('users', AdminUserController::class);
    });
