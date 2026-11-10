<?php

namespace App\Filament\Resources\Keuangans\Pages;

use App\Filament\Resources\Keuangans\KeuanganResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKeuangan extends EditRecord
{
    protected static string $resource = KeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
