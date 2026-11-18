<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pengelola')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->label('Hak Akses')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'superadmin' => 'Superadmin',
                        'admin' => 'Admin',
                        default => 'Tidak diketahui',
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'superadmin' => 'success',
                        'admin' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Filter Hak Akses')
                    ->options([
                        'admin' => 'Admin',
                        'superadmin' => 'Superadmin',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Kelola Akun')
                    ->visible(fn (User $record): bool => $record->role !== 'superadmin'),
            ])
            ->toolbarActions([]);
    }
}
