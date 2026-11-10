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
                    ->relationship('blok', 'nama_blok')
                    ->required(),
                TextInput::make('nomor_rumah')
                    ->label('Nomor Rumah')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),
            ]);
    }
}
