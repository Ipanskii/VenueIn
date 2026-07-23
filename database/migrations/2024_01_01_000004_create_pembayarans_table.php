<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pembayaran. Relasi ke pemesanans bersifat satu-ke-satu
     * dalam praktiknya (satu pemesanan = satu transaksi pembayaran),
     * namun didefinisikan sebagai FK biasa agar fleksibel jika ke depan
     * dibutuhkan riwayat pembayaran cicilan/multi-tahap.
     */
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id('id_pembayaran');

            $table->foreignId('id_pemesanan')
                ->constrained('pemesanans', 'id_pemesanan')
                ->cascadeOnDelete();

            $table->decimal('jumlah', 12, 2);
            $table->string('metode_pembayaran');
            $table->string('bukti_transfer')->nullable();
            $table->enum('status_pembayaran', ['Belum Bayar', 'Menunggu Verifikasi', 'Lunas'])
                ->default('Belum Bayar');
            $table->timestamp('tanggal_bayar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
