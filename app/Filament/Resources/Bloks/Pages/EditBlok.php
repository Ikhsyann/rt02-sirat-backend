<?php

namespace App\Filament\Resources\Bloks\Pages;

use App\Filament\Resources\Bloks\BlokResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBlok extends EditRecord
{
    protected static string $resource = BlokResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
