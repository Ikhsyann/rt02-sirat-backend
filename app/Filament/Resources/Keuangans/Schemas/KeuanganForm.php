<?php

namespace App\Filament\Resources\Keuangans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KeuanganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->default(now())
                    ->maxDate(now()),
                Select::make('jenis')
                    ->label('Jenis')
                    ->options(['income' => 'Kas Masuk', 'expense' => 'Kas Keluar'])
                    ->required()
                    ->default('income'),
                TextInput::make('kategori')
                    ->label('Kategori')
                    ->required()
                    ->maxLength(50),
                TextInput::make('nama_barang')
                    ->label('Nama Barang / Rincian')
                    ->required()
                    ->maxLength(100),
                TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(1),
                TextInput::make('harga_satuan')
                    ->label('Harga Satuan (Rp)')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->columnSpanFull(),
            ]);
    }
}
