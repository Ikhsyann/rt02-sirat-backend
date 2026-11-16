<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nomor_darurats', function (Blueprint $table) {
            $table->dropForeign(['unit_darurat_id']);

            $table->foreign('unit_darurat_id')
                ->references('id')
                ->on('unit_darurats')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('nomor_darurats', function (Blueprint $table) {
            $table->dropForeign(['unit_darurat_id']);

            $table->foreign('unit_darurat_id')
                ->references('id')
                ->on('unit_darurats')
                ->cascadeOnDelete();
        });
    }
};