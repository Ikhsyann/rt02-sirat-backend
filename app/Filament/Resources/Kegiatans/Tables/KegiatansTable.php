<?php

namespace App\Filament\Resources\Kegiatans\Tables;

use App\Models\Kegiatan;
use Filament\Actions\DeleteAction;
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
                TextColumn::make('nama_kegiatan')
                    ->label('Nama Kegiatan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal')
                    ->label('Tanggal Kegiatan')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('foto_dokumentasi')
                    ->label('Dokumentasi')
                    ->disk('public')
                    ->height(55)
                    ->width(85),

                TextColumn::make('absensis_count')
                    ->label('Jumlah Peserta')
                    ->counts('absensis')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tanggal', 'desc')
            ->recordActions([
                EditAction::make()
                    ->label('Ubah'),

                DeleteAction::make()
                    ->label('Hapus')
                    ->modalHeading('Hapus Kegiatan')
                    ->modalDescription(
                        'Menghapus kegiatan ini juga akan menghapus seluruh catatan absensi yang terkait. Tindakan ini tidak dapat dibatalkan.'
                    ),
            ]);
    }
}