<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tambahan (bukan edit ulang migration lama) supaya bisa
 * dijalankan dengan `php artisan migrate` biasa, tanpa perlu
 * `migrate:fresh` yang akan menghapus data yang sudah ada.
 *
 * Kolom ini seharusnya sudah ada sejak awal di migration pembuatan
 * tabel `pembayarans`, namun sempat terlewat saat generate awal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            if (! Schema::hasColumn('pembayarans', 'bukti_transfer')) {
                $table->string('bukti_transfer')->nullable()->after('metode_pembayaran');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            if (Schema::hasColumn('pembayarans', 'bukti_transfer')) {
                $table->dropColumn('bukti_transfer');
            }
        });
    }
};