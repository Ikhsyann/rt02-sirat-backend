<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Hanya superadmin yang dapat melihat daftar akun pengelola.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'superadmin';
    }

    /**
     * Superadmin hanya dapat melihat akun admin dan superadmin.
     */
    public function view(User $user, User $model): bool
    {
        return $user->role === 'superadmin'
            && in_array($model->role, ['admin', 'superadmin'], true);
    }

    /**
     * Hanya superadmin yang dapat membuat akun pengelola.
     */
    public function create(User $user): bool
    {
        return $user->role === 'superadmin';
    }

    /**
     * Hanya superadmin yang dapat mengubah akun pengelola.
     */
    public function update(User $user, User $model): bool
    {
        return $user->role === 'superadmin'
            && in_array($model->role, ['admin', 'superadmin'], true);
    }

    /**
     * Penghapusan akun pengelola tidak diperbolehkan.
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
}
