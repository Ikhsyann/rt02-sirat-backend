<?php

namespace Tests\Feature;

use App\Filament\Resources\Wargas\Pages\CreateWarga;
use App\Filament\Resources\Wargas\Pages\EditWarga;
use App\Filament\Resources\Wargas\WargaResource;
use App\Models\AnggotaKeluarga;
use App\Models\Blok;
use App\Models\KartuKeluarga;
use App\Models\Rumah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AkunWargaTest extends TestCase
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

    public function test_admin_dan_superadmin_dapat_membuka_akun_warga(): void
    {
        $admin = $this->createUserWithRole('admin');
        $superadmin = $this->createUserWithRole('superadmin');

        foreach ([$admin, $superadmin] as $actor) {
            $this->actingAs($actor)
                ->get('/admin/akun-warga')
                ->assertOk();

            $this->actingAs($actor)
                ->get('/admin/akun-warga/create')
                ->assertOk();
        }
    }

    public function test_admin_dapat_membuat_akun_warga_dan_role_dipaksa_warga(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin);

        Livewire::test(CreateWarga::class)
            ->fillForm([
                'name' => 'Warga Baru',
                'email' => 'warga-baru@rt02.test',
                // Penyusupan role harus diabaikan oleh server.
                'role' => 'superadmin',
                'password' => 'Password123',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'name' => 'Warga Baru',
            'email' => 'warga-baru@rt02.test',
            'role' => 'warga',
        ]);
    }

    public function test_email_duplikat_ditolak(): void
    {
        $admin = $this->createUserWithRole('admin');
        $warga = $this->createUserWithRole('warga');

        $this->actingAs($admin);

        Livewire::test(CreateWarga::class)
            ->fillForm([
                'name' => 'Duplikat',
                'email' => $warga->email,
                'password' => 'Password123',
            ])
            ->call('create')
            ->assertHasFormErrors(['email']);
    }

    public function test_password_kosong_saat_edit_tidak_mengubah_hash(): void
    {
        $admin = $this->createUserWithRole('admin');
        $warga = $this->createUserWithRole('warga');
        $hashLama = $warga->password;

        $this->actingAs($admin);

        Livewire::test(EditWarga::class, [
            'record' => $warga->getRouteKey(),
        ])
            ->fillForm([
                'name' => 'Warga Diperbarui',
                'password' => '',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $warga->refresh();

        $this->assertSame('Warga Diperbarui', $warga->name);
        $this->assertSame($hashLama, $warga->password);
        $this->assertTrue(Hash::check('password', $warga->password));
    }

    public function test_admin_dapat_mengubah_email_warga(): void
    {
        $admin = $this->createUserWithRole('admin');
        $warga = $this->createUserWithRole('warga');

        $this->actingAs($admin);

        Livewire::test(EditWarga::class, [
            'record' => $warga->getRouteKey(),
        ])
            ->fillForm([
                'email' => 'email-warga-baru@rt02.test',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(
            'email-warga-baru@rt02.test',
            $warga->fresh()->email
        );
    }

    public function test_admin_tidak_dapat_mengedit_akun_admin(): void
    {
        $admin = $this->createUserWithRole('admin');
        $targetAdmin = $this->createUserWithRole('admin');

        $this->actingAs($admin)
            ->get("/admin/users/{$targetAdmin->id}/edit")
            ->assertForbidden();
    }

    public function test_warga_ditolak_semua(): void
    {
        $warga = $this->createUserWithRole('warga');

        $this->actingAs($warga)
            ->get('/admin/akun-warga')
            ->assertForbidden();

        $this->actingAs($warga)
            ->get('/admin/users')
            ->assertForbidden();
    }

    public function test_daftar_hanya_memuat_akun_warga(): void
    {
        $this->createUserWithRole('admin');
        $warga = $this->createUserWithRole('warga');

        $emails = WargaResource::getEloquentQuery()
            ->pluck('email')
            ->all();

        $this->assertSame([$warga->email], $emails);
    }

    public function test_status_terhubung_ke_anggota_ditampilkan(): void
    {
        $blok = Blok::create(['nama_blok' => 'F1']);
        $rumah = Rumah::create([
            'blok_id' => $blok->id,
            'nomor_rumah' => '001',
        ]);
        $kk = KartuKeluarga::create([
            'rumah_id' => $rumah->id,
            'nomor_kk' => '3271010101010001',
        ]);

        $terhubung = $this->createUserWithRole('warga');
        AnggotaKeluarga::create([
            'kartu_keluarga_id' => $kk->id,
            'user_id' => $terhubung->id,
            'nama' => 'Budi',
            'nik' => '6471010101010002',
        ]);

        $belum = $this->createUserWithRole('warga');

        $this->assertTrue(
            WargaResource::getEloquentQuery()
                ->find($terhubung->id)
                ->anggotaKeluarga()
                ->exists()
        );
        $this->assertFalse(
            WargaResource::getEloquentQuery()
                ->find($belum->id)
                ->anggotaKeluarga()
                ->exists()
        );
    }
}
