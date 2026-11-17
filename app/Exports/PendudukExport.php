<?php

namespace App\Exports;

use App\Models\AnggotaKeluarga;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class PendudukExport extends DefaultValueBinder implements FromQuery, WithCustomValueBinder, WithHeadings, WithMapping
{
    public function __construct(private readonly Builder $query) {}

    public static function headingLabels(): array
    {
        return [
            'Nama',
            'NIK',
            'Nomor KK',
            'Blok',
            'Nomor Rumah',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Pekerjaan',
            'Golongan Darah',
            'Status Perkawinan',
            'Tanggal Perkawinan/Perceraian',
            'Hubungan Keluarga',
            'Email Akun Warga',
        ];
    }

    public function headings(): array
    {
        return static::headingLabels();
    }

    /**
     * Query dasar penduduk lengkap dengan join blok/KK/akun; dipakai
     * export xlsx dan CSV agar isi kedua format tidak berbeda.
     */
    public static function baseQuery(): Builder
    {
        return AnggotaKeluarga::query()
            ->join('kartu_keluargas as kk', 'anggota_keluargas.kartu_keluarga_id', '=', 'kk.id')
            ->join('rumahs as r', 'kk.rumah_id', '=', 'r.id')
            ->join('bloks as b', 'r.blok_id', '=', 'b.id')
            ->leftJoin('users as u', 'anggota_keluargas.user_id', '=', 'u.id')
            ->select(
                'anggota_keluargas.*',
                'kk.nomor_kk',
                'b.nama_blok',
                'r.nomor_rumah',
                'u.email',
            );
    }

    /**
     * Query penduduk: FromQuery dieksekusi Laravel chunk() oleh package,
     * sehingga tidak memuat seluruh tabel ke memori sekaligus.
     */
    public function query(): Builder
    {
        return static::baseQuery()->orderBy('anggota_keluargas.id');
    }

    /**
     * Sumber satu-satu untuk baris: dipakai sheet xlsx ini dan CSV
     * penduduk agar kedua format tidak berbeda isi.
     */
    public static function row(AnggotaKeluarga $row): array
    {
        return [
            $row->nama ?? '-',
            // NIK dan nomor KK wajib teks: kalau tidak, Excel membaca 16
            // digit sebagai angka dan merusaknya menjadi 6,47E+15.
            (string) $row->nik,
            (string) $row->nomor_kk,
            $row->nama_blok ?? '-',
            $row->nomor_rumah ?? '-',
            $row->tempat_lahir ?? '-',
            $row->tanggal_lahir?->format('d/m/Y') ?? '-',
            $row->jenis_kelamin ?? '-',
            $row->pekerjaan ?? '-',
            $row->golongan_darah ?? '-',
            $row->status_perkawinan ?? '-',
            $row->tanggal_perkawinan_perceraian?->format('d/m/Y') ?? '-',
            $row->status_hubungan_keluarga ?? '-',
            $row->email ?? '-',
        ];
    }

    public function map($row): array
    {
        return static::row($row);
    }

    /**
     * Kolom NIK (B) dan Nomor KK (C) dipaksa tipe string eksplisit.
     */
    public function bindValue(Cell $cell, mixed $value): bool
    {
        if (in_array($cell->getColumn(), ['B', 'C'], true) && is_string($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
