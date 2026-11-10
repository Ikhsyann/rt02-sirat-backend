<?php

namespace App\Filament\Resources\Rumahs;

use App\Filament\Resources\Rumahs\Pages\CreateRumah;
use App\Filament\Resources\Rumahs\Pages\EditRumah;
use App\Filament\Resources\Rumahs\Pages\ListRumahs;
use App\Filament\Resources\Rumahs\Schemas\RumahForm;
use App\Filament\Resources\Rumahs\Tables\RumahsTable;
use App\Models\Rumah;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RumahResource extends Resource
{
    protected static ?string $model = Rumah::class;

    protected static ?string $modelLabel = 'Rumah';

    protected static ?string $pluralModelLabel = 'Rumah';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return RumahForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RumahsTable::configure($table);
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
            'index' => ListRumahs::route('/'),
            'create' => CreateRumah::route('/create'),
            'edit' => EditRumah::route('/{record}/edit'),
        ];
    }
}
