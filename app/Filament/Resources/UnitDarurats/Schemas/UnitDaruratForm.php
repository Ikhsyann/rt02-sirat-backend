<?php

namespace App\Filament\Resources\UnitDarurats\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UnitDaruratForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_unit')
                    ->label('Nama Unit Darurat')
                    ->placeholder('Contoh: Pemadam Kebakaran')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ]);
    }
}