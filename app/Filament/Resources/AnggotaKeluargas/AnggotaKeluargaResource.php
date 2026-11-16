<?php

namespace App\Filament\Resources\AnggotaKeluargas;

use App\Filament\Resources\AnggotaKeluargas\Pages\CreateAnggotaKeluarga;
use App\Filament\Resources\AnggotaKeluargas\Pages\EditAnggotaKeluarga;
use App\Filament\Resources\AnggotaKeluargas\Pages\ListAnggotaKeluargas;
use App\Filament\Resources\AnggotaKeluargas\Schemas\AnggotaKeluargaForm;
use App\Filament\Resources\AnggotaKeluargas\Tables\AnggotaKeluargasTable;
use App\Models\AnggotaKeluarga;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AnggotaKeluargaResource extends Resource
{
    protected static ?string $model = AnggotaKeluarga::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return AnggotaKeluargaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnggotaKeluargasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnggotaKeluargas::route('/'),
            'create' => CreateAnggotaKeluarga::route('/create'),
            'edit' => EditAnggotaKeluarga::route('/{record}/edit'),
        ];
    }
}
