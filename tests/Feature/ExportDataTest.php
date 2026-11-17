<?php

namespace Tests\Feature;

use App\Models\AnggotaKeluarga;
use App\Models\Blok;
use App\Models\KartuKeluarga;
use App\Models\Rumah;
use App\Models\User;
use App\Services\KeuanganService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Tests\TestCase;

class ExportDataTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();

        $user->forceFill(['role' => $role])->save();

        return $user;
    }

    private function seedPenduduk(): void
    {
        $blokA = Blok::create(['nama_blok' => 'A']);
        $blokB = Blok::create(['nama_blok' => 'B']);

        $rumahA = Rumah::create(['blok_id' => $blokA->id, 'nomor_rumah' => 'A-1']);
        $rumahB = Rumah::create(['blok_id' => $blokB->id, 'nomor_rumah' => 'B-1']);

        $kkA = KartuKeluarga::create(['rumah_id' => $rumahA->id, 'nomor_kk' => '6471010101010001']);
        $kkB = KartuKeluarga::create(['rumah_id' => $rumahB->id, 'nomor_kk' => '6471010101010002']);

        $akun = User::factory()->create();

        AnggotaKeluarga::create([
            'kartu_keluarga_id' => $kkA->id,
            'user_id' => $akun->id,
            'nama' => 'Budi Santoso',
            'nik' => '6471010101010003',
            'jenis_kelamin' => 'Laki-laki',
            'status_perkawinan' => 'Kawin',
            'status_hubungan_keluarga' => 'Kepala Keluarga',
        ]);

        AnggotaKeluarga::create([
            'kartu_keluarga_id' => $kkB->id,
            'user_id' => null,
            'nama' => 'Siti Rahma',
            'nik' => '6471010101010004',
            'jenis_kelamin' => 'Perempuan',
            'status_perkawinan' => 'Belum Kawin',
            'status_hubungan_keluarga' => 'Anak',
        ]);
    }

    private function seedKeuangan(): void
    {
        // Transaksi dibuat lewat service agar total & saldo berjalan
        // terhitung seperti di panel admin.
        $service = app(KeuanganService::class);

        $service->create([
            'tanggal' => '2026-01-10',
            'jenis' => 'income',
            'kategori' => 'Iuran',
            'nama_barang' => 'Iuran January',
            'jumlah' => 1,
            'harga_satuan' => '50000.00',
            'keterangan' => 'Data pengujian otomatis',
        ]);

        $service->create([
            'tanggal' => '2026-02-10',
            'jenis' => 'income',
            'kategori' => 'Iuran',
            'nama_barang' => 'Iuran February',
            'jumlah' => 1,
            'harga_satuan' => '30000.00',
            'keterangan' => 'Data pengujian otomatis',
        ]);

        $service->create([
            'tanggal' => '2026-03-10',
            'jenis' => 'expense',
            'kategori' => '=SUM(A1:A2)',
            'nama_barang' => 'Konsumsi Rapat',
            'jumlah' => 2,
            'harga_satuan' => '10000.00',
            'keterangan' => 'Data pengujian otomatis',
        ]);
    }

    private function loadSpreadsheet($response): Spreadsheet
    {
        // Unduhan xlsx adalah BinaryFileResponse; baca isi berkasnya.
        $path = tempnam(sys_get_temp_dir(), 'sirat-xlsx');
        file_put_contents(
            $path,
            (string) file_get_contents($response->baseResponse->getFile()->getRealPath()),
        );

        return IOFactory::load($path);
    }

    private function csvContent($response): string
    {
        // Unduhan CSV adalah StreamedResponse; ambil lewat streamedContent.
        return $response->streamedContent();
    }

    public function test_halaman_export_data_dirender_untuk_admin(): void
    {
        $this->actingAs($this->createUserWithRole('admin'))
            ->get('/admin/export-data')
            ->assertOk()
            ->assertSee('Jenis Data');
    }

    public function test_guest_dan_warga_ditolak(): void
    {
        $this->get(route('admin.export.data', ['jenis_data' => 'keuangan', 'format' => 'csv']))
            ->assertRedirect(route('login'));

        $this->actingAs($this->createUserWithRole('warga'))
            ->get(route('admin.export.data', ['jenis_data' => 'keuangan', 'format' => 'csv']))
            ->assertForbidden();
    }

    public function test_pilihan_validasi_di_luar_daftar_tidak_500(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.export.data', ['jenis_data' => 'keuangan', 'format' => 'docx']))
            ->assertStatus(302);

        $this->actingAs($admin)
            ->get(route('admin.export.data', ['jenis_data' => 'dokumen', 'format' => 'csv']))
            ->assertStatus(302);
    }

    public function test_admin_dapat_unduh_keuangan_xlsx_dengan_filter_jenis(): void
    {
        $this->seedKeuangan();
        $admin = $this->createUserWithRole('admin');

        $response = $this->actingAs($admin)
            ->get(route('admin.export.data', [
                'jenis_data' => 'keuangan',
                'format' => 'xlsx',
                'jenis_keuangan' => 'income',
            ]))
            ->assertOk();

        $this->assertStringContainsString(
            'Laporan-Keuangan-RT-02-'.now()->format('Y-m-d').'.xlsx',
            $response->headers->get('content-disposition'),
        );

        $spreadsheet = $this->loadSpreadsheet($response);
        $transaksi = $spreadsheet->getSheetByName('Transaksi');
        $ringkasan = $spreadsheet->getSheetByName('Ringkasan');

        // Hanya transaksi pemasukan yang masuk: 2 baris; pengeluaran tidak ada.
        $this->assertSame('Pemasukan', $transaksi->getCell('B2')->getValue());
        $this->assertSame('Pemasukan', $transaksi->getCell('B3')->getValue());
        $this->assertNull($transaksi->getCell('B4')->getValue());

        $this->assertSame('Total Pemasukan', $ringkasan->getCell('A5')->getValue());
        $this->assertSame('Total Pengeluaran', $ringkasan->getCell('A6')->getValue());
        $this->assertSame('Saldo Akhir', $ringkasan->getCell('A7')->getValue());
    }

    public function test_keuangan_csv_memakai_bom_dan_mengescape_formula(): void
    {
        $this->seedKeuangan();
        $admin = $this->createUserWithRole('admin');

        $content = $this->csvContent($this->actingAs($admin)
            ->get(route('admin.export.data', ['jenis_data' => 'keuangan', 'format' => 'csv']))
            ->assertOk());

        // BOM UTF-8 di awal berkas.
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);

        // Kategori berawalan "=" harus diberi apostrof.
        $this->assertStringContainsString("'=SUM(A1:A2)", $content);
        $this->assertStringNotContainsString("\n=SUM(A1:A2)", $content);

        // Semua transaksi ada (tanpa filter jenis).
        $this->assertStringContainsString('Iuran January', $content);
        $this->assertStringContainsString('Konsumsi Rapat', $content);
    }

    public function test_penduduk_csv_lengkap_dengan_filter_blok(): void
    {
        $this->seedPenduduk();
        $admin = $this->createUserWithRole('admin');
        $blokA = Blok::where('nama_blok', 'A')->first();

        $semua = $this->csvContent($this->actingAs($admin)
            ->get(route('admin.export.data', ['jenis_data' => 'penduduk', 'format' => 'csv']))
            ->assertOk());

        $this->assertStringStartsWith("\xEF\xBB\xBF", $semua);
        $this->assertStringContainsString('Budi Santoso', $semua);
        $this->assertStringContainsString('Siti Rahma', $semua);
        // NIK dan nomor KK utuh 16 digit, bukan hasil masking atau ilmiah.
        $this->assertStringContainsString('6471010101010003', $semua);
        $this->assertStringContainsString('6471010101010002', $semua);
        // Warga tanpa akun ditulis "-" pada kolom email (kolom terakhir);
        // "-" diawali tanda minus, jadi sesuai aturan mendapat apostrof.
        $this->assertStringContainsString("Anak,'-", $semua);
        // Pemilik akun tampil dengan emailnya.
        $this->assertStringContainsString('"Budi Santoso",6471010101010003', $semua);

        $filterBlok = $this->csvContent($this->actingAs($admin)
            ->get(route('admin.export.data', [
                'jenis_data' => 'penduduk',
                'format' => 'csv',
                'blok_id' => $blokA->id,
            ]))
            ->assertOk());

        $this->assertStringContainsString('Budi Santoso', $filterBlok);
        $this->assertStringNotContainsString('Siti Rahma', $filterBlok);
    }

    public function test_penduduk_xlsx_menyimpan_nik_sebagai_teks(): void
    {
        $this->seedPenduduk();
        $admin = $this->createUserWithRole('admin');

        $spreadsheet = $this->loadSpreadsheet(
            $this->actingAs($admin)
                ->get(route('admin.export.data', ['jenis_data' => 'penduduk', 'format' => 'xlsx']))
                ->assertOk()
        );

        // Satu sheet tunggal untuk penduduk.
        $sheet = $spreadsheet->getSheet(0);

        $this->assertSame('6471010101010003', $sheet->getCell('B2')->getValue());
        $this->assertSame('6471010101010001', $sheet->getCell('C2')->getValue());

        // Tipe sel NIK harus string, bukan angka.
        $this->assertSame(
            DataType::TYPE_STRING,
            $sheet->getCell('B2')->getDataType(),
        );
    }
}
