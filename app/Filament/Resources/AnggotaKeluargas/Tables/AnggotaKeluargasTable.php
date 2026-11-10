<?php

namespace App\Filament\Resources\AnggotaKeluargas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnggotaKeluargasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->label('Nama')->searchable()->sortable(),
                TextColumn::make('nik')->label('NIK')->searchable(),
                TextColumn::make('jenis_kelamin')->label('Jenis Kelamin'),
                TextColumn::make('tanggal_lahir')->label('Tanggal Lahir')->date('d-m-Y')->sortable(),
                TextColumn::make('kartuKeluarga.nomor_kk')->label('Nomor KK'),
                TextColumn::make('status_hubungan_keluarga')->label('Status Hubungan'),
                TextColumn::make('user.email')->label('Akun Warga'),
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
