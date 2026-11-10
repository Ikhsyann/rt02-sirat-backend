<?php

namespace App\Filament\Resources\Kegiatans\Pages;

use App\Filament\Resources\Kegiatans\KegiatanResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateKegiatan extends CreateRecord
{
    protected static string $resource = KegiatanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::slug($data['nama_kegiatan'].' '.$data['tanggal'].' '.Str::random(4));

        return $data;
    }
}
