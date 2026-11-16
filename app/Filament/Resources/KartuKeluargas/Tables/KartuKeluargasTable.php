<?php

namespace App\Filament\Resources\KartuKeluargas\Tables;

use App\Models\KartuKeluarga;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KartuKeluargasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_kk')
                    ->label('Nomor KK')
                    ->formatStateUsing(
                        fn (string $state): string =>
                            substr($state, 0, 4)
                            . str_repeat('*', 8)
                            . substr($state, -4)
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rumah.nomor_rumah')
                    ->label('Nomor Rumah')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rumah.blok.nama_blok')
                    ->label('Blok')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('foto_kk')
                    ->label('Foto KK')
                    ->state(
                        fn (KartuKeluarga $record): string =>
                            filled($record->foto_kk)
                                ? 'Tersedia'
                                : 'Belum diunggah'
                    )
                    ->badge()
                    ->color(
                        fn (string $state): string =>
                            $state === 'Tersedia' ? 'success' : 'gray'
                    ),

                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit'),

                DeleteAction::make()
                    ->label('Hapus')
                    ->disabled(
                        fn (KartuKeluarga $record): bool =>
                            $record->anggotaKeluargas()->exists()
                    ),
            ]);
    }
}