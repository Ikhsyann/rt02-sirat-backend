<?php

namespace App\Filament\Resources\Keuangans\Pages;

use App\Filament\Resources\Keuangans\KeuanganResource;
use App\Models\Keuangan;
use App\Services\KeuanganService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use LogicException;

class EditKeuangan extends EditRecord
{
    protected static string $resource = KeuanganResource::class;

    protected function handleRecordUpdate(
        Model $record,
        array $data
    ): Model {
        if (! $record instanceof Keuangan) {
            throw new LogicException(
                'Record yang diedit bukan transaksi Keuangan.'
            );
        }

        return app(KeuanganService::class)->update($record, $data);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
