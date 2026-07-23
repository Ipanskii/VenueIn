<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGedungRequest;
use App\Http\Requests\UpdateGedungRequest;
use App\Models\Gedung;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GedungController extends Controller
{
    public function index(): View
    {
        $gedungs = Gedung::with('admin')
            ->orderBy('id_gedung') #mengurutkan berdasarkan data lama ke data terbaru
            ->paginate(15);

        return view('admin.gedung.index', compact('gedungs'));
    }

    public function create(): View
    {
        return view('admin.gedung.create');
    }

    public function store(StoreGedungRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('gedung', 'public');
        }

        $gedung = Gedung::create([
            ...$data,
            'id_admin' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.gedung.show', $gedung)
            ->with('success', 'Gedung "' . $gedung->nama_gedung . '" berhasil ditambahkan.');
    }

    public function show(Gedung $gedung): View
    {
        $gedung->load([
            'admin',
            'pemesanans' => fn ($q) => $q->with('user')->latest()->take(10),
        ]);

        return view('admin.gedung.show', compact('gedung'));
    }

    public function edit(Gedung $gedung): View
    {
        return view('admin.gedung.edit', compact('gedung'));
    }

    public function update(UpdateGedungRequest $request, Gedung $gedung): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            if ($gedung->foto) {
                Storage::disk('public')->delete($gedung->foto);
            }

            $data['foto'] = $request->file('foto')->store('gedung', 'public');
        } else {
            unset($data['foto']);
        }

        $gedung->update($data);

        return redirect()
            ->route('admin.gedung.show', $gedung)
            ->with('success', 'Data gedung "' . $gedung->nama_gedung . '" berhasil diperbarui.');
    }

    public function destroy(Gedung $gedung): RedirectResponse
    {
        $adaPemesananAktif = $gedung->pemesanans()
            ->whereIn('status_pemesanan', ['Pending', 'Disetujui'])
            ->exists();

        if ($adaPemesananAktif) {
            return back()->withErrors([
                'gedung' => 'Gedung tidak dapat dihapus karena masih memiliki pemesanan aktif (Pending atau Disetujui).',
            ]);
        }

        if ($gedung->foto) {
            Storage::disk('public')->delete($gedung->foto);
        }

        $namaGedung = $gedung->nama_gedung;
        $gedung->delete();

        return redirect()
            ->route('admin.gedung.index')
            ->with('success', 'Gedung "' . $namaGedung . '" berhasil dihapus.');
    }
}
