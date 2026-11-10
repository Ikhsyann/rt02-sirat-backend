<?php

namespace App\Filament\Resources\AnggotaKeluargas\Pages;

use App\Filament\Resources\AnggotaKeluargas\AnggotaKeluargaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnggotaKeluargas extends ListRecords
{
    protected static string $resource = AnggotaKeluargaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
