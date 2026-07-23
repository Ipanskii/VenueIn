<?php

namespace App\Observers;

use App\Models\Pembayaran;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\Log;

class PembayaranObserver
{
    /**
     * Bereaksi setelah baris `pembayarans` berhasil diperbarui ke database.
     *
     * CATATAN IMPLEMENTASI — wasChanged() vs isDirty():
     *
     *   isDirty()     → mengecek atribut yang BELUM disimpan (sebelum save()).
     *                   Dipanggil dalam event `saving` / `updating`.
     *
     *   wasChanged()  → mengecek atribut yang BARU SAJA disimpan (sesudah save()).
     *                   Dipanggil dalam event `saved` / `updated`.
     *
     * Observer ini menggunakan event `updated` (pasca-simpan), sehingga
     * wasChanged() adalah metode yang benar. Draf PRD Bagian 4.3 menulis
     * isDirty() di konteks yang sama — ini dikoreksi di sini.
     */
    public function updated(Pembayaran $pembayaran): void
    {
        if (! $pembayaran->wasChanged('status_pembayaran')) {
            return;
        }

        /** @var Pemesanan $pemesanan */
        $pemesanan = $pembayaran->pemesanan;

        if (! $pemesanan) {
            Log::warning('PembayaranObserver: tidak dapat menemukan Pemesanan untuk id_pembayaran=' . $pembayaran->id_pembayaran);
            return;
        }

        match ($pembayaran->status_pembayaran) {

            /**
             * Admin telah mengonfirmasi pembayaran sebagai Lunas.
             * Pemesanan otomatis beralih ke Disetujui — tidak perlu
             * tindakan manual admin kedua.
             */
            'Lunas' => $pemesanan->update(['status_pemesanan' => 'Disetujui']),

            /**
             * Penyewa baru mengunggah bukti bayar.
             * status_pemesanan tetap 'Pending' sampai admin memverifikasi.
             */
            'Menunggu Verifikasi' => null,

            /**
             * Admin menolak bukti bayar — status dikembalikan ke Belum Bayar.
             * status_pemesanan kembali ke Pending agar penyewa bisa
             * mengunggah ulang bukti yang benar.
             */
            'Belum Bayar' => $pemesanan->update(['status_pemesanan' => 'Pending']),

            /**
             * Tangkap nilai enum yang tidak dikenal (guard terhadap
             * perubahan enum di masa depan tanpa memperbarui observer).
             */
            default => Log::error(
                'PembayaranObserver: status_pembayaran tidak dikenal.',
                [
                    'id_pembayaran'     => $pembayaran->id_pembayaran,
                    'status_pembayaran' => $pembayaran->status_pembayaran,
                ]
            ),
        };
    }
}
