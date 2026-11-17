<?php

namespace App\Exports\Sheets;

use App\Models\Keuangan;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KeuanganTransactionsSheet extends DefaultValueBinder implements FromQuery, WithColumnFormatting, WithColumnWidths, WithCustomValueBinder, WithHeadings, WithMapping, WithTitle
{
    public function query(): Builder
    {
        return Keuangan::query()
            ->orderBy('tanggal')
            ->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Jenis Transaksi',
            'Kategori',
            'Nama Barang / Keperluan',
            'Jumlah',
            'Harga Satuan',
            'Total',
            'Saldo Berjalan',
            'Keterangan',
        ];
    }

    public function map($record): array
    {
        return [
            $record->tanggal->format('d/m/Y'),
            $record->jenis === 'income'
                ? 'Pemasukan'
                : 'Pengeluaran',
            $record->kategori,
            $record->nama_barang,
            $record->jumlah,
            (float) $record->harga_satuan,
            (float) $record->total,
            (float) $record->saldo,
            $record->keterangan ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Transaksi';
    }

    public function columnFormats(): array
    {
        return [
            'F' => '"Rp" #,##0.00;-"Rp" #,##0.00',
            'G' => '"Rp" #,##0.00;-"Rp" #,##0.00',
            'H' => '"Rp" #,##0.00;-"Rp" #,##0.00',
            'F:H' => '"Rp" #,##0.00;[Red]-"Rp" #,##0.00;"Rp" 0.00',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14,
            'B' => 19,
            'C' => 18,
            'D' => 36,
            'E' => 11,
            'F' => 18,
            'G' => 18,
            'H' => 20,
            'I' => 32,
        ];
    }

    /**
     * Memastikan teks dari database tetap dianggap sebagai teks,
     * bukan formula Excel.
     */
    public function bindValue(Cell $cell, mixed $value): bool
    {
        if (
            in_array($cell->getColumn(), ['B', 'C', 'D', 'I'], true)
            && is_string($value)
        ) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = max($sheet->getHighestRow(), 1);

        $sheet->setShowGridlines(false);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:I{$lastRow}");

        // Header tabel
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'name' => 'Aptos',
                'bold' => true,
                'size' => 10,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '16324F'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '256D85'],
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(34);

        // Isi tabel
        if ($lastRow >= 2) {
            $sheet->getStyle("A2:I{$lastRow}")->applyFromArray([
                'font' => [
                    'name' => 'Aptos',
                    'size' => 10,
                    'color' => ['rgb' => '334155'],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_HAIR,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                ],
            ]);

            // Warna selang-seling agar mudah dibaca
            for ($row = 2; $row <= $lastRow; $row += 2) {
                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8FAFC'],
                    ],
                ]);
            }

            // Perataan isi kolom
            $sheet->getStyle("A2:A{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("B2:B{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("E2:E{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("F2:H{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $sheet->getStyle("D2:D{$lastRow}")
                ->getAlignment()
                ->setWrapText(true);

            $sheet->getStyle("I2:I{$lastRow}")
                ->getAlignment()
                ->setWrapText(true);
        }

        // Pengaturan cetak
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        return [];
    }
}
