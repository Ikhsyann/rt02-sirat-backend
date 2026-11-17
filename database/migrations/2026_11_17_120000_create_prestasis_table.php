<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Guard idempoten: produksi menjalankan migrate --force pada DB yang
        // sudah berisi data; migrasi tidak boleh gagal saat dipanggil ulang.
        if (Schema::hasTable('prestasis')) {
            return;
        }

        Schema::create('prestasis', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);
            // String, bukan enum: enum sempat merepotkan di SQLite.
            $table->string('jenis', 20);
            $table->date('tanggal');
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestasis');
    }
};
