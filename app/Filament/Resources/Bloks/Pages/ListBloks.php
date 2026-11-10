<?php

namespace App\Filament\Resources\Bloks\Pages;

use App\Filament\Resources\Bloks\BlokResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBloks extends ListRecords
{
    protected static string $resource = BlokResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
