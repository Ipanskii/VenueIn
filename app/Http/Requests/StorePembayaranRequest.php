<?php

namespace App\Http\Requests;

use App\Models\Pemesanan;
use Illuminate\Foundation\Http\FormRequest;

class StorePembayaranRequest extends FormRequest
{
    /**
     * Penyewa hanya boleh mengupload bukti bayar untuk pemesanan miliknya
     * sendiri yang masih berstatus Pending. Validasi kepemilikan dilakukan
     * di sini untuk mencegah manipulasi `id_pemesanan` dari URL.
     */
    public function authorize(): bool
    {
        /** @var Pemesanan|null $pemesanan */
        $pemesanan = $this->route('pemesanan');

        if (! $pemesanan) {
            return false;
        }

        return $this->user()?->isPenyewa()
            && (int) $pemesanan->id_pengguna === (int) $this->user()->getKey()
            && $pemesanan->status_pemesanan === 'Pending';
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'jumlah' => [
                'required',
                'numeric',
                'min:1',
            ],

            /**
             * `metode_pembayaran` dibatasi pada opsi yang dikelola sistem.
             * Tambahkan opsi baru di sini dan di komponen Blade dropdown
             * secara bersamaan untuk menjaga konsistensi.
             */
            'metode_pembayaran' => [
                'required',
                'string',
                'in:Transfer Bank,QRIS,Virtual Account',
            ],

            /**
             * `bukti_transfer` tidak ada di ERD awal — direkomendasikan
             * di PRD untuk kebutuhan verifikasi admin. Wajib tambahkan
             * kolom `bukti_transfer` (string, nullable) di migration
             * `pembayarans` sebelum method store() dijalankan.
             * Ukuran maks 2MB sudah cukup untuk screenshot/scan struk.
             */
            'bukti_transfer' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'jumlah.required'              => 'Jumlah pembayaran wajib diisi.',
            'jumlah.numeric'               => 'Jumlah pembayaran harus berupa angka.',
            'jumlah.min'                   => 'Jumlah pembayaran minimal Rp 1.',
            'metode_pembayaran.required'   => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in'         => 'Metode pembayaran harus salah satu dari: Transfer Bank, QRIS, Virtual Account.',
            'bukti_transfer.required'      => 'Bukti transfer wajib diunggah.',
            'bukti_transfer.file'          => 'Bukti transfer harus berupa file.',
            'bukti_transfer.mimes'         => 'Bukti transfer harus berformat JPG, PNG, atau PDF.',
            'bukti_transfer.max'           => 'Ukuran file bukti transfer maksimal 2MB.',
        ];
    }
}
