<?php

namespace App\Filament\Resources\UnitDarurats\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NomorDaruratsRelationManager extends RelationManager
{
    protected static string $relationship = 'nomorDarurats';

    protected static ?string $title = 'Daftar Nomor Darurat';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('lokasi')
                    ->label('Lokasi')
                    ->placeholder('Contoh: Balikpapan Utara')
                    ->required()
                    ->maxLength(255),

                TextInput::make('nomor_telepon')
                    ->label('Nomor Telepon')
                    ->placeholder('Contoh: 112')
                    ->tel()
                    ->required()
                    ->maxLength(30),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('lokasi')
            ->columns([
                TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nomor_telepon')
                    ->label('Nomor Telepon')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Ditambahkan')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Nomor'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Ubah'),

                DeleteAction::make()
                    ->label('Hapus'),
            ]);
    }
}
