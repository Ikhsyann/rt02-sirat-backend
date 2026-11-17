<?php

namespace App\Exports\Sheets;

use Illuminate\Database\Eloquent\Builder;

/**
 * Versi berfilter dari sheet Transaksi. Mewarisi seluruh pemetaan dan
 * gaya sheet asli; yang berubah hanya sumber query-nya.
 */
class KeuanganFilteredTransactionsSheet extends KeuanganTransactionsSheet
{
    public function __construct(private readonly Builder $query) {}

    public function query(): Builder
    {
        // Urutan sama dengan versi tanpa filter agar saldo tetap kronologis.
        return (clone $this->query)
            ->orderBy('tanggal')
            ->orderBy('id');
    }
}
