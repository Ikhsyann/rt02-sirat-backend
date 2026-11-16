<?php

namespace App\Services;

use App\Models\Keuangan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class KeuanganService
{
    /**
     * Batas nilai kolom DECIMAL(15, 2), dalam satuan sen.
     */
    private const MAX_AMOUNT_CENTS = 999_999_999_999_999;

    private const MAX_QUANTITY = 2_147_483_647;

    /**
     * Membuat transaksi baru.
     */
    public function create(array $data): Keuangan
    {
        return DB::transaction(function () use ($data): Keuangan {
            $openingBalance = $this->lockLedger();

            $attributes = $this->prepareAttributes($data);

            [$quantity, $unitPrice, $total] =
                $this->calculateTransaction($data);

            $record = Keuangan::query()->create([
                ...$attributes,
                'jumlah' => $quantity,
                'harga_satuan' => $this->formatCents($unitPrice),
                'total' => $this->formatCents($total),
                'saldo' => '0.00',
            ]);

            $this->recalculateBalances($openingBalance);

            return $record->refresh();
        }, 5);
    }

    /**
     * Memperbarui transaksi.
     */
    public function update(Keuangan $keuangan, array $data): Keuangan
    {
        return DB::transaction(function () use ($keuangan, $data): Keuangan {
            $openingBalance = $this->lockLedger();

            $record = Keuangan::query()
                ->lockForUpdate()
                ->findOrFail($keuangan->getKey());

            $attributes = $this->prepareAttributes($data);

            [$quantity, $unitPrice, $total] =
                $this->calculateTransaction($data);

            $record->fill([
                ...$attributes,
                'jumlah' => $quantity,
                'harga_satuan' => $this->formatCents($unitPrice),
                'total' => $this->formatCents($total),
            ]);

            $record->save();

            $this->recalculateBalances($openingBalance);

            return $record->refresh();
        }, 5);
    }

    /**
     * Menghapus transaksi.
     */
    public function delete(Keuangan $keuangan): void
    {
        DB::transaction(function () use ($keuangan): void {
            $openingBalance = $this->lockLedger();

            $record = Keuangan::query()
                ->lockForUpdate()
                ->findOrFail($keuangan->getKey());

            $record->delete();

            $this->recalculateBalances($openingBalance);
        }, 5);
    }

    /**
     * Mengunci baris pengaturan agar perubahan ledger
     * berjalan secara berurutan.
     */
    private function lockLedger(): int
    {
        $setting = DB::table('keuangan_settings')
            ->where('id', 1)
            ->lockForUpdate()
            ->first();

        if (! $setting) {
            throw new RuntimeException(
                'Pengaturan saldo awal Keuangan tidak ditemukan.'
            );
        }

        return $this->decimalToCents(
            $setting->saldo_awal,
            'saldo_awal',
            allowNegative: true,
        );
    }

    /**
     * Memvalidasi atribut transaksi yang boleh disimpan.
     * Total dan saldo tidak diterima dari input pengguna.
     */
    private function prepareAttributes(array $data): array
    {
        $jenis = $data['jenis'] ?? null;

        if (! in_array($jenis, ['income', 'expense'], true)) {
            throw ValidationException::withMessages([
                'jenis' => 'Jenis transaksi tidak valid.',
            ]);
        }

        $tanggal = $data['tanggal'] ?? null;
        $kategori = trim((string) ($data['kategori'] ?? ''));
        $namaBarang = trim((string) ($data['nama_barang'] ?? ''));

        if (blank($tanggal)) {
            throw ValidationException::withMessages([
                'tanggal' => 'Tanggal transaksi wajib diisi.',
            ]);
        }

        if ($kategori === '') {
            throw ValidationException::withMessages([
                'kategori' => 'Kategori wajib diisi.',
            ]);
        }

        if ($namaBarang === '') {
            throw ValidationException::withMessages([
                'nama_barang' => 'Nama barang atau keperluan wajib diisi.',
            ]);
        }

        $keterangan = trim((string) ($data['keterangan'] ?? ''));

        return [
            'tanggal' => $tanggal,
            'jenis' => $jenis,
            'kategori' => $kategori,
            'nama_barang' => $namaBarang,
            'keterangan' => $keterangan !== '' ? $keterangan : null,
        ];
    }

    /**
     * Menghitung jumlah, harga satuan, dan total transaksi.
     */
    private function calculateTransaction(array $data): array
    {
        $quantity = $this->parseQuantity($data['jumlah'] ?? null);

        $unitPrice = $this->decimalToCents(
            $data['harga_satuan'] ?? null,
            'harga_satuan',
        );

        if ($unitPrice <= 0) {
            throw ValidationException::withMessages([
                'harga_satuan' => 'Harga satuan harus lebih besar dari nol.',
            ]);
        }

        if (
            $unitPrice >
            intdiv(self::MAX_AMOUNT_CENTS, $quantity)
        ) {
            throw ValidationException::withMessages([
                'harga_satuan' => 'Total transaksi melebihi batas nominal database.',
            ]);
        }

        $total = $quantity * $unitPrice;

        return [$quantity, $unitPrice, $total];
    }

    /**
     * Memastikan jumlah berupa bilangan bulat positif.
     */
    private function parseQuantity(mixed $value): int
    {
        $raw = trim((string) ($value ?? ''));

        if (! preg_match('/^\d+$/D', $raw)) {
            throw ValidationException::withMessages([
                'jumlah' => 'Jumlah harus berupa bilangan bulat.',
            ]);
        }

        $quantity = filter_var($raw, FILTER_VALIDATE_INT);

        if (
            $quantity === false ||
            $quantity < 1 ||
            $quantity > self::MAX_QUANTITY
        ) {
            throw ValidationException::withMessages([
                'jumlah' => 'Jumlah harus berada dalam batas yang valid.',
            ]);
        }

        return $quantity;
    }

    /**
     * Mengubah angka desimal menjadi satuan sen.
     * Perhitungan menggunakan integer agar menghindari
     * kesalahan presisi floating point.
     */
    private function decimalToCents(
        mixed $value,
        string $field,
        bool $allowNegative = false,
    ): int {
        $raw = trim((string) ($value ?? ''));

        $pattern = $allowNegative
            ? '/^-?\d{1,13}(?:\.\d{1,2})?$/D'
            : '/^\d{1,13}(?:\.\d{1,2})?$/D';

        if (! preg_match($pattern, $raw)) {
            throw ValidationException::withMessages([
                $field => 'Nominal harus berupa angka dengan maksimal dua desimal.',
            ]);
        }

        $negative = str_starts_with($raw, '-');

        $raw = $negative ? substr($raw, 1) : $raw;

        [$whole, $fraction] = array_pad(
            explode('.', $raw, 2),
            2,
            ''
        );

        $whole = ltrim($whole, '0') ?: '0';

        $fraction = str_pad($fraction, 2, '0');

        $cents = ((int) $whole * 100) + (int) $fraction;

        if ($cents > self::MAX_AMOUNT_CENTS) {
            throw ValidationException::withMessages([
                $field => 'Nominal melebihi batas maksimum database.',
            ]);
        }

        return $negative ? -$cents : $cents;
    }

    /**
     * Mengubah satuan sen menjadi angka desimal.
     */
    private function formatCents(int $cents): string
    {
        $negative = $cents < 0;
        $absolute = abs($cents);

        $formatted = intdiv($absolute, 100)
            . '.'
            . str_pad(
                (string) ($absolute % 100),
                2,
                '0',
                STR_PAD_LEFT
            );

        return $negative ? '-' . $formatted : $formatted;
    }

    /**
     * Menghitung ulang seluruh saldo berdasarkan tanggal
     * dan ID transaksi.
     *
     * Saldo awal menjadi titik awal perhitungan.
     */
    private function recalculateBalances(int $openingBalance): void
    {
        $balance = $openingBalance;

        $transactions = DB::table('keuangans')
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get([
                'id',
                'jenis',
                'jumlah',
                'harga_satuan',
            ]);

        foreach ($transactions as $transaction) {
            $quantity = $this->parseQuantity(
                $transaction->jumlah
            );

            $unitPrice = $this->decimalToCents(
                $transaction->harga_satuan,
                'harga_satuan',
            );

            $total = $quantity * $unitPrice;

            if ($transaction->jenis === 'income') {
                $balance += $total;
            } elseif ($transaction->jenis === 'expense') {
                $balance -= $total;
            } else {
                throw new RuntimeException(
                    "Jenis transaksi ID {$transaction->id} tidak valid."
                );
            }

            if (
                $balance > self::MAX_AMOUNT_CENTS ||
                $balance < -self::MAX_AMOUNT_CENTS
            ) {
                throw new RuntimeException(
                    'Saldo melebihi batas nominal database.'
                );
            }

            DB::table('keuangans')
                ->where('id', $transaction->id)
                ->update([
                    'total' => $this->formatCents($total),
                    'saldo' => $this->formatCents($balance),
                ]);
        }
    }
}