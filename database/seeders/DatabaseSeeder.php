<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\AnggotaKeluarga;
use App\Models\Blok;
use App\Models\KartuKeluarga;
use App\Models\Kegiatan;
use App\Models\Keuangan;
use App\Models\NomorDarurat;
use App\Models\Rumah;
use App\Models\UnitDarurat;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Akun ----------
        // role sengaja di luar $fillable; admin harus ditetapkan eksplisit.
        User::forceCreate([
            'name' => 'Admin RT 02',
            'email' => 'admin@rt02.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $wargaDemo = User::create([
            'name' => 'Budi Santoso',
            'email' => 'warga@rt02.test',
            'password' => bcrypt('password'),
        ]);

        // ---------- Blok & Rumah ----------
        $blokA = Blok::create(['nama_blok' => 'A']);
        $blokB = Blok::create(['nama_blok' => 'B']);

        $rumahA1 = Rumah::create(['blok_id' => $blokA->id, 'nomor_rumah' => 'A-1']);
        $rumahA2 = Rumah::create(['blok_id' => $blokA->id, 'nomor_rumah' => 'A-2']);
        $rumahB1 = Rumah::create(['blok_id' => $blokB->id, 'nomor_rumah' => 'B-3']);
        $rumahB2 = Rumah::create(['blok_id' => $blokB->id, 'nomor_rumah' => 'B-9']);
        $rumahB3 = Rumah::create(['blok_id' => $blokB->id, 'nomor_rumah' => 'B-12']);

        // ---------- Kartu Keluarga ----------
        $kk1 = KartuKeluarga::create(['rumah_id' => $rumahA1->id, 'nomor_kk' => '6471010101250001']);
        $kk2 = KartuKeluarga::create(['rumah_id' => $rumahA2->id, 'nomor_kk' => '6471010101250002']);
        $kk3 = KartuKeluarga::create(['rumah_id' => $rumahB1->id, 'nomor_kk' => '6471010101250003']);
        $kk4 = KartuKeluarga::create(['rumah_id' => $rumahB2->id, 'nomor_kk' => '6471010101250004']);
        $kk5 = KartuKeluarga::create(['rumah_id' => $rumahB3->id, 'nomor_kk' => '6471010101250005']);

        // ---------- Anggota Keluarga (hanya 1 yang bertaut akun demo) ----------
        $budi = AnggotaKeluarga::create([
            'kartu_keluarga_id' => $kk1->id,
            'user_id' => $wargaDemo->id,
            'nama' => 'Budi Santoso',
            'nik' => '6471010101800001',
            'tempat_lahir' => 'Balikpapan',
            'tanggal_lahir' => '1990-05-10',
            'jenis_kelamin' => 'Laki-laki',
            'pekerjaan' => 'Wiraswasta',
            'golongan_darah' => 'O',
            'status_perkawinan' => 'Kawin',
            'status_hubungan_keluarga' => 'Kepala Keluarga',
        ]);
        AnggotaKeluarga::create([
            'kartu_keluarga_id' => $kk1->id,
            'nama' => 'Sari Wulandari',
            'nik' => '6471010101930002',
            'tempat_lahir' => 'Balikpapan',
            'tanggal_lahir' => '1993-02-14',
            'jenis_kelamin' => 'Perempuan',
            'pekerjaan' => 'Guru',
            'golongan_darah' => 'A',
            'status_perkawinan' => 'Kawin',
            'status_hubungan_keluarga' => 'Pasangan',
        ]);
        $andri = AnggotaKeluarga::create([
            'kartu_keluarga_id' => $kk2->id,
            'nama' => 'Andri Pratama',
            'nik' => '6471010101880001',
            'tempat_lahir' => 'Samarinda',
            'tanggal_lahir' => '1988-11-21',
            'jenis_kelamin' => 'Laki-laki',
            'pekerjaan' => 'Karyawan Swasta',
            'golongan_darah' => 'B',
            'status_perkawinan' => 'Kawin',
            'status_hubungan_keluarga' => 'Kepala Keluarga',
        ]);
        AnggotaKeluarga::create([
            'kartu_keluarga_id' => $kk2->id,
            'nama' => 'Anak Pratama',
            'nik' => '6471010101150002',
            'tempat_lahir' => 'Balikpapan',
            'tanggal_lahir' => '2015-07-30',
            'jenis_kelamin' => 'Laki-laki',
            'pekerjaan' => 'Pelajar',
            'golongan_darah' => 'O',
            'status_perkawinan' => 'Belum Kawin',
            'status_hubungan_keluarga' => 'Anak',
        ]);
        $ikhwan = AnggotaKeluarga::create([
            'kartu_keluarga_id' => $kk3->id,
            'nama' => 'Ikhwan Hakim',
            'nik' => '6471010101920001',
            'tempat_lahir' => 'Balikpapan',
            'tanggal_lahir' => '1992-04-08',
            'jenis_kelamin' => 'Laki-laki',
            'pekerjaan' => 'Perawat',
            'golongan_darah' => 'AB',
            'status_perkawinan' => 'Belum Kawin',
            'status_hubungan_keluarga' => 'Kepala Keluarga',
        ]);
        $nuria = AnggotaKeluarga::create([
            'kartu_keluarga_id' => $kk4->id,
            'nama' => 'Nur Aisyah',
            'nik' => '6471010101950001',
            'tempat_lahir' => 'Balikpapan',
            'tanggal_lahir' => '1995-09-12',
            'jenis_kelamin' => 'Perempuan',
            'pekerjaan' => 'Wirausaha',
            'golongan_darah' => 'O',
            'status_perkawinan' => 'Belum Kawin',
            'status_hubungan_keluarga' => 'Kepala Keluarga',
        ]);
        $pakRahmat = AnggotaKeluarga::create([
            'kartu_keluarga_id' => $kk5->id,
            'nama' => 'Rahmat Hidayat',
            'nik' => '6471010101730001',
            'tempat_lahir' => 'Balikpapan',
            'tanggal_lahir' => '1973-01-05',
            'jenis_kelamin' => 'Laki-laki',
            'pekerjaan' => 'Pensiunan',
            'golongan_darah' => 'A',
            'status_perkawinan' => 'Cerai Hidup',
            'status_hubungan_keluarga' => 'Kepala Keluarga',
        ]);

        // ---------- Keuangan (6 transaksi, saldo berjalan) ----------
        Keuangan::create(['tanggal' => now()->subMonths(2)->startOfMonth(), 'jenis' => 'income', 'kategori' => 'Iuran', 'nama_barang' => 'Iuran Bulanan Warga', 'jumlah' => 40, 'harga_satuan' => 25000, 'total' => 1000000, 'saldo' => 1000000]);
        Keuangan::create(['tanggal' => now()->subMonths(2)->addDays(2), 'jenis' => 'expense', 'kategori' => 'Kebersihan', 'nama_barang' => 'Upah Petugas Sampah', 'jumlah' => 2, 'harga_satuan' => 150000, 'total' => 300000, 'saldo' => 700000]);
        Keuangan::create(['tanggal' => now()->subMonths(1)->startOfMonth(), 'jenis' => 'income', 'kategori' => 'Iuran', 'nama_barang' => 'Iuran Bulanan Warga', 'jumlah' => 40, 'harga_satuan' => 25000, 'total' => 1000000, 'saldo' => 1700000]);
        Keuangan::create(['tanggal' => now()->subMonths(1)->addDays(4), 'jenis' => 'expense', 'kategori' => 'Keamanan', 'nama_barang' => 'Peralatan Pos Ronda', 'jumlah' => 5, 'harga_satuan' => 80000, 'total' => 400000, 'saldo' => 1300000]);
        Keuangan::create(['tanggal' => now()->startOfMonth(), 'jenis' => 'income', 'kategori' => 'Donasi', 'nama_barang' => 'Donasi Kegiatan Sosial', 'jumlah' => 1, 'harga_satuan' => 500000, 'total' => 500000, 'saldo' => 1800000]);
        Keuangan::create(['tanggal' => now()->subDays(2), 'jenis' => 'expense', 'kategori' => 'Kegiatan', 'nama_barang' => 'Perlengkapan Kerja Bakti', 'jumlah' => 8, 'harga_satuan' => 25000, 'total' => 200000, 'saldo' => 1600000]);

        // ---------- Unit & Nomor Darurat ----------
        $unitPemadam = UnitDarurat::create(['nama_unit' => 'Pemadam Kebakaran']);
        NomorDarurat::create(['unit_darurat_id' => $unitPemadam->id, 'lokasi' => 'Balikpapan Timur', 'nomor_telepon' => '0542-7510101']);
        $unitRS = UnitDarurat::create(['nama_unit' => 'Rumah Sakit']);
        NomorDarurat::create(['unit_darurat_id' => $unitRS->id, 'lokasi' => 'Balikpapan Timur', 'nomor_telepon' => '0542-7531313']);
        $unitPolisi = UnitDarurat::create(['nama_unit' => 'Kepolisian']);
        NomorDarurat::create(['unit_darurat_id' => $unitPolisi->id, 'lokasi' => 'Balikpapan', 'nomor_telepon' => '110']);
        $unitAmbulans = UnitDarurat::create(['nama_unit' => 'Ambulans']);
        NomorDarurat::create(['unit_darurat_id' => $unitAmbulans->id, 'lokasi' => 'Balikpapan', 'nomor_telepon' => '119']);

        // ---------- Kegiatan & Absensi ----------
        $kegiatan1 = Kegiatan::create(['nama_kegiatan' => 'Kerja Bakti Mingguan', 'slug' => Str::slug('Kerja Bakti Mingguan'), 'tanggal' => now()->subWeek(), 'deskripsi' => 'Bersih lingkungan dan saluran air RT 02.']);
        $kegiatan2 = Kegiatan::create(['nama_kegiatan' => 'Peringatan Hari Kemerdekaan', 'slug' => Str::slug('Peringatan Hari Kemerdekaan 17an'), 'tanggal' => now()->subMonth(), 'deskripsi' => 'Lomba-lomba menyambut hari kemerdekaan untuk warga RT 02.']);
        $kegiatan3 = Kegiatan::create(['nama_kegiatan' => 'Rapat Warga Rutin', 'slug' => Str::slug('Rapat Warga Rutin'), 'tanggal' => now()->subDays(3), 'deskripsi' => 'Rapat bulanan pengurus membahas perkembangan warga.']);

        foreach ([
            [$kegiatan1, $budi, 'hadir'], [$kegiatan1, $andri, 'hadir'], [$kegiatan1, $ikhwan, 'izin'],
            [$kegiatan2, $budi, 'hadir'], [$kegiatan2, $nuria, 'hadir'], [$kegiatan2, $pakRahmat, 'alpa'],
            [$kegiatan3, $budi, 'hadir'], [$kegiatan3, $andri, 'sakit'],
        ] as [$k, $a, $s]) {
            Absensi::create(['kegiatan_id' => $k->id, 'anggota_keluarga_id' => $a->id, 'status_hadir' => $s]);
        }
    }
}
