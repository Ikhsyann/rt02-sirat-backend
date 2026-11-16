<?php

namespace Tests\Feature;

use App\Models\Keuangan;
use App\Services\KeuanganService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class KeuanganServiceTest extends TestCase
{
    use RefreshDatabase;

    private function transactionData(
        string $tanggal,
        string $jenis,
        int $jumlah,
        string $harga
    ): array {
        return [
            'tanggal' => $tanggal,
            'jenis' => $jenis,
            'kategori' => 'Pengujian',
            'nama_barang' => 'Transaksi Test',
            'jumlah' => $jumlah,
            'harga_satuan' => $harga,
            'keterangan' => 'Data pengujian otomatis',
        ];
    }

    public function test_create_calculates_total_and_running_balance(): void
    {
        $service = app(KeuanganService::class);

        $income = $service->create(
            $this->transactionData(
                '2026-11-01',
                'income',
                2,
                '50000.00'
            )
        );

        $this->assertSame('100000.00', $income->total);
        $this->assertSame('100000.00', $income->saldo);

        $expense = $service->create(
            $this->transactionData(
                '2026-11-02',
                'expense',
                1,
                '25000.00'
            )
        );

        $this->assertSame('25000.00', $expense->total);
        $this->assertSame('75000.00', $expense->saldo);
    }

    public function test_backdated_transaction_updates_following_balances(): void
    {
        $service = app(KeuanganService::class);

        $firstIncome = $service->create(
            $this->transactionData(
                '2026-11-03',
                'income',
                1,
                '100000.00'
            )
        );

        $expense = $service->create(
            $this->transactionData(
                '2026-11-05',
                'expense',
                1,
                '30000.00'
            )
        );

        $backdatedIncome = $service->create(
            $this->transactionData(
                '2026-11-04',
                'income',
                1,
                '50000.00'
            )
        );

        $this->assertSame(
            '100000.00',
            $firstIncome->fresh()->saldo
        );

        $this->assertSame(
            '150000.00',
            $backdatedIncome->fresh()->saldo
        );

        $this->assertSame(
            '120000.00',
            $expense->fresh()->saldo
        );
    }

    public function test_update_recalculates_following_balances(): void
    {
        $service = app(KeuanganService::class);

        $service->create(
            $this->transactionData(
                '2026-11-03',
                'income',
                1,
                '100000.00'
            )
        );

        $expense = $service->create(
            $this->transactionData(
                '2026-11-05',
                'expense',
                1,
                '30000.00'
            )
        );

        $updated = $service->update(
            $expense,
            $this->transactionData(
                '2026-11-05',
                'expense',
                2,
                '20000.00'
            )
        );

        $this->assertSame('40000.00', $updated->total);
        $this->assertSame('60000.00', $updated->saldo);
    }

    public function test_delete_recalculates_following_balances(): void
    {
        $service = app(KeuanganService::class);

        $income = $service->create(
            $this->transactionData(
                '2026-11-03',
                'income',
                1,
                '100000.00'
            )
        );

        $expense = $service->create(
            $this->transactionData(
                '2026-11-05',
                'expense',
                1,
                '30000.00'
            )
        );

        $service->delete($income);

        $this->assertDatabaseMissing('keuangans', [
            'id' => $income->id,
        ]);

        $this->assertSame(' -30000.00', ' ' . $expense->fresh()->saldo);
    }

    public function test_invalid_quantity_is_rejected(): void
    {
        $this->expectException(ValidationException::class);

        $data = array_merge(
            $this->transactionData(
                '2026-11-03',
                'income',
                1,
                '100000.00'
            ),
            ['jumlah' => '1.5']
        );

        app(KeuanganService::class)->create($data);
    }
}