<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PendudukExport;
use App\Exports\RekapKeuanganExport;
use App\Http\Requests\Admin\ExportDataRequest;
use App\Models\AnggotaKeluarga;
use App\Models\Keuangan;
use Filament\Facades\Filament;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportDataController
{
    public function __invoke(ExportDataRequest $request)
    {
        // Otorisasi sama dengan export keuangan: hanya admin dan superadmin.
        abort_unless(
            $request->user()?->canAccessPanel(
                Filament::getPanel('admin')
            ),
            403,
            'Anda tidak memiliki izin mengunduh data.'
        );

        $data = $request->validated();
        $namaTanggal = now()->format('Y-m-d');

        if ($data['jenis_data'] === 'keuangan') {
            $query = $this->queryKeuangan($data);

            if ($data['format'] === 'xlsx') {
                return Excel::download(
                    new RekapKeuanganExport(clone $query),
                    "Laporan-Keuangan-RT-02-{$namaTanggal}.xlsx"
                );
            }

            // CSV: hanya tabel transaksi, ditulis streaming agar hemat RAM.
            return $this->streamCsv(
                $query->cursor()->map(fn (Keuangan $r): array => [
                    $r->tanggal->format('d/m/Y'),
                    $r->jenis === 'income' ? 'Pemasukan' : 'Pengeluaran',
                    $r->kategori ?? '-',
                    $r->nama_barang ?? '-',
                    (string) $r->jumlah,
                    (string) $r->harga_satuan,
                    (string) $r->total,
                    (string) $r->saldo,
                    $r->keterangan ?? '-',
                ]),
                ['Tanggal', 'Jenis Transaksi', 'Kategori', 'Nama Barang / Keperluan', 'Jumlah', 'Harga Satuan', 'Total', 'Saldo Berjalan', 'Keterangan'],
                "Laporan-Keuangan-RT-02-{$namaTanggal}.csv"
            );
        }

        $query = $this->queryPenduduk($data);

        if ($data['format'] === 'xlsx') {
            return Excel::download(
                new PendudukExport($query),
                "Data-Penduduk-RT-02-{$namaTanggal}.xlsx"
            );
        }

        return $this->streamCsv(
            $query->cursor()->map(fn (AnggotaKeluarga $r): array => PendudukExport::row($r)),
            PendudukExport::headingLabels(),
            "Data-Penduduk-RT-02-{$namaTanggal}.csv"
        );
    }

    private function queryKeuangan(array $f)
    {
        // Nilai filter dibaca dari $f (bukan parameter when) agar binding
        // tidak menerima boolean.
        return Keuangan::query()
            ->when($f['tanggal_dari'] ?? null, fn ($q, $d) => $q->whereDate('tanggal', '>=', $d))
            ->when($f['tanggal_sampai'] ?? null, fn ($q, $d) => $q->whereDate('tanggal', '<=', $d))
            ->when(
                ($f['jenis_keuangan'] ?? 'semua') !== 'semua',
                fn ($q) => $q->where('jenis', $f['jenis_keuangan'])
            )
            ->when($f['kategori'] ?? null, fn ($q, $kategori) => $q->where('kategori', $kategori));
    }

    private function queryPenduduk(array $f)
    {
        return PendudukExport::baseQuery()
            ->when($f['blok_id'] ?? null, fn ($q, $id) => $q->where('b.id', $id))
            ->when($f['jenis_kelamin'] ?? null, fn ($q, $v) => $q->where('anggota_keluargas.jenis_kelamin', $v))
            ->when($f['status_perkawinan'] ?? null, fn ($q, $v) => $q->where('anggota_keluargas.status_perkawinan', $v))
            ->when($f['status_hubungan_keluarga'] ?? null, fn ($q, $v) => $q->where('anggota_keluargas.status_hubungan_keluarga', $v))
            ->orderBy('anggota_keluargas.id');
    }

    /**
     * CSV memakai BOM UTF-8 agar terbaca benar di Excel, dan tiap sel
     * teks yang berawalan = + - @ diberi apostrof agar tidak dieksekusi
     * sebagai formula (CSV injection).
     */
    private function streamCsv($rows, array $headings, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($rows, $headings): void {
            $out = fopen('php://output', 'wb');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headings);
            foreach ($rows as $row) {
                fputcsv($out, array_map(fn ($v): string => $this->escapeCsvCell($v), $row));
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function escapeCsvCell(mixed $value): string
    {
        $value = (string) ($value ?? '-');

        if ($value !== '' && in_array($value[0], ['=', '+', '-', '@'], true)) {
            return "'".$value;
        }

        return $value;
    }
}
