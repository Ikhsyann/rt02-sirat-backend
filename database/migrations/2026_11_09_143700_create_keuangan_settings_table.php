<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keuangan_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->timestamps();
        });

        // Satu baris pengaturan awal sekaligus lock untuk operasi ledger.
        DB::table('keuangan_settings')->insert([
            'id' => 1,
            'saldo_awal' => '0.00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('keuangan_settings');
    }
};
