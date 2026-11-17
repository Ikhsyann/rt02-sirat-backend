<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu akun hanya boleh terhubung ke satu anggota keluarga.
        // Kolom tetap nullable sehingga anggota tanpa akun tetap diperbolehkan.
        if (! Schema::hasIndex('anggota_keluargas', 'anggota_keluargas_user_id_unique')) {
            Schema::table('anggota_keluargas', function (Blueprint $table) {
                $table->unique('user_id');
            });
        }

        // Riwayat absensi harus dipertahankan.
        // Anggota yang memiliki absensi tidak boleh dihapus.
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropForeign(['anggota_keluarga_id']);

            $table->foreign('anggota_keluarga_id')
                ->references('id')
                ->on('anggota_keluargas')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropForeign(['anggota_keluarga_id']);

            $table->foreign('anggota_keluarga_id')
                ->references('id')
                ->on('anggota_keluargas')
                ->cascadeOnDelete();
        });

        Schema::table('anggota_keluargas', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
        });
    }
};