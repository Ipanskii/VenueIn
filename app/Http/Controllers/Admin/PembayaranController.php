<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function index(): View
    {
        $query = Pembayaran::with(['pemesanan.user', 'pemesanan.gedung'])
            ->latest();

        if (request()->filled('status')) {
            $query->where('status_pembayaran', request('status'));
        }

        $pembayarans = $query->paginate(15)->withQueryString();

        return view('admin.pembayaran.index', compact('pembayarans'));
    }

    public function show(Pembayaran $pembayaran): View
    {
        $pembayaran->load(['pemesanan.user', 'pemesanan.gedung']);

        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    public function verify(Pembayaran $pembayaran): RedirectResponse
    {
        if ($pembayaran->status_pembayaran !== 'Menunggu Verifikasi') {
            return back()->withErrors([
                'status' => 'Hanya pembayaran berstatus "Menunggu Verifikasi" yang dapat dikonfirmasi. Status saat ini: ' . $pembayaran->status_pembayaran . '.',
            ]);
        }

        $pembayaran->update([
            'status_pembayaran' => 'Lunas',
            'tanggal_bayar'     => now(),
        ]);

        return redirect()
            ->route('admin.pembayaran.show', $pembayaran)
            ->with('success', 'Pembayaran dikonfirmasi sebagai Lunas. Pemesanan otomatis disetujui.');
    }

    public function reject(Pembayaran $pembayaran): RedirectResponse
    {
        if ($pembayaran->status_pembayaran !== 'Menunggu Verifikasi') {
            return back()->withErrors([
                'status' => 'Hanya pembayaran berstatus "Menunggu Verifikasi" yang dapat ditolak. Status saat ini: ' . $pembayaran->status_pembayaran . '.',
            ]);
        }

        $pembayaran->update([
            'status_pembayaran' => 'Belum Bayar',
        ]);

        return redirect()
            ->route('admin.pembayaran.show', $pembayaran)
            ->with('success', 'Pembayaran ditolak. Penyewa diminta mengunggah ulang bukti bayar.');
    }
}
