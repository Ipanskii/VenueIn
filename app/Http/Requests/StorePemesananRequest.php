<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePemesananRequest extends FormRequest
{
    /**
     * Hanya penyewa yang terautentikasi yang boleh submit form ini.
     * Middleware `role:Penyewa` di route sudah menjamin ini, namun
     * dideklarasikan ulang di sini sebagai lapisan pertahanan kedua
     * (defense in depth).
     */
    public function authorize(): bool
    {
        return $this->user()?->isPenyewa() ?? false;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'id_gedung' => [
                'required',
                'integer',
                'exists:gedungs,id_gedung',
            ],

            /**
             * `after_or_equal:today` membandingkan dengan tanggal server,
             * bukan timezone client. Pastikan `config/app.php` timezone
             * sudah disesuaikan ke 'Asia/Jakarta' sebelum deploy.
             */
            'tanggal_mulai' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            /**
             * `after:tanggal_mulai` (bukan after_or_equal) — melarang
             * tanggal selesai sama dengan tanggal mulai agar jumlah_hari
             * selalu >= 1 dan total_harga selalu > 0. Koreksi dari draf
             * PRD yang menggunakan `after_or_equal`.
             */
            'tanggal_selesai' => [
                'required',
                'date',
                'after:tanggal_mulai',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id_gedung.required'           => 'Gedung wajib dipilih.',
            'id_gedung.integer'            => 'Format ID gedung tidak valid.',
            'id_gedung.exists'             => 'Gedung yang dipilih tidak ditemukan dalam sistem.',
            'tanggal_mulai.required'       => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date'           => 'Format tanggal mulai tidak valid.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini.',
            'tanggal_selesai.required'     => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.date'         => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.after'        => 'Tanggal selesai harus lebih dari tanggal mulai (minimal 1 hari).',
        ];
    }
}
