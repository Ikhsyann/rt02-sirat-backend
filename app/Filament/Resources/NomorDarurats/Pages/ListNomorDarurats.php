<?php

namespace App\Filament\Resources\NomorDarurats\Pages;

use App\Filament\Resources\NomorDarurats\NomorDaruratResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNomorDarurats extends ListRecords
{
    protected static string $resource = NomorDaruratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
