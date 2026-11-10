<?php

namespace App\Filament\Resources\Kegiatans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KegiatansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_kegiatan')->label('Nama Kegiatan')->searchable(),
                TextColumn::make('tanggal')->label('Tanggal')->date('d-m-Y')->sortable(),
                TextColumn::make('absensis_count')->counts('absensis')->label('Jumlah Kehadiran'),
                ImageColumn::make('foto_dokumentasi')->disk('public')->circular(),
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
