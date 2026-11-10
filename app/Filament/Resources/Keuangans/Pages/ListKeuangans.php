<?php

namespace App\Filament\Resources\Keuangans\Pages;

use App\Filament\Resources\Keuangans\KeuanganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKeuangans extends ListRecords
{
    protected static string $resource = KeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
