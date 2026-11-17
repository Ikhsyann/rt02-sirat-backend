<?php

namespace App\Filament\Resources\Wargas\Tables;

use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WargasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status_akun')
                    ->label('Status')
                    ->state(fn (User $record): string => $record->anggotaKeluarga()->exists()
                        ? 'Terhubung ke anggota'
                        : 'Belum terhubung')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Terhubung ke anggota'
                        ? 'success'
                        : 'warning'),
            ])
            // Tidak ada aksi hapus: kebijakan proyek adalah akun tidak dihapus.
            ->recordActions([
                EditAction::make()
                    ->label('Kelola Akun'),
            ])
            ->toolbarActions([]);
    }
}
