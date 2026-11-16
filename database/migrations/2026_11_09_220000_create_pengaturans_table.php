<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // satu baris saja (id=1): pengaturan tampilan global
        Schema::create('pengaturans', function (Blueprint $table) {
            $table->id();
            $table->string('hero_media')->nullable();   // gambar atau video hero
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturans');
    }
};
