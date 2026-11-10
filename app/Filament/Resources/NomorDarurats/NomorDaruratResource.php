<?php

namespace App\Filament\Resources\NomorDarurats;

use App\Filament\Resources\NomorDarurats\Pages\CreateNomorDarurat;
use App\Filament\Resources\NomorDarurats\Pages\EditNomorDarurat;
use App\Filament\Resources\NomorDarurats\Pages\ListNomorDarurats;
use App\Filament\Resources\NomorDarurats\Schemas\NomorDaruratForm;
use App\Filament\Resources\NomorDarurats\Tables\NomorDaruratsTable;
use App\Models\NomorDarurat;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NomorDaruratResource extends Resource
{
    protected static ?string $model = NomorDarurat::class;

    protected static ?string $modelLabel = 'Nomor Darurat';

    protected static ?string $pluralModelLabel = 'Nomor Darurat';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return NomorDaruratForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NomorDaruratsTable::configure($table);
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
            'index' => ListNomorDarurats::route('/'),
            'create' => CreateNomorDarurat::route('/create'),
            'edit' => EditNomorDarurat::route('/{record}/edit'),
        ];
    }
}
