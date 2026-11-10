<?php

namespace App\Filament\Resources\Rumahs\Pages;

use App\Filament\Resources\Rumahs\RumahResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRumah extends EditRecord
{
    protected static string $resource = RumahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
