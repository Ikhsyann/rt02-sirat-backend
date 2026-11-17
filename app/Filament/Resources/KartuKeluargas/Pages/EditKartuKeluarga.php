<?php

namespace App\Filament\Resources\KartuKeluargas\Pages;

use App\Filament\Resources\KartuKeluargas\KartuKeluargaResource;
use Filament\Resources\Pages\EditRecord;

class EditKartuKeluarga extends EditRecord
{
    protected static string $resource = KartuKeluargaResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
