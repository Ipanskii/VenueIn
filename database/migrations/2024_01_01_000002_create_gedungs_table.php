<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel inventaris gedung. `id_admin` merujuk ke pengguna dengan
     * role Admin yang mengelola gedung tersebut.
     */
    public function up(): void
    {
        Schema::create('gedungs', function (Blueprint $table) {
            $table->id('id_gedung');

            $table->foreignId('id_admin')
                ->constrained('users', 'id_pengguna')
                ->cascadeOnDelete();

            $table->string('nama_gedung');
            $table->text('alamat');
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('kapasitas');
            $table->decimal('harga_per_hari', 12, 2);
            $table->text('fasilitas')->nullable();
            $table->enum('status', ['Tersedia', 'Perbaikan'])->default('Tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gedungs');
    }
};
