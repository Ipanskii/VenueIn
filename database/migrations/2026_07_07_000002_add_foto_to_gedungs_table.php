<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tambahan untuk menyimpan path foto gedung.
 * Dibuat sebagai file migration baru (bukan edit migration lama)
 * supaya bisa dijalankan dengan `php artisan migrate` biasa tanpa
 * perlu `migrate:fresh` yang akan menghapus data yang sudah ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gedungs', function (Blueprint $table) {
            if (! Schema::hasColumn('gedungs', 'foto')) {
                $table->string('foto')->nullable()->after('fasilitas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('gedungs', function (Blueprint $table) {
            if (Schema::hasColumn('gedungs', 'foto')) {
                $table->dropColumn('foto');
            }
        });
    }
};
