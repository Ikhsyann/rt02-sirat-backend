<?php

namespace App\Filament\Resources\NomorDarurats\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NomorDaruratForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('unit_darurat_id')
                    ->label('Unit Darurat')
                    ->relationship('unitDarurat', 'nama_unit')
                    ->required(),
                TextInput::make('lokasi')
                    ->label('Lokasi')
                    ->required()
                    ->maxLength(100),
                TextInput::make('nomor_telepon')
                    ->label('Nomor Telepon')
                    ->required()
                    ->maxLength(20)
                    ->regex('/^[0-9+()\-\s]+$/'),
            ]);
    }
}
