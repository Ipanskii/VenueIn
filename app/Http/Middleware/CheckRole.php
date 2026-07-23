<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Memvalidasi bahwa pengguna terautentikasi memiliki salah satu
     * role yang diizinkan untuk mengakses route ini.
     *
     * Nilai role yang valid mengikuti enum kolom `role` pada tabel
     * `users`: "Admin" atau "Penyewa" (case-sensitive, sesuai migration).
     *
     * Contoh penggunaan pada route:
     *   ->middleware('role:Admin')
     *   ->middleware('role:Admin,Penyewa')
     *
     * @param  string  ...$roles  Daftar role yang diizinkan, dipisah koma pada definisi route.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Anda harus login untuk mengakses halaman ini.');
        }

        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
