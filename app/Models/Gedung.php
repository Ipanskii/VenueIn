<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Gedung extends Model
{
    protected $primaryKey = 'id_gedung';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id_admin',
        'nama_gedung',
        'alamat',
        'deskripsi',
        'kapasitas',
        'harga_per_hari',
        'fasilitas',
        'status',
        'foto',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kapasitas' => 'integer',
            'harga_per_hari' => 'decimal:2',
        ];
    }

    /**
     * URL publik foto gedung (null kalau belum ada foto yang diunggah).
     */
    protected function fotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->foto ? Storage::disk('public')->url($this->foto) : null,
        );
    }

    /**
     * Admin (pengguna dengan role Admin) yang mengelola gedung ini.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_admin', 'id_pengguna');
    }

    /**
     * Seluruh riwayat pemesanan pada gedung ini.
     */
    public function pemesanans(): HasMany
    {
        return $this->hasMany(Pemesanan::class, 'id_gedung', 'id_gedung');
    }

    /**
     * Wajib di-override karena PK bukan `id` — digunakan saat
     * route menggunakan implicit route-model binding, contoh:
     * Route::get('/gedung/{gedung}', ...).
     */
    public function getRouteKeyName(): string
    {
        return 'id_gedung';
    }
}
