<?php

namespace App\Filament\Resources\KartuKeluargas\Schemas;

use App\Models\Rumah;
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
                    ->relationship(
                        name: 'rumah',
                        titleAttribute: 'nomor_rumah',
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn (Rumah $record): string =>
                            "{$record->nomor_rumah} - Blok {$record->blok->nama_blok}"
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('nomor_kk')
                    ->label('Nomor Kartu Keluarga')
                    ->placeholder('Masukkan 16 digit nomor KK')
                    ->helperText('Nomor KK harus terdiri dari 16 digit angka.')
                    ->required()
                    ->minLength(16)
                    ->maxLength(16)
                    ->regex('/^[0-9]{16}$/')
                    ->unique(ignoreRecord: true),

                FileUpload::make('foto_kk')
                    ->label('Foto Kartu Keluarga')
                    ->image()
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ])
                    ->maxSize(5120)
                    ->disk('local')
                    ->directory('kartu-keluarga')
                    ->visibility('private')
                    ->preventFilePathTampering()
                    ->imageEditor()
                    ->helperText('Format JPG, PNG, atau WEBP. Maksimal 5 MB.'),
            ]);
    }
}