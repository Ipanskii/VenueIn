<?php

namespace App\Providers;

use App\Models\Pembayaran;
use App\Observers\PembayaranObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Daftarkan binding ke service container — tidak digunakan saat ini.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap seluruh service aplikasi.
     *
     * PembayaranObserver didaftarkan di sini agar aktif sejak aplikasi
     * pertama kali di-boot. Setiap perubahan pada model Pembayaran
     * akan secara otomatis diteruskan ke observer tanpa perlu memanggil
     * event secara manual di dalam controller.
     *
     * Jika di masa depan ditambahkan model observer baru, daftarkan
     * di dalam method ini (bukan di controller) untuk menjaga
     * konsistensi arsitektur.
     */
    public function boot(): void
    {
        Pembayaran::observe(PembayaranObserver::class);
        Paginator::useTailwind();
    }
}
