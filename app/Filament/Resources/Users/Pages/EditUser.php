<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Halaman edit ditutup untuk akun superadmin; UserResource::canEdit
     * adalah satu-satunya sumber aturan ini.
     */
    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canEdit($this->getRecord()), 403);
    }

    protected function getHeaderActions(): array
    {
        // Tidak menyediakan tindakan hapus akun.
        return [];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $role = $data['role'] ?? null;

        if (! in_array($role, ['admin', 'superadmin'], true)) {
            throw ValidationException::withMessages([
                'data.role' => 'Hak akses yang dipilih tidak valid.',
            ]);
        }

        unset($data['role']);

        return DB::transaction(function () use ($record, $data, $role): User {
            $user = User::query()
                ->lockForUpdate()
                ->findOrFail($record->getKey());

            // Pengelola tidak boleh mengubah role akun sendiri.
            if (
                (string) auth()->id() === (string) $user->getKey()
                && $role !== $user->role
            ) {
                throw ValidationException::withMessages([
                    'data.role' => 'Anda tidak dapat mengubah hak akses akun sendiri.',
                ]);
            }

            // Superadmin terakhir tidak boleh diturunkan menjadi admin.
            if (
                $user->role === 'superadmin'
                && $role !== 'superadmin'
            ) {
                $superadmins = User::query()
                    ->where('role', 'superadmin')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get(['id']);

                if ($superadmins->count() <= 1) {
                    throw ValidationException::withMessages([
                        'data.role' => 'Superadmin terakhir tidak boleh kehilangan hak aksesnya.',
                    ]);
                }
            }

            $user->fill($data);
            $user->save();

            // Penetapan role tetap melewati perlindungan model User.
            if ($role !== $user->role) {
                $user->forceFill([
                    'role' => $role,
                ])->save();
            }

            return $user->refresh();
        });
    }
}
