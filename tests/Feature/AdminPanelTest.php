<?php

namespace Tests\Feature;

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
}
