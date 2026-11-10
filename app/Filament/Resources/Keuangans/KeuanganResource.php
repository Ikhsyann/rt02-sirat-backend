<?php

namespace App\Filament\Resources\Keuangans;

use App\Filament\Resources\Keuangans\Pages\CreateKeuangan;
use App\Filament\Resources\Keuangans\Pages\EditKeuangan;
use App\Filament\Resources\Keuangans\Pages\ListKeuangans;
use App\Filament\Resources\Keuangans\Schemas\KeuanganForm;
use App\Filament\Resources\Keuangans\Tables\KeuangansTable;
use App\Models\Keuangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KeuanganResource extends Resource
{
    protected static ?string $model = Keuangan::class;

    protected static ?string $modelLabel = 'Keuangan';

    protected static ?string $pluralModelLabel = 'Keuangan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return KeuanganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KeuangansTable::configure($table);
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
            'index' => ListKeuangans::route('/'),
            'create' => CreateKeuangan::route('/create'),
            'edit' => EditKeuangan::route('/{record}/edit'),
        ];
    }
}
