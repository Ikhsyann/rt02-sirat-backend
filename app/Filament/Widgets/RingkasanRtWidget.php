<?php

namespace App\Filament\Widgets;

use App\Models\AnggotaKeluarga;
use App\Models\KartuKeluarga;
use App\Models\Keuangan;
use App\Models\Rumah;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class RingkasanRtWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        // Statistik kependudukan
        $jumlahWarga = AnggotaKeluarga::count();
        $jumlahRumah = Rumah::count();
        $jumlahKK = KartuKeluarga::count();

        // Saldo terakhir berdasarkan tanggal dan ID transaksi
        $saldoAkhir = Keuangan::query()
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->value('saldo');

        // Jika belum ada transaksi, gunakan saldo awal
        if ($saldoAkhir === null) {
            $saldoAkhir = DB::table('keuangan_settings')
                ->where('id', 1)
                ->value('saldo_awal') ?? 0;
        }

        // Periode bulan berjalan
        $awalBulan = now('Asia/Makassar')->startOfMonth()->toDateString();
        $akhirBulan = now('Asia/Makassar')->endOfMonth()->toDateString();

        // Total pemasukan bulan berjalan
        $pemasukanBulanIni = Keuangan::query()
            ->where('jenis', 'income')
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->sum('total');

        // Total pengeluaran bulan berjalan
        $pengeluaranBulanIni = Keuangan::query()
            ->where('jenis', 'expense')
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->sum('total');

        $periode = now('Asia/Makassar')->locale('id')->translatedFormat('F Y');

        return [
            Stat::make('Jumlah Warga', number_format($jumlahWarga, 0, ',', '.'))
                ->description('Total anggota keluarga terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Jumlah Rumah', number_format($jumlahRumah, 0, ',', '.'))
                ->description('Total rumah terdaftar')
                ->descriptionIcon('heroicon-m-home')
                ->color('success'),

            Stat::make('Kartu Keluarga', number_format($jumlahKK, 0, ',', '.'))
                ->description('Total kartu keluarga terdaftar')
                ->descriptionIcon('heroicon-m-identification')
                ->color('primary'),

            Stat::make('Saldo Kas RT', $this->formatRupiah($saldoAkhir))
                ->description('Saldo terakhir seluruh transaksi')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('success'),

            Stat::make('Pemasukan Bulan Ini', $this->formatRupiah($pemasukanBulanIni))
                ->description("Periode {$periode}")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Pengeluaran Bulan Ini', $this->formatRupiah($pengeluaranBulanIni))
                ->description("Periode {$periode}")
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }

    private function formatRupiah($nominal): string
    {
        return 'Rp ' . number_format((float) $nominal, 0, ',', '.');
    }
}