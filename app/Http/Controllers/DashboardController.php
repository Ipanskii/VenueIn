<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'gedung_tersedia' => Gedung::where('status', 'Tersedia')->count(),

            'pemesanan_pending' => Pemesanan::where('status_pemesanan', 'Pending')->count(),

            'pemesanan_bulan_ini' => Pemesanan::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'pendapatan_bulan_ini' => Pemesanan::where('status_pemesanan', 'Disetujui')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_harga'),

            'pemesanan_terbaru' => Pemesanan::with(['user', 'gedung', 'pembayaran'])
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
