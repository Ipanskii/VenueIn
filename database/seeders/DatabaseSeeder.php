<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun Admin default — gunakan ini untuk masuk ke panel /admin.
        User::factory()->admin()->create([
            'nama' => 'Admin VenueIn',
            'email' => 'admin@venuein.test',
            'password' => Hash::make('password'),
        ]);

        // Akun Penyewa contoh — untuk uji alur pemesanan.
        User::factory()->create([
            'nama' => 'Budi Penyewa',
            'email' => 'penyewa@venuein.test',
            'password' => Hash::make('password'),
        ]);
    }
}
