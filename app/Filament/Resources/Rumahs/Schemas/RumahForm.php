<?php

namespace App\Filament\Resources\Rumahs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RumahForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('blok_id')
                    ->label('Blok')
                    ->relationship(
                        name: 'blok',
                        titleAttribute: 'nama_blok'
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('nomor_rumah')
                    ->label('Nomor Rumah')
                    ->placeholder('Contoh: 001')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
