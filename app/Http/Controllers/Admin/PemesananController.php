<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PemesananController extends Controller
{
    /**
     * Daftar seluruh pemesanan di sistem, dengan filter opsional.
     *
     * Filter yang tersedia via query string:
     *   ?status=Pending|Disetujui|Dibatalkan
     *   ?id_gedung=X
     *   ?tanggal_mulai=YYYY-MM-DD
     *
     * Data di-eager load untuk mencegah N+1 saat dirender di tabel.
     */
    public function index(): View
    {
        $query = Pemesanan::with(['user', 'gedung', 'pembayaran'])
            ->latest();

        if (request()->filled('status')) {
            $query->where('status_pemesanan', request('status'));
        }

        if (request()->filled('id_gedung')) {
            $query->where('id_gedung', request()->integer('id_gedung'));
        }

        if (request()->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_mulai', '>=', request('tanggal_mulai'));
        }

        $pemesanans = $query->paginate(15)->withQueryString();

        return view('admin.pemesanan.index', compact('pemesanans'));
    }

    /**
     * Detail satu pemesanan beserta relasi lengkap untuk keperluan
     * tinjauan admin (info penyewa, gedung, dan status pembayaran terkini).
     */
    public function show(Pemesanan $pemesanan): View
    {
        $pemesanan->load(['user', 'gedung', 'pembayaran']);

        return view('admin.pemesanan.show', compact('pemesanan'));
    }

    /**
     * Setujui pemesanan secara langsung (tanpa menunggu verifikasi pembayaran).
     *
     * KAPAN DIGUNAKAN:
     * Untuk skenario pembayaran tunai/di tempat di mana admin menerima
     * konfirmasi langsung dari penyewa tanpa bukti transfer digital.
     * Jika alur normal (upload bukti → verifikasi → Lunas) yang digunakan,
     * status 'Disetujui' sudah diset otomatis oleh PembayaranObserver.
     *
     * Guard: hanya pemesanan berstatus 'Pending' yang bisa disetujui.
     * Pemesanan yang sudah 'Disetujui' atau 'Dibatalkan' tidak dapat
     * diubah melalui method ini.
     */
    public function approve(Pemesanan $pemesanan): RedirectResponse
    {
        if ($pemesanan->status_pemesanan !== 'Pending') {
            return back()->withErrors([
                'status' => 'Hanya pemesanan berstatus "Pending" yang dapat disetujui. Status saat ini: ' . $pemesanan->status_pemesanan . '.',
            ]);
        }

        $pemesanan->update(['status_pemesanan' => 'Disetujui']);

        return redirect()
            ->route('admin.pemesanan.show', $pemesanan)
            ->with('success', 'Pemesanan #' . $pemesanan->id_pemesanan . ' berhasil disetujui.');
    }

    /**
     * Batalkan pemesanan dari sisi admin.
     *
     * Admin dapat membatalkan dari status apapun kecuali yang sudah
     * 'Dibatalkan'. Ini berbeda dengan cancel() penyewa yang hanya
     * bisa membatalkan saat status masih 'Pending'.
     *
     * Karena query anti-double-booking pada PemesananController::store()
     * mengecualikan status 'Dibatalkan', gedung otomatis kembali tersedia
     * pada rentang tanggal tersebut tanpa perlu mengubah kolom `status`
     * pada tabel `gedungs`.
     */
    public function cancel(Pemesanan $pemesanan): RedirectResponse
    {
        if ($pemesanan->status_pemesanan === 'Dibatalkan') {
            return back()->withErrors([
                'status' => 'Pemesanan ini sudah berstatus "Dibatalkan".',
            ]);
        }

        $pemesanan->update(['status_pemesanan' => 'Dibatalkan']);

        return redirect()
            ->route('admin.pemesanan.show', $pemesanan)
            ->with('success', 'Pemesanan #' . $pemesanan->id_pemesanan . ' berhasil dibatalkan.');
    }
}
