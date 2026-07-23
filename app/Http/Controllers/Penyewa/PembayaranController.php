<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePembayaranRequest;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function create(Pemesanan $pemesanan): View|RedirectResponse
    {
        if ((int) $pemesanan->id_pengguna !== (int) auth()->id()) {
            abort(403);
        }

        if ($pemesanan->status_pemesanan !== 'Pending') {
            return redirect()
                ->route('pemesanan.show', $pemesanan)
                ->withErrors(['status' => 'Pembayaran hanya dapat dikirim untuk pemesanan berstatus Pending.']);
        }

        $pemesanan->load(['gedung', 'pembayaran']);

        return view('penyewa.pembayaran.create', compact('pemesanan'));
    }

    public function store(StorePembayaranRequest $request, Pemesanan $pemesanan): RedirectResponse
    {
        $validated = $request->validated();

        $path = $request->file('bukti_transfer')
            ->store('bukti_transfer', 'public');

        $pembayaranLama = $pemesanan->pembayaran;
        if ($pembayaranLama && $pembayaranLama->bukti_transfer) {
            Storage::disk('public')->delete($pembayaranLama->bukti_transfer);
        }

        Pembayaran::updateOrCreate(
            ['id_pemesanan' => $pemesanan->id_pemesanan],
            [
                'jumlah'             => $validated['jumlah'],
                'metode_pembayaran'  => $validated['metode_pembayaran'],
                'status_pembayaran'  => 'Menunggu Verifikasi',
                'bukti_transfer'     => $path,
                'tanggal_bayar'      => null,
            ]
        );

        return redirect()
            ->route('pemesanan.show', $pemesanan)
            ->with('success', 'Bukti pembayaran berhasil diunggah. Mohon tunggu verifikasi dari admin.');
    }
}
