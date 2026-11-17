<?php

namespace App\Filament\Resources\Wargas;

use App\Filament\Resources\Wargas\Pages\CreateWarga;
use App\Filament\Resources\Wargas\Pages\EditWarga;
use App\Filament\Resources\Wargas\Pages\ListWargas;
use App\Filament\Resources\Wargas\Schemas\WargaForm;
use App\Filament\Resources\Wargas\Tables\WargasTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class WargaResource extends Resource
{
    protected static ?string $model = User::class;

    // Slug eksplisit karena dua resource memakai model User yang sama;
    // default slug model akan bentrok dengan Manajemen Admin.
    protected static ?string $slug = 'akun-warga';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Akun Warga';

    protected static ?string $modelLabel = 'Akun Warga';

    protected static ?string $pluralModelLabel = 'Akun Warga';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan Sistem';

    protected static ?int $navigationSort = 98;

    public static function form(Schema $schema): Schema
    {
        return WargaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WargasTable::configure($table);
    }

    /**
     * Daftar hanya berisi akun ber-role warga.
     * Akun pengelola tidak akan tercampur di sini.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', 'warga');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWargas::route('/'),
            'create' => CreateWarga::route('/create'),
            'edit' => EditWarga::route('/{record}/edit'),
        ];
    }
}
