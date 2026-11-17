<?php

namespace App\Filament\Resources\Prestasis\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PrestasisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->disk('public')
                    ->height(55)
                    ->width(85),

                TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jenis')
                    ->label('Jenis')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sertifikat' => 'Sertifikat',
                        'piala' => 'Piala',
                        default => 'Tidak diketahui',
                    })
                    ->badge()
                    ->color(fn (string $state): string => $state === 'piala'
                        ? 'warning'
                        : 'info'),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                SelectFilter::make('jenis')
                    ->label('Jenis Prestasi')
                    ->options([
                        'sertifikat' => 'Sertifikat',
                        'piala' => 'Piala',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Ubah'),

                DeleteAction::make()
                    ->label('Hapus')
                    ->modalHeading('Hapus Prestasi')
                    ->modalDescription(
                        'Prestasi yang dihapus tidak dapat dikembalikan.'
                    ),
            ]);
    }
}
