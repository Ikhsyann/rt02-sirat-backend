<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $role = $data['role'] ?? null;

        if (! in_array($role, ['admin', 'superadmin'], true)) {
            throw ValidationException::withMessages([
                'data.role' => 'Hak akses yang dipilih tidak valid.',
            ]);
        }

        unset($data['role']);

        return DB::transaction(function () use ($data, $role): User {
            $user = User::create($data);

            // Role ditetapkan secara eksplisit karena tidak termasuk $fillable.
            $user->forceFill([
                'role' => $role,
            ])->save();

            return $user;
        });
    }
}
