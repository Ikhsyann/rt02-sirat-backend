<?php

namespace App\Filament\Widgets;

use App\Models\Keuangan;
use Filament\Widgets\ChartWidget;

class KeuanganChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    protected ?string $heading = 'Grafik Pemasukan dan Pengeluaran';

    protected ?string $description = 'Perbandingan keuangan RT selama 6 bulan terakhir.';

    protected function getData(): array
    {
        // Periode 6 bulan terakhir termasuk bulan berjalan
        $startMonth = now('Asia/Makassar')->startOfMonth()->subMonths(5);
        $endMonth = now('Asia/Makassar')->endOfMonth();

        $labels = [];
        $pemasukan = [];
        $pengeluaran = [];

        // Inisialisasi seluruh bulan dengan nilai nol
        for ($i = 0; $i < 6; $i++) {
            $month = $startMonth->copy()->addMonths($i);
            $key = $month->format('Y-m');

            $labels[] = $month->locale('id')->translatedFormat('M Y');
            $pemasukan[$key] = 0;
            $pengeluaran[$key] = 0;
        }

        // Ambil transaksi pada periode yang dipilih
        $transaksi = Keuangan::query()
            ->whereBetween('tanggal', [
                $startMonth->toDateString(),
                $endMonth->toDateString(),
            ])
            ->get(['tanggal', 'jenis', 'total']);

        // Kelompokkan transaksi berdasarkan bulan
        foreach ($transaksi as $item) {
            $key = $item->tanggal->format('Y-m');

            if (! array_key_exists($key, $pemasukan)) {
                continue;
            }

            $nominal = (float) $item->total;

            if ($item->jenis === 'income') {
                $pemasukan[$key] += $nominal;
            } elseif ($item->jenis === 'expense') {
                $pengeluaran[$key] += $nominal;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan',
                    'data' => array_values($pemasukan),
                    'backgroundColor' => '#16A34A',
                    'borderColor' => '#15803D',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => array_values($pengeluaran),
                    'backgroundColor' => '#DC2626',
                    'borderColor' => '#B91C1C',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}