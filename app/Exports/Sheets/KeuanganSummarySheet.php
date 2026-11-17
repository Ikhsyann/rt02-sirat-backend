<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KeuanganSummarySheet implements FromArray, WithColumnFormatting, WithColumnWidths, WithStyles, WithTitle
{
    public function array(): array
    {
        $saldoAwal = (float) (
            DB::table('keuangan_settings')
                ->where('id', 1)
                ->value('saldo_awal') ?? 0
        );

        $pemasukan = (float) DB::table('keuangans')
            ->where('jenis', 'income')
            ->sum('total');

        $pengeluaran = (float) DB::table('keuangans')
            ->where('jenis', 'expense')
            ->sum('total');

        $saldoAkhir = DB::table('keuangans')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->value('saldo');

        if ($saldoAkhir === null) {
            $saldoAkhir = $saldoAwal;
        }

        $jumlahTransaksi = DB::table('keuangans')->count();

        return [
            ['LAPORAN KEUANGAN RT 02'],
            ['Tanggal Ekspor', now()->format('d/m/Y H:i')],
            [],
            ['Ringkasan', 'Nominal'],
            ['Saldo Awal', $saldoAwal],
            ['Total Pemasukan', $pemasukan],
            ['Total Pengeluaran', $pengeluaran],
            ['Saldo Akhir', (float) $saldoAkhir],
            ['Jumlah Transaksi', $jumlahTransaksi.' transaksi'],
        ];
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function columnFormats(): array
    {
        return [
            'B4:B7' => '"Rp" #,##0.00;[Red]-"Rp" #,##0.00;"Rp" 0.00',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 28,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->mergeCells('A1:B1');

        $sheet->setShowGridlines(false);
        $sheet->freezePane('A4');

        // Judul laporan
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => [
                'name' => 'Aptos Display',
                'bold' => true,
                'size' => 18,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '16324F'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(38);

        // Informasi tanggal ekspor
        $sheet->getStyle('A2:B2')->applyFromArray([
            'font' => [
                'name' => 'Aptos',
                'size' => 10,
                'italic' => true,
                'color' => ['rgb' => '64748B'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F1F5F9'],
            ],
        ]);

        // Header ringkasan
        $sheet->getStyle('A3:B3')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '256D85'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(3)->setRowHeight(26);

        // Isi ringkasan
        $sheet->getStyle('A4:B7')->applyFromArray([
            'font' => [
                'name' => 'Aptos',
                'size' => 11,
                'color' => ['rgb' => '334155'],
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E2E8F0'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Nilai nominal rata kanan
        $sheet->getStyle('B4:B7')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Saldo akhir dibuat lebih menonjol
        $sheet->getStyle('A7:B7')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '166534'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DCFCE7'],
            ],
        ]);

        // Jumlah transaksi
        $sheet->getStyle('A8:B8')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '334155'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F1F5F9'],
            ],
        ]);

        $sheet->getRowDimension(8)->setRowHeight(24);

        return [];
    }
}
