<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $primaryKey = 'id_pembayaran';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id_pemesanan',
        'jumlah',
        'metode_pembayaran',
        'bukti_transfer',
        'status_pembayaran',
        'tanggal_bayar',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'tanggal_bayar' => 'datetime',
        ];
    }

    /**
     * Pemesanan yang terkait dengan pembayaran ini.
     */
    public function pemesanan(): BelongsTo
    {
        return $this->belongsTo(Pemesanan::class, 'id_pemesanan', 'id_pemesanan');
    }

    public function getRouteKeyName(): string
    {
        return 'id_pembayaran';
    }
}
