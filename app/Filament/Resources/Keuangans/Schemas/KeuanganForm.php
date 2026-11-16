<?php

namespace App\Filament\Resources\Keuangans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class KeuanganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Transaksi')
                    ->description('Masukkan informasi pemasukan atau pengeluaran kas RT.')
                    ->schema([
                        DatePicker::make('tanggal')
                            ->label('Tanggal Transaksi')
                            ->required()
                            ->default(today('Asia/Makassar')),

                        Select::make('jenis')
                            ->label('Jenis Transaksi')
                            ->options([
                                'income' => 'Pemasukan',
                                'expense' => 'Pengeluaran',
                            ])
                            ->native(false)
                            ->required(),

                        TextInput::make('kategori')
                            ->label('Kategori')
                            ->placeholder('Contoh: Iuran Warga')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('nama_barang')
                            ->label('Nama Barang / Keperluan')
                            ->placeholder('Contoh: Pembelian Lampu Jalan')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Rincian Nilai Transaksi')
                    ->schema([
                        TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->numeric()
                            ->rules(['integer'])
                            ->minValue(1)
                            ->default(1)
                            ->required()
                            ->live(onBlur: true),

                        TextInput::make('harga_satuan')
                            ->label('Harga Satuan')
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0.01)
                            ->required()
                            ->live(onBlur: true)
                            ->helperText(function (Get $get): string {
                                $jumlah = $get('jumlah');
                                $harga = $get('harga_satuan');

                                if (
                                    ! is_numeric($jumlah) ||
                                    ! is_numeric($harga) ||
                                    (int) $jumlah < 1 ||
                                    (float) $harga <= 0
                                ) {
                                    return 'Masukkan jumlah dan harga untuk melihat perkiraan total.';
                                }

                                $total = (int) $jumlah * (float) $harga;

                                return 'Perkiraan Total: Rp ' .
                                    number_format($total, 2, ',', '.');
                            }),
                    ])
                    ->columns(2),

                Section::make('Keterangan Tambahan')
                    ->schema([
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Tambahkan catatan transaksi jika diperlukan.')
                            ->maxLength(2000)
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}