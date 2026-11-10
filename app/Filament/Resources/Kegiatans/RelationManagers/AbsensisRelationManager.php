<?php

namespace App\Filament\Resources\Kegiatans\RelationManagers;

use App\Models\AnggotaKeluarga;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AbsensisRelationManager extends RelationManager
{
    protected static string $relationship = 'absensis';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('anggota_keluarga_id')
                    ->label('Anggota Keluarga')
                    ->options(fn () => AnggotaKeluarga::query()->orderBy('nama')->pluck('nama', 'id'))
                    ->required(),
                Select::make('status_hadir')
                    ->label('Status Kehadiran')
                    ->options(['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'])
                    ->required()
                    ->default('hadir'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('anggota_keluarga_id')
            ->columns([
                TextColumn::make('anggotaKeluarga.nama')->label('Nama'),
                TextColumn::make('status_hadir')->label('Status')->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
