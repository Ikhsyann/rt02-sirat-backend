<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Keuangan extends Model
{
    protected $fillable = [
        'tanggal', 'jenis', 'kategori', 'nama_barang', 'jumlah',
        'harga_satuan', 'total', 'saldo', 'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    protected static function booted(): void
    {
        // total selalu turunan jumlah × harga; saldo hanya nilai sementara di sini,
        // rantainya dirapikan setelah simpan/hapus lewat recomputeSaldo().
        static::saving(function (self $row) {
            $row->total = round($row->jumlah * $row->harga_satuan, 2);
            $row->saldo ??= 0;
        });

        static::saved(fn (self $row) => self::recomputeSaldo($row->tanggal, $row->id));
        static::deleted(fn (self $row) => self::recomputeSaldo($row->tanggal, $row->id));
    }

    /**
     * Hitung ulang saldo berjalan mulai (tanggal, id) ke depan.
     * Baris sebelum titik itu dianggap sudah benar, jadi edit/hapus baris lama
     * tidak membuat warga melihat saldo yang melenceng. Update dilakukan via
     * query builder agar memicu event ini lagi.
     */
    public static function recomputeSaldo(Carbon $dari, int $idBaris): void
    {
        $tanggalKunci = $dari->toDateString();

        $saldo = (float) (self::query()
            ->where(fn ($q) => $q
                ->where('tanggal', '<', $tanggalKunci)
                ->orWhere(fn ($w) => $w->where('tanggal', $tanggalKunci)->where('id', '<', $idBaris)))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->value('saldo') ?? 0);

        self::query()
            ->where(fn ($q) => $q
                ->where('tanggal', '>', $tanggalKunci)
                ->orWhere(fn ($w) => $w->where('tanggal', $tanggalKunci)->where('id', '>=', $idBaris)))
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get()
            ->each(function (self $baris) use (&$saldo) {
                $saldo = round($saldo + ($baris->jenis === 'income' ? $baris->total : -$baris->total), 2);

                // Query builder langsung: tidak memicu event saved berulang.
                DB::table('keuangans')->where('id', $baris->id)->update(['saldo' => $saldo]);
            });
    }
}
