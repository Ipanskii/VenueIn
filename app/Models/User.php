<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom primary key kustom sesuai ERD (bukan konvensi default `id`).
     */
    protected $primaryKey = 'id_pengguna';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_telepon',
        'role',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Gedung-gedung yang dikelola oleh pengguna ini (jika role = Admin).
     */
    public function gedungs(): HasMany
    {
        return $this->hasMany(Gedung::class, 'id_admin', 'id_pengguna');
    }

    /**
     * Pemesanan yang dibuat oleh pengguna ini (jika role = Penyewa).
     */
    public function pemesanans(): HasMany
    {
        return $this->hasMany(Pemesanan::class, 'id_pengguna', 'id_pengguna');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    public function isPenyewa(): bool
    {
        return $this->role === 'Penyewa';
    }
}
