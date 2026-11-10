<?php

namespace App\Filament\Resources\UnitDarurats;

use App\Filament\Resources\UnitDarurats\Pages\CreateUnitDarurat;
use App\Filament\Resources\UnitDarurats\Pages\EditUnitDarurat;
use App\Filament\Resources\UnitDarurats\Pages\ListUnitDarurats;
use App\Filament\Resources\UnitDarurats\Schemas\UnitDaruratForm;
use App\Filament\Resources\UnitDarurats\Tables\UnitDaruratsTable;
use App\Models\UnitDarurat;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UnitDaruratResource extends Resource
{
    protected static ?string $model = UnitDarurat::class;

    protected static ?string $modelLabel = 'Unit Darurat';

    protected static ?string $pluralModelLabel = 'Unit Darurat';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return UnitDaruratForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UnitDaruratsTable::configure($table);
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
            'index' => ListUnitDarurats::route('/'),
            'create' => CreateUnitDarurat::route('/create'),
            'edit' => EditUnitDarurat::route('/{record}/edit'),
        ];
    }
}
