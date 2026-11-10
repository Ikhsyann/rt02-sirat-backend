<?php

namespace App\Filament\Resources\KartuKeluargas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KartuKeluargasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_kk')->label('Nomor KK'),
                TextColumn::make('rumah.nomor_rumah')->label('Rumah'),
                TextColumn::make('rumah.blok.nama_blok')->label('Blok'),
                ImageColumn::make('foto_kk')->disk('public'),
                TextColumn::make('anggota_keluargas_count')->counts('anggotaKeluargas')->label('Jumlah Anggota'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
