<?php

namespace App\Filament\Resources\Keuangans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KeuangansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')->label('Tanggal')->date('d-m-Y')->sortable(),
                TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'income' ? 'Masuk' : 'Keluar')
                    ->color(fn (string $state) => $state === 'income' ? Color::Green : Color::Red),
                TextColumn::make('kategori')->label('Kategori')->searchable(),
                TextColumn::make('nama_barang')->label('Rincian')->searchable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state, $record) => ($record->jenis === 'income' ? '' : '-').'Rp '.number_format($state, 0, ',', '.')),
                TextColumn::make('saldo')->label('Saldo')->formatStateUsing(fn ($state) => 'Rp '.number_format($state, 0, ',', '.')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('tanggal', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
