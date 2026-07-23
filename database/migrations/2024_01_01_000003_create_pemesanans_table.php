<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel transaksi pemesanan. Indeks komposit pada (id_gedung,
     * tanggal_mulai, tanggal_selesai) wajib ada — kueri anti-double-booking
     * pada Tahap 2 akan selalu memfilter berdasarkan kombinasi tiga kolom ini,
     * sehingga tanpa indeks performanya akan menurun signifikan saat data tumbuh.
     */
    public function up(): void
    {
        Schema::create('pemesanans', function (Blueprint $table) {
            $table->id('id_pemesanan');

            $table->foreignId('id_pengguna')
                ->constrained('users', 'id_pengguna')
                ->cascadeOnDelete();

            $table->foreignId('id_gedung')
                ->constrained('gedungs', 'id_gedung')
                ->cascadeOnDelete();

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status_pemesanan', ['Pending', 'Disetujui', 'Dibatalkan'])
                ->default('Pending');
            $table->decimal('total_harga', 12, 2);
            $table->timestamps();

            $table->index(
                ['id_gedung', 'tanggal_mulai', 'tanggal_selesai'],
                'idx_gedung_rentang_tanggal'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemesanans');
    }
};
