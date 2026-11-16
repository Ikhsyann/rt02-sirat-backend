<?php

namespace App\Filament\Resources\AnggotaKeluargas\Pages;

use App\Filament\Resources\AnggotaKeluargas\AnggotaKeluargaResource;
use Filament\Resources\Pages\EditRecord;

class EditAnggotaKeluarga extends EditRecord
{
    protected static string $resource = AnggotaKeluargaResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}