
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Blok tidak dapat dihapus jika masih memiliki Rumah.
        Schema::table('rumahs', function (Blueprint $table) {
            $table->dropForeign(['blok_id']);

            $table->foreign('blok_id')
                ->references('id')
                ->on('bloks')
                ->restrictOnDelete();
        });

        // Rumah tidak dapat dihapus jika masih memiliki KK.
        Schema::table('kartu_keluargas', function (Blueprint $table) {
            $table->dropForeign(['rumah_id']);

            $table->foreign('rumah_id')
                ->references('id')
                ->on('rumahs')
                ->restrictOnDelete();
        });

        // KK tidak dapat dihapus jika masih memiliki anggota.
        Schema::table('anggota_keluargas', function (Blueprint $table) {
            $table->dropForeign(['kartu_keluarga_id']);

            $table->foreign('kartu_keluarga_id')
                ->references('id')
                ->on('kartu_keluargas')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        // Mengembalikan aturan lama jika migration di-rollback.
        Schema::table('rumahs', function (Blueprint $table) {
            $table->dropForeign(['blok_id']);

            $table->foreign('blok_id')
                ->references('id')
                ->on('bloks')
                ->cascadeOnDelete();
        });

        Schema::table('kartu_keluargas', function (Blueprint $table) {
            $table->dropForeign(['rumah_id']);

            $table->foreign('rumah_id')
                ->references('id')
                ->on('rumahs')
                ->cascadeOnDelete();
        });

        Schema::table('anggota_keluargas', function (Blueprint $table) {
            $table->dropForeign(['kartu_keluarga_id']);

            $table->foreign('kartu_keluarga_id')
                ->references('id')
                ->on('kartu_keluargas')
                ->cascadeOnDelete();
        });
    }
};
