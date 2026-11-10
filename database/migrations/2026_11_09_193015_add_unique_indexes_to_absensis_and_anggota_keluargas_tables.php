<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu anggota keluarga hanya bertaut satu akun warga; satu orang hanya absen satu kali per kegiatan.
        Schema::table('anggota_keluargas', function (Blueprint $table) {
            $table->unique('user_id');
        });

        Schema::table('absensis', function (Blueprint $table) {
            $table->unique(['kegiatan_id', 'anggota_keluarga_id']);
        });
    }

    public function down(): void
    {
        Schema::table('anggota_keluargas', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
        });

        Schema::table('absensis', function (Blueprint $table) {
            $table->dropUnique(['kegiatan_id', 'anggota_keluarga_id']);
        });
    }
};
