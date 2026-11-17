<?php

namespace App\Exports\Sheets;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Versi berfilter dari sheet Ringkasan: total pemasukan, total
 * pengeluaran, dan saldo akhir dihitung atas query periode terpilih.
 * Struktur baris mengikuti induknya agar gaya sheet asli tetap berlaku.
 */
class KeuanganFilteredSummarySheet extends KeuanganSummarySheet
{
    public function __construct(private readonly Builder $query) {}

    public function array(): array
    {
        $query = $this->query;

        $saldoAwal = (float) (
            DB::table('keuangan_settings')
                ->where('id', 1)
                ->value('saldo_awal') ?? 0
        );

        $pemasukan = (float) (clone $query)
            ->where('jenis', 'income')
            ->sum('total');

        $pengeluaran = (float) (clone $query)
            ->where('jenis', 'expense')
            ->sum('total');

        // Saldo akhir diambil dari baris terakhir dalam rentang filter;
        // bila rentang kosong, pakai saldo awal dari pengaturan kas.
        $saldoAkhir = (clone $query)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->value('saldo');

        $jumlahTransaksi = (clone $query)->count();

        return [
            ['LAPORAN KEUANGAN RT 02'],
            ['Tanggal Ekspor', now()->format('d/m/Y H:i')],
            // Baris kosong diberi sel kosong: FromArray melewatkan array
            // kosong sepenuhnya, yang akan menggeser baris di bawahnya.
            ['', ''],
            ['Ringkasan', 'Nominal'],
            ['Total Pemasukan', $pemasukan],
            ['Total Pengeluaran', $pengeluaran],
            ['Saldo Akhir', (float) ($saldoAkhir ?? $saldoAwal)],
            ['Jumlah Transaksi', $jumlahTransaksi.' transaksi'],
        ];
    }
}
