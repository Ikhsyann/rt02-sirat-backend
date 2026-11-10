<?php

namespace App\Filament\Resources\Rumahs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RumahsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('blok.nama_blok')->label('Blok'),
                TextColumn::make('nomor_rumah')->label('Nomor Rumah'),
                TextColumn::make('kartu_keluargas_count')->counts('kartuKeluargas')->label('Jumlah KK'),
            ])
            ->filters([
                SelectFilter::make('blok')
                    ->relationship('blok', 'nama_blok'),
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
