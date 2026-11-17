<?php

namespace App\Filament\Resources\Keuangans\Pages;

use App\Filament\Resources\Keuangans\KeuanganResource;
use App\Services\KeuanganService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateKeuangan extends CreateRecord
{
    protected static string $resource = KeuanganResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(KeuanganService::class)->create($data);
    }
}
