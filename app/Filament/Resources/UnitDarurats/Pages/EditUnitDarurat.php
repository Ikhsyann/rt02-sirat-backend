<?php

namespace App\Filament\Resources\UnitDarurats\Pages;

use App\Filament\Resources\UnitDarurats\UnitDaruratResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUnitDarurat extends EditRecord
{
    protected static string $resource = UnitDaruratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
