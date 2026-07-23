<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePemesananRequest;
use App\Models\Gedung;
use App\Models\Pemesanan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PemesananController extends Controller
{
    /**
     * Tampilkan riwayat pemesanan milik penyewa yang sedang login.
     * Data di-eager load dengan relasi `gedung` dan `pembayaran` untuk
     * menghindari N+1 query saat ditampilkan di tabel.
     */
    public function index(): View
    {
        $pemesanans = Pemesanan::with(['gedung', 'pembayaran'])
            ->where('id_pengguna', auth()->id())
            ->latest()
            ->paginate(10);

        return view('penyewa.pemesanan.index', compact('pemesanans'));
    }

    /**
     * Tampilkan form pemesanan.
     *
     * Jika URL menyertakan query parameter `?id_gedung=X` (misalnya
     * dari klik tombol "Pesan" di card katalog), gedung akan di-prefill
     * secara otomatis di form.
     */
    public function create(): View
    {
        $gedungDipilih = null;

        if (request()->filled('id_gedung')) {
            $gedungDipilih = Gedung::where('status', 'Tersedia')
                ->findOrFail(request()->integer('id_gedung'));
        }

        $gedungs = Gedung::where('status', 'Tersedia')
            ->orderBy('nama_gedung')
            ->get();

        return view('penyewa.pemesanan.create', compact('gedungs', 'gedungDipilih'));
    }

    /**
     * Proses pembuatan pemesanan baru.
     *
     * Tiga lapisan perlindungan diterapkan secara berurutan:
     *  1. Validasi form (StorePemesananRequest) — tanggal, keberadaan gedung.
     *  2. Validasi status gedung — tidak bisa memesan gedung berstatus Perbaikan.
     *  3. Anti-double-booking dalam DB::transaction + lockForUpdate — mencegah
     *     konflik jadwal termasuk race condition dua request bersamaan.
     */
    public function store(StorePemesananRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // ── Lapisan 2: Cek status gedung ──────────────────────────────────────
        // Meskipun validasi awal sudah memastikan gedung ada di DB, status
        // gedung bisa berubah antara user membuka form dan menekan submit.
        $gedung = Gedung::findOrFail($validated['id_gedung']);

        if ($gedung->status !== 'Tersedia') {
            return back()->withErrors([
                'id_gedung' => 'Gedung ini sedang berstatus "Perbaikan" dan tidak dapat dipesan.',
            ])->withInput();
        }

        // ── Lapisan 3: Anti-double-booking dalam Transaksi DB ─────────────────
        $pemesanan = null;

        DB::transaction(function () use ($validated, $gedung, &$pemesanan) {

            // ── 3a. Lock pada baris gedung ─────────────────────────────────────
            //
            // MENGAPA LOCK PADA GEDUNG, BUKAN HANYA PADA PEMESANAN?
            //
            // `lockForUpdate()` hanya mengunci baris yang sudah ADA dan cocok
            // dengan kondisi WHERE. Jika belum ada pemesanan aktif untuk gedung
            // ini, query pada tabel `pemesanans` tidak akan mengunci apapun —
            // dua request bersamaan bisa sama-sama lolos pengecekan exists().
            //
            // Solusinya: kunci baris gedung terlebih dahulu. Karena baris gedung
            // pasti selalu ada, lock ini memastikan hanya satu transaksi per
            // gedung yang bisa berjalan secara bersamaan. Transaksi kedua harus
            // menunggu transaksi pertama COMMIT atau ROLLBACK sebelum
            // melanjutkan, sehingga race condition sepenuhnya dieliminasi.
            Gedung::where('id_gedung', $gedung->id_gedung)
                ->lockForUpdate()
                ->firstOrFail();

            // ── 3b. Deteksi tiga skenario overlap ─────────────────────────────
            //
            // Anggap rentang baru: [A, B] (tanggal_mulai A, tanggal_selesai B)
            // Anggap rentang lama: [C, D] (sudah ada di DB)
            //
            // Overlap terjadi jika salah satu dari kondisi ini terpenuhi:
            //
            //   Skenario 1 — Awal baru jatuh di dalam rentang lama:
            //     C ──────── A ──── D     → A BETWEEN C AND D
            //
            //   Skenario 2 — Akhir baru jatuh di dalam rentang lama:
            //     C ── B ──────── D       → B BETWEEN C AND D
            //
            //   Skenario 3 — Rentang baru membungkus rentang lama:
            //     A ──── C ──── D ── B   → C >= A AND D <= B
            //
            // Catatan: pemesanan berstatus 'Dibatalkan' diabaikan karena
            // gedung otomatis tersedia kembali saat dibatalkan.
            $bentrok = Pemesanan::where('id_gedung', $gedung->id_gedung)
                ->where('status_pemesanan', '!=', 'Dibatalkan')
                ->where(function (Builder $query) use ($validated): void {
                    $mulai   = $validated['tanggal_mulai'];
                    $selesai = $validated['tanggal_selesai'];

                    $query
                        // Skenario 1: tanggal_mulai request baru jatuh di dalam rentang lama
                        ->whereBetween('tanggal_mulai', [$mulai, $selesai])
                        // Skenario 2: tanggal_selesai request baru jatuh di dalam rentang lama
                        ->orWhereBetween('tanggal_selesai', [$mulai, $selesai])
                        // Skenario 3: rentang baru sepenuhnya membungkus rentang lama
                        ->orWhere(function (Builder $q) use ($mulai, $selesai): void {
                            $q->where('tanggal_mulai', '<=', $mulai)
                              ->where('tanggal_selesai', '>=', $selesai);
                        });
                })
                ->exists();

            if ($bentrok) {
                throw ValidationException::withMessages([
                    'tanggal_mulai' => 'Gedung sudah dipesan pada rentang tanggal tersebut. Silakan pilih tanggal lain.',
                ]);
            }

            // ── 3c. Hitung total harga ─────────────────────────────────────────
            //
            // Menggunakan hitungan INKLUSIF: sewa Jan-1 s/d Jan-3 = 3 hari
            // (Jan-1, Jan-2, Jan-3), bukan 2 hari. Ini konvensi standar
            // persewaan gedung/aula di mana setiap hari kalender dalam
            // rentang diperhitungkan sebagai 1 unit sewa.
            $tanggalMulai   = Carbon::parse($validated['tanggal_mulai']);
            $tanggalSelesai = Carbon::parse($validated['tanggal_selesai']);
            $jumlahHari     = (int) $tanggalMulai->diffInDays($tanggalSelesai) + 1;
            $totalHarga     = $gedung->harga_per_hari * $jumlahHari;

            // ── 3d. Simpan pemesanan ───────────────────────────────────────────
            $pemesanan = Pemesanan::create([
                'id_pengguna'      => auth()->id(),
                'id_gedung'        => $gedung->id_gedung,
                'tanggal_mulai'    => $validated['tanggal_mulai'],
                'tanggal_selesai'  => $validated['tanggal_selesai'],
                'status_pemesanan' => 'Pending',
                'total_harga'      => $totalHarga,
            ]);
        });

        /** @var \App\Models\Pemesanan $pemesanan */
        return redirect()
            ->route('pemesanan.show', $pemesanan)
            ->with('success', "Pemesanan berhasil dibuat. Total tagihan: Rp " . number_format($pemesanan->total_harga, 0, ',', '.') . ". Silakan lanjutkan ke pembayaran.");
    }

    /**
     * Tampilkan detail satu pemesanan.
     *
     * Guard kepemilikan: penyewa A tidak boleh melihat pemesanan milik
     * penyewa B meskipun menebak URL-nya. Ini adalah otorisasi berbasis
     * resource (ownership check), bukan berbasis role.
     */
    public function show(Pemesanan $pemesanan): View
    {
        $this->authorizeOwnership($pemesanan);

        $pemesanan->load(['gedung', 'pembayaran']);

        return view('penyewa.pemesanan.show', compact('pemesanan'));
    }

    /**
     * Batalkan pemesanan milik penyewa yang sedang login.
     *
     * Pembatalan hanya diizinkan saat status masih 'Pending'. Jika sudah
     * 'Disetujui' (berarti admin sudah memverifikasi dan mungkin gedung
     * sudah dipersiapkan), pembatalan harus melalui admin secara manual.
     */
    public function cancel(Pemesanan $pemesanan): RedirectResponse
    {
        $this->authorizeOwnership($pemesanan);

        if ($pemesanan->status_pemesanan !== 'Pending') {
            return back()->withErrors([
                'status' => 'Pemesanan dengan status "' . $pemesanan->status_pemesanan . '" tidak dapat dibatalkan secara mandiri. Hubungi admin.',
            ]);
        }

        $pemesanan->update(['status_pemesanan' => 'Dibatalkan']);

        return redirect()
            ->route('pemesanan.index')
            ->with('success', 'Pemesanan berhasil dibatalkan.');
    }

    /**
     * Pastikan pemesanan yang diakses adalah milik user yang sedang login.
     * Lempar 403 jika bukan miliknya.
     */
    private function authorizeOwnership(Pemesanan $pemesanan): void
    {
        if ((int) $pemesanan->id_pengguna !== (int) auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pemesanan ini.');
        }
    }
}
