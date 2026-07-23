<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Digunakan oleh Admin\GedungController::update().
 * Dipersiapkan di Tahap 2 agar Tahap 3 (GedungController) dapat langsung
 * menggunakannya tanpa perlu membuat file baru.
 */
class UpdateGedungRequest extends FormRequest
{
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
        return (new StoreGedungRequest())->messages();
    }
}
