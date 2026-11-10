<?php

namespace App\Filament\Resources\Kegiatans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KegiatanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_kegiatan')
                    ->label('Nama Kegiatan')
                    ->required()
                    ->maxLength(100),
                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->default(now()),
                Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
                // kolom foto_dokumentasi string tunggal — 1 foto per kegiatan
                // Format eksplisit: `image/*` juga menerima HEIC, yang tidak bisa dirender browser.
                FileUpload::make('foto_dokumentasi')
                    ->label('Foto Dokumentasi')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->directory('kegiatan')
                    ->disk('public')
                    ->maxSize(5120),
            ]);
    }
}
