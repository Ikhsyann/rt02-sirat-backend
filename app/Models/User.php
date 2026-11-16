<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\ValidationException;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Menentukan siapa yang dapat mengakses panel Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin'
            && $this->role === 'admin';
    }

    /**
     * Relasi User dengan Anggota Keluarga.
     */
    public function anggotaKeluarga(): HasOne
    {
        return $this->hasOne(AnggotaKeluarga::class);
    }

    protected static function booted(): void
    {
        static::saving(function (User $user): void {
            if (
                $user->isDirty('role')
                && $user->role !== 'warga'
                && $user->anggotaKeluarga()->exists()
            ) {
                throw ValidationException::withMessages([
                    'role' => 'Akun yang terhubung dengan anggota keluarga tidak dapat diubah menjadi admin.',
                ]);
            }
        });
    }
}