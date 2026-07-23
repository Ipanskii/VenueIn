<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\Gedung;
use App\Models\Pemesanan;
use Illuminate\View\View;

class GedungController extends Controller
{
    public function index(): View
    {
        $query = Gedung::where('status', 'Tersedia');

        if (request()->filled('q')) {
            $cari = request('q');
            $query->where(function ($q) use ($cari): void {
                $q->where('nama_gedung', 'like', "%{$cari}%")
                  ->orWhere('alamat', 'like', "%{$cari}%")
                  ->orWhere('fasilitas', 'like', "%{$cari}%");
            });
        }

        if (request()->filled('kapasitas')) {
            $query->where('kapasitas', '>=', request()->integer('kapasitas'));
        }

        if (request()->filled('harga_max')) {
            $query->where('harga_per_hari', '<=', request()->integer('harga_max'));
        }

        $gedungs = $query
            ->orderBy('nama_gedung')
            ->paginate(12)
            ->withQueryString();

        return view('penyewa.gedung.index', compact('gedungs'));
    }

    public function show(Gedung $gedung): View
    {
        $tanggalTerpesan = Pemesanan::where('id_gedung', $gedung->id_gedung)
            ->where('status_pemesanan', '!=', 'Dibatalkan')
            ->where('tanggal_selesai', '>=', now()->toDateString())
            ->get(['tanggal_mulai', 'tanggal_selesai']);

        return view('penyewa.gedung.show', compact('gedung', 'tanggalTerpesan'));
    }
}
