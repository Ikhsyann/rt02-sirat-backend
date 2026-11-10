<?php

namespace App\Filament\Resources\Bloks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BlokForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_blok')
                    ->label('Nama Blok')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),
            ]);
    }
}
