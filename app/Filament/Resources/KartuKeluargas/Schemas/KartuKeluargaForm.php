<?php

namespace App\Filament\Resources\KartuKeluargas\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KartuKeluargaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('rumah_id')
                    ->label('Rumah')
                    ->relationship('rumah', 'nomor_rumah')
                    ->required(),
                TextInput::make('nomor_kk')
                    ->label('Nomor KK')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(30),
                // Format eksplisit: `image/*` juga menerima HEIC, yang tidak bisa dirender browser.
                // Foto KK adalah dokumen pribadi: simpan di disk privat,
                // bukan public/storage yang bisa diakses tanpa login.
                FileUpload::make('foto_kk')
                    ->label('Foto Kartu Keluarga')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->directory('kartu-keluarga')
                    ->disk('local')
                    ->maxSize(5120),
            ]);
    }
}
