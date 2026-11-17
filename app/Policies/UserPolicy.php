<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Policy ini dipakai dua resource akun sekaligus (Manajemen Admin dan
     * Akun Warga). Pembatasan lanjut dilakukan di masing-masing resource.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'superadmin'], true);
    }

    public function view(User $user, User $model): bool
    {
        return $this->canManageTarget($user, $model);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'superadmin'], true);
    }

    /**
     * Superadmin boleh mengelola semua akun;
     * admin hanya boleh mengelola akun warga.
     */
    public function update(User $user, User $model): bool
    {
        return $this->canManageTarget($user, $model);
    }

    /**
     * Penghapusan akun tidak diperbolehkan untuk siapa pun.
     */
    public function delete(User $user, User $model): bool
    {
        return false;
    }

    public function restore(User $user, User $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }

    private function canManageTarget(User $actor, User $target): bool
    {
        if ($actor->role === 'superadmin') {
            return true;
        }

        return $actor->role === 'admin' && $target->role === 'warga';
    }
}
