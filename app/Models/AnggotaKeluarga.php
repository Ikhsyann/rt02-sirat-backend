<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class AnggotaKeluarga extends Model
{
    protected $fillable = [
        'kartu_keluarga_id', 'user_id', 'nama', 'nik', 'tempat_lahir',
        'tanggal_lahir', 'jenis_kelamin', 'pekerjaan', 'golongan_darah',
        'status_perkawinan', 'tanggal_perkawinan_perceraian', 'status_hubungan_keluarga',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_perkawinan_perceraian' => 'date',
    ];

    public function kartuKeluarga()
    {
        return $this->belongsTo(KartuKeluarga::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    protected static function booted(): void
    {
        static::saving(function (AnggotaKeluarga $anggota): void {
            // Anggota keluarga boleh tidak memiliki akun.
            if ($anggota->user_id === null) {
                return;
            }

            $user = User::query()->find($anggota->user_id);

            // Hanya akun warga yang boleh dihubungkan.
            if (! $user || $user->role !== 'warga') {
                throw ValidationException::withMessages([
                    'user_id' => 'Hanya akun dengan role warga yang dapat dihubungkan.',
                ]);
            }

            // Satu akun tidak boleh digunakan anggota lain.
            $sudahTerhubung = static::query()
                ->where('user_id', $anggota->user_id)
                ->when(
                    $anggota->exists,
                    fn ($query) => $query->where('id', '!=', $anggota->getKey())
                )
                ->exists();

            if ($sudahTerhubung) {
                throw ValidationException::withMessages([
                    'user_id' => 'Akun warga sudah terhubung dengan anggota keluarga lain.',
                ]);
            }
        });
    }
}
