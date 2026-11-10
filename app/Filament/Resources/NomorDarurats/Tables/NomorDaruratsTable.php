<?php

namespace App\Filament\Resources\NomorDarurats\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NomorDaruratsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('unitDarurat.nama_unit')->label('Unit'),
                TextColumn::make('lokasi')->label('Lokasi')->searchable(),
                TextColumn::make('nomor_telepon')->label('Nomor Telepon'),
            ])
            ->filters([
                SelectFilter::make('unit')
                    ->relationship('unitDarurat', 'nama_unit'),
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
