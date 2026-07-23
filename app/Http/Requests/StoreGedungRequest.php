<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGedungRequest extends FormRequest
{
    /**
     * Hanya pengguna dengan role Admin yang boleh membuat gedung baru.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'nama_gedung'    => ['required', 'string', 'max:255'],
            'alamat'         => ['required', 'string', 'max:1000'],
            'deskripsi'      => ['nullable', 'string', 'max:5000'],
            'kapasitas'      => ['required', 'integer', 'min:1', 'max:100000'],
            'harga_per_hari' => ['required', 'numeric', 'min:0', 'max:999999999999'],
            'fasilitas'      => ['nullable', 'string', 'max:5000'],
            'status'         => ['required', 'in:Tersedia,Perbaikan'],
            'foto'           => ['nullable', 'image', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_gedung.required'    => 'Nama gedung wajib diisi.',
            'nama_gedung.max'         => 'Nama gedung maksimal 255 karakter.',
            'alamat.required'         => 'Alamat gedung wajib diisi.',
            'kapasitas.required'      => 'Kapasitas gedung wajib diisi.',
            'kapasitas.integer'       => 'Kapasitas harus berupa angka bulat.',
            'kapasitas.min'           => 'Kapasitas minimal 1 orang.',
            'harga_per_hari.required' => 'Harga per hari wajib diisi.',
            'harga_per_hari.numeric'  => 'Harga per hari harus berupa angka.',
            'harga_per_hari.min'      => 'Harga per hari tidak boleh negatif.',
            'status.required'         => 'Status gedung wajib dipilih.',
            'status.in'               => 'Status gedung harus salah satu dari: Tersedia, Perbaikan.',
            'foto.image'              => 'File foto harus berupa gambar (jpg, png, atau webp).',
            'foto.max'                => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
