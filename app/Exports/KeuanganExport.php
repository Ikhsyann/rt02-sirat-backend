<?php

namespace App\Exports;

use App\Exports\Sheets\KeuanganSummarySheet;
use App\Exports\Sheets\KeuanganTransactionsSheet;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class KeuanganExport implements Export, WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new KeuanganSummarySheet,
            new KeuanganTransactionsSheet,
        ];
    }
}
