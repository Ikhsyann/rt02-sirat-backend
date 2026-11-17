<?php

namespace App\Filament\Resources\Wargas\Pages;

use App\Filament\Resources\Wargas\WargaResource;
use App\Models\User;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditWarga extends EditRecord
{
    protected static string $resource = WargaResource::class;

    protected function getHeaderActions(): array
    {
        // Tidak menyediakan tindakan hapus akun.
        return [];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Role tidak disentuh lewat resource ini; akun tetap warga apa pun
        // yang terselip dalam payload.
        unset($data['role']);

        return DB::transaction(function () use ($record, $data): User {
            $user = User::query()
                ->lockForUpdate()
                ->findOrFail($record->getKey());

            $user->fill($data);
            $user->save();

            return $user->refresh();
        });
    }
}
