<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pemesanan extends Model
{
    protected $primaryKey = 'id_pemesanan';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id_pengguna',
        'id_gedung',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_pemesanan',
        'total_harga',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'total_harga' => 'decimal:2',
        ];
    }

    /**
     * Penyewa yang membuat pemesanan ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Gedung yang dipesan.
     */
    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class, 'id_gedung', 'id_gedung');
    }

    /**
     * Satu pemesanan memiliki satu transaksi pembayaran terkait.
     */
    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'id_pemesanan', 'id_pemesanan');
    }

    public function getRouteKeyName(): string
    {
        return 'id_pemesanan';
    }
}
