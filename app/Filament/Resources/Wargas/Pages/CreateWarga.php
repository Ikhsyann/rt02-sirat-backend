<?php

namespace App\Filament\Resources\Wargas\Pages;

use App\Filament\Resources\Wargas\WargaResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateWarga extends CreateRecord
{
    protected static string $resource = WargaResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Role tidak pernah diambil dari input: selalu dipaksa 'warga' di
        // server, meski payload menyelipkan nilai role lain.
        unset($data['role']);

        return DB::transaction(function () use ($data): User {
            $user = User::create($data);

            // Penetapan role melewati $fillable karena role memang dijaga
            // dari mass assignment.
            $user->forceFill([
                'role' => 'warga',
            ])->save();

            return $user;
        });
    }
}
