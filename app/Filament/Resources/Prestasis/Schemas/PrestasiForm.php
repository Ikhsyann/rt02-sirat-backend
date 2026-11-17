<?php

namespace App\Filament\Resources\Prestasis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PrestasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('judul')
                    ->label('Judul Prestasi')
                    ->placeholder('Contoh: Juara 1 Lomba Kebersihan Antargang')
                    ->required()
                    ->maxLength(255),

                Select::make('jenis')
                    ->label('Jenis Prestasi')
                    ->options([
                        'sertifikat' => 'Sertifikat',
                        'piala' => 'Piala',
                    ])
                    ->required(),

                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->default(today('Asia/Makassar'))
                    ->maxDate(today('Asia/Makassar'))
                    ->native(false)
                    ->displayFormat('d/m/Y'),

                Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->placeholder('Tuliskan keterangan prestasi...')
                    ->rows(5)
                    ->maxLength(5000)
                    ->columnSpanFull(),

                FileUpload::make('foto')
                    ->label('Foto')
                    ->image()
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ])
                    ->maxSize(5120)
                    ->disk('public')
                    ->visibility('public')
                    ->directory('prestasi')
                    ->preventFilePathTampering()
                    ->imageEditor()
                    ->helperText('Format JPG, PNG, atau WebP. Maksimal 5 MB.'),
            ]);
    }
}
