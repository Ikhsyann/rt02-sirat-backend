<?php

namespace App\Filament\Resources\NomorDarurats\Pages;

use App\Filament\Resources\NomorDarurats\NomorDaruratResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNomorDarurat extends EditRecord
{
    protected static string $resource = NomorDaruratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
