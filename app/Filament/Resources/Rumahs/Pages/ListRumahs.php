<?php

namespace App\Filament\Resources\Rumahs\Pages;

use App\Filament\Resources\Rumahs\RumahResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRumahs extends ListRecords
{
    protected static string $resource = RumahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
