<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Mengubah ENUM menjadi VARCHAR agar role baru
            // dapat ditambahkan tanpa mengubah struktur kolom lagi.
            $table->string('role', 20)
                ->default('warga')
                ->change();
        });
    }

    public function down(): void
    {
        // Jangan rollback jika masih ada akun superadmin.
        // Ini mencegah data role menjadi tidak valid atau hilang.
        if (DB::table('users')->where('role', 'superadmin')->exists()) {
            throw new RuntimeException(
                'Tidak dapat membatalkan migration: masih terdapat akun superadmin.'
            );
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE users MODIFY role ENUM('admin', 'warga') NOT NULL DEFAULT 'warga'"
            );

            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)
                ->default('warga')
                ->change();
        });
    }
};
