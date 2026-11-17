<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExportDataRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Pilihan di luar daftar jatuh ke pesan validasi, bukan 500.
            'jenis_data' => ['required', Rule::in(['keuangan', 'penduduk'])],
            'format' => ['required', Rule::in(['xlsx', 'csv'])],

            // Filter Rekap Keuangan.
            'tanggal_dari' => ['nullable', 'date'],
            'tanggal_sampai' => ['nullable', 'date'],
            'jenis_keuangan' => ['nullable', Rule::in(['semua', 'income', 'expense'])],
            'kategori' => ['nullable', 'string', 'max:255'],

            // Filter Data Penduduk (semua opsional, default "Semua").
            'blok_id' => ['nullable', Rule::exists('bloks', 'id')],
            'jenis_kelamin' => ['nullable', Rule::in(['Laki-laki', 'Perempuan'])],
            'status_perkawinan' => [
                'nullable',
                Rule::in(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']),
            ],
            'status_hubungan_keluarga' => [
                'nullable',
                Rule::in([
                    'Kepala Keluarga', 'Suami', 'Istri', 'Anak', 'Menantu',
                    'Cucu', 'Orang Tua', 'Mertua', 'Famili Lain',
                ]),
            ],
        ];
    }
}
