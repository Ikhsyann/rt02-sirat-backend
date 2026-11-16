<?php

namespace App\Filament\Resources\UnitDarurats\Tables;

use App\Models\UnitDarurat;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UnitDaruratsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_unit')
                    ->label('Nama Unit Darurat')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Ditambahkan')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('nama_unit')
            ->recordActions([
                EditAction::make()
                    ->label('Ubah'),

                DeleteAction::make()
                    ->label('Hapus')
                    ->disabled(
                        fn (UnitDarurat $record): bool =>
                            $record->nomorDarurats()->exists()
                    )
                    ->tooltip(
                        fn (UnitDarurat $record): ?string =>
                            $record->nomorDarurats()->exists()
                                ? 'Unit tidak dapat dihapus karena masih memiliki nomor darurat.'
                                : null
                    ),
            ]);
    }
}