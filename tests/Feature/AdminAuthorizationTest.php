<?php

namespace Tests\Feature;

use App\Exports\KeuanganExport;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();

        $user->forceFill([
            'role' => $role,
        ])->save();

        return $user;
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_filament_dashboard(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk();
    }

    public function test_warga_cannot_access_filament_dashboard(): void
    {
        $warga = $this->createUserWithRole('warga');

        $this->actingAs($warga)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_guest_cannot_download_keuangan_export(): void
    {
        $this->get(route('admin.keuangan.export'))
            ->assertRedirect(route('login'));
    }

    public function test_warga_cannot_download_keuangan_export(): void
    {
        $warga = $this->createUserWithRole('warga');

        $this->actingAs($warga)
            ->get(route('admin.keuangan.export'))
            ->assertForbidden();
    }

    public function test_admin_can_download_keuangan_export(): void
    {
        $admin = $this->createUserWithRole('admin');

        Excel::fake();

        $response = $this->actingAs($admin)
            ->get(route('admin.keuangan.export'));

        $response->assertOk();

        Excel::assertDownloaded(
            'Laporan-Keuangan-RT-02.xlsx',
            fn (KeuanganExport $export): bool =>
                $export instanceof KeuanganExport
        );
    }

    public function test_admin_role_is_required_for_export_access(): void
    {
        $panel = Filament::getPanel('admin');

        $warga = $this->createUserWithRole('warga');
        $admin = $this->createUserWithRole('admin');

        $this->assertFalse($warga->canAccessPanel($panel));
        $this->assertTrue($admin->canAccessPanel($panel));
    }
}