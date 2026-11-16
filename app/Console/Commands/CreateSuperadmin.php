<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateSuperadmin extends Command
{
    protected $signature = 'app:create-superadmin';

    protected $description = 'Membuat akun superadmin pertama jika belum tersedia';

    public function handle(): int
    {
        // Pastikan belum ada superadmin.
        if (User::query()->where('role', 'superadmin')->exists()) {
            $this->error('Akun superadmin sudah tersedia. Pembuatan pertama dibatalkan.');

            return self::FAILURE;
        }

        $this->info('Pembuatan Superadmin Pertama SIRAT');
        $this->line('Isi informasi akun yang akan menjadi pengelola utama.');

        $name = trim((string) $this->ask('Nama lengkap'));
        $email = strtolower(trim((string) $this->ask('Alamat email')));

        // Validasi nama dan email.
        $validator = Validator::make(
            [
                'name' => $name,
                'email' => $email,
            ],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email'),
                ],
            ],
            [],
            [
                'name' => 'Nama lengkap',
                'email' => 'Alamat email',
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        // Password tidak ditampilkan di terminal.
        $password = (string) $this->secret('Kata sandi (minimal 12 karakter)');
        $confirmation = (string) $this->secret('Ulangi kata sandi');

        $passwordValidator = Validator::make(
            ['password' => $password],
            [
                'password' => ['required', 'string', 'min:12'],
            ],
            [],
            ['password' => 'Kata sandi']
        );

        if ($passwordValidator->fails()) {
            foreach ($passwordValidator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        if (! hash_equals($password, $confirmation)) {
            $this->error('Konfirmasi kata sandi tidak sama.');

            return self::FAILURE;
        }

        try {
            $user = DB::transaction(function () use ($name, $email, $password): ?User {
                // Pemeriksaan ulang untuk mencegah pembuatan ganda.
                if (User::query()->where('role', 'superadmin')->exists()) {
                    return null;
                }

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                ]);

                // Role ditetapkan secara eksplisit.
                // Tidak memasukkan role ke dalam $fillable.
                $user->forceFill([
                    'role' => 'superadmin',
                ])->save();

                return $user;
            });
        } catch (QueryException $exception) {
            report($exception);

            $this->error('Akun gagal dibuat. Periksa kembali email dan kondisi database.');

            return self::FAILURE;
        }

        if (! $user) {
            $this->error('Superadmin sudah dibuat oleh proses lain. Pembuatan dibatalkan.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Superadmin berhasil dibuat.');
        $this->line('Nama: '.$user->name);
        $this->line('Email: '.$user->email);
        $this->line('Hak akses: Superadmin');

        return self::SUCCESS;
    }
}
