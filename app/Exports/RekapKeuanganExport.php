<?php

namespace App\Exports;

use App\Exports\Sheets\KeuanganFilteredSummarySheet;
use App\Exports\Sheets\KeuanganFilteredTransactionsSheet;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Rekap keuangan berfilter (periode, jenis, kategori): sheet Ringkasan
 * sesuai rentang + sheet Transaksi. Versi tanpa filter memakai
 * KeuanganExport yang tetap utuh.
 */
class RekapKeuanganExport implements Export, WithMultipleSheets
{
    public function __construct(private readonly Builder $query) {}

    public function sheets(): array
    {
        return [
            new KeuanganFilteredSummarySheet(clone $this->query),
            new KeuanganFilteredTransactionsSheet(clone $this->query),
        ];
    }
}
