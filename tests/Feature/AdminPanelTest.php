<?php

namespace Tests\Feature;

use App\Models\AnggotaKeluarga;
use App\Models\Blok;
use App\Models\KartuKeluarga;
use App\Models\Rumah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dapat_mengakses_panel(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }

    public function test_warga_ditolak_di_panel(): void
    {
        $user = User::factory()->create(['role' => 'warga']);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_halaman_semua_resource_dirender_tanpa_error(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        foreach ([
            'bloks', 'rumahs', 'kartu-keluargas', 'anggota-keluargas',
            'keuangans', 'unit-darurats', 'nomor-darurats', 'kegiatans',
        ] as $resource) {
            $this->get("/admin/{$resource}")->assertOk();
            $this->get("/admin/{$resource}/create")->assertOk();
        }
    }

    public function test_edit_anggota_dengan_akun_warga_tidak_error(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $warga = User::factory()->create(['role' => 'warga']);

        $kk = KartuKeluarga::create([
            'rumah_id' => Rumah::create([
                'blok_id' => Blok::create(['nama_blok' => 'ZZ'])->id,
                'nomor_rumah' => '99',
            ])->id,
            'nomor_kk' => '6471010101259999',
        ]);
        $anggota = AnggotaKeluarga::create([
            'kartu_keluarga_id' => $kk->id,
            'user_id' => $warga->id,
            'nama' => 'Warga Tertaut',
            'nik' => '6471010199999999',
        ]);

        $this->actingAs($admin)
            ->get("/admin/anggota-keluargas/{$anggota->id}/edit")
            ->assertOk()
            ->assertSee($warga->email);
    }
}
