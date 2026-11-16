<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keuangan extends Model
{
    protected $fillable = [
        'tanggal',
        'jenis',
        'kategori',
        'nama_barang',
        'jumlah',
        'harga_satuan',
        'total',
        'saldo',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'integer',
        'harga_satuan' => 'decimal:2',
        'total' => 'decimal:2',
        'saldo' => 'decimal:2',
    ];
}