<?php

namespace App\Filament\Resources\UnitDarurats\Pages;

use App\Filament\Resources\UnitDarurats\UnitDaruratResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUnitDarurats extends ListRecords
{
    protected static string $resource = UnitDaruratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
