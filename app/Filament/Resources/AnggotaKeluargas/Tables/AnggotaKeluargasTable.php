<?php

namespace App\Filament\Resources\AnggotaKeluargas\Tables;

use App\Models\AnggotaKeluarga;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnggotaKeluargasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nik')
                    ->label('NIK')
                    ->formatStateUsing(
                        fn (?string $state): string => filled($state)
                                ? substr($state, 0, 4)
                                    .str_repeat('*', 8)
                                    .substr($state, -4)
                                : '-'
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kartuKeluarga.nomor_kk')
                    ->label('Nomor KK')
                    ->formatStateUsing(
                        fn (?string $state): string => filled($state)
                                ? '••••••••••••'.substr($state, -4)
                                : '-'
                    )
                    ->searchable(),

                TextColumn::make('kartuKeluarga.rumah.nomor_rumah')
                    ->label('Nomor Rumah')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kartuKeluarga.rumah.blok.nama_blok')
                    ->label('Blok')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status_hubungan_keluarga')
                    ->label('Hubungan Keluarga')
                    ->badge()
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Akun Warga')
                    ->placeholder('Belum terhubung')
                    ->searchable(),

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
                        fn (AnggotaKeluarga $record): bool => $record->absensis()->exists()
                            || $record->user_id !== null
                    ),
            ]);
    }
}
