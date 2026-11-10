<?php

namespace App\Filament\Resources\Bloks;

use App\Filament\Resources\Bloks\Pages\CreateBlok;
use App\Filament\Resources\Bloks\Pages\EditBlok;
use App\Filament\Resources\Bloks\Pages\ListBloks;
use App\Filament\Resources\Bloks\Schemas\BlokForm;
use App\Filament\Resources\Bloks\Tables\BloksTable;
use App\Models\Blok;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BlokResource extends Resource
{
    protected static ?string $model = Blok::class;

    protected static ?string $modelLabel = 'Blok';

    protected static ?string $pluralModelLabel = 'Blok';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BlokForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BloksTable::configure($table);
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
            'index' => ListBloks::route('/'),
            'create' => CreateBlok::route('/create'),
            'edit' => EditBlok::route('/{record}/edit'),
        ];
    }
}
