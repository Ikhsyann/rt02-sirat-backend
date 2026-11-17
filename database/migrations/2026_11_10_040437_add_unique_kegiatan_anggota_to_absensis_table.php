<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasIndex('absensis', 'absensis_kegiatan_anggota_unique')) {
            Schema::table('absensis', function (Blueprint $table) {
                $table->unique(
                    ['kegiatan_id', 'anggota_keluarga_id'],
                    'absensis_kegiatan_anggota_unique'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropUnique('absensis_kegiatan_anggota_unique');
        });
    }
};