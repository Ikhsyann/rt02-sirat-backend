<?php

namespace Tests\Feature;

use App\Models\Keuangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeuanganSaldoTest extends TestCase
{
    use RefreshDatabase;

    private function baris(string $tanggal, string $jenis, int $jumlah, float $harga): Keuangan
    {
        return Keuangan::create([
            'tanggal' => $tanggal,
            'jenis' => $jenis,
            'kategori' => 'Iuran',
            'nama_barang' => 'Data '.$tanggal,
            'jumlah' => $jumlah,
            'harga_satuan' => $harga,
        ]);
    }

    private function saldo(Keuangan $row): float
    {
        return (float) $row->fresh()->saldo;
    }

    public function test_total_dan_saldo_berantai_saat_create(): void
    {
        $a = $this->baris('2026-11-01', 'income', 1, 10000);
        $b = $this->baris('2026-11-02', 'expense', 2, 2000);
        $c = $this->baris('2026-11-03', 'income', 1, 1000);

        $this->assertSame(10000.0, (float) $a->fresh()->total);
        $this->assertSame([10000.0, 6000.0, 7000.0], [$this->saldo($a), $this->saldo($b), $this->saldo($c)]);
    }

    public function test_edit_baris_menyesuaikan_saldo_baris_selanjutnya(): void
    {
        $a = $this->baris('2026-11-01', 'income', 1, 10000);
        $b = $this->baris('2026-11-02', 'expense', 2, 2000);
        $c = $this->baris('2026-11-03', 'income', 1, 1000);

        $b->update(['jumlah' => 5]);

        $this->assertSame(10000.0, (float) $b->fresh()->total);
        $this->assertSame([10000.0, 0.0, 1000.0], [$this->saldo($a), $this->saldo($b), $this->saldo($c)]);
    }

    public function test_hapus_baris_awal_membuat_rantai_tetap_konsisten(): void
    {
        $a = $this->baris('2026-11-01', 'income', 1, 10000);
        $b = $this->baris('2026-11-02', 'expense', 2, 2000);
        $c = $this->baris('2026-11-03', 'income', 1, 1000);

        $a->delete();

        $this->assertSame([-4000.0, -3000.0], [$this->saldo($b), $this->saldo($c)]);
    }
}
