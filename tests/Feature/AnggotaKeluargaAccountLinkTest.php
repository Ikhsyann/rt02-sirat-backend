<?php

namespace Tests\Feature;

use App\Models\AnggotaKeluarga;
use App\Models\Blok;
use App\Models\KartuKeluarga;
use App\Models\Rumah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AnggotaKeluargaAccountLinkTest extends TestCase
{
    use RefreshDatabase;

    private function createKartuKeluarga(): KartuKeluarga
    {
        $blok = Blok::create([
            'nama_blok' => 'F1',
        ]);

        $rumah = Rumah::create([
            'blok_id' => $blok->id,
            'nomor_rumah' => '001',
        ]);

        return KartuKeluarga::create([
            'rumah_id' => $rumah->id,
            'nomor_kk' => '3271010101010001',
        ]);
    }

    private function createAnggota(
        KartuKeluarga $kk,
        ?int $userId,
        string $nik
    ): AnggotaKeluarga {
        return AnggotaKeluarga::create([
            'kartu_keluarga_id' => $kk->id,
            'user_id' => $userId,
            'nama' => 'Test Anggota',
            'nik' => $nik,
        ]);
    }

    public function test_warga_account_can_be_linked_to_member(): void
    {
        $kk = $this->createKartuKeluarga();
        $warga = User::factory()->create();

        $anggota = $this->createAnggota(
            $kk,
            $warga->id,
            '6471010101010001'
        );

        $this->assertSame($warga->id, $anggota->user_id);
    }

    public function test_admin_account_cannot_be_linked_to_member(): void
    {
        $kk = $this->createKartuKeluarga();

        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $this->expectException(ValidationException::class);

        $this->createAnggota(
            $kk,
            $admin->id,
            '6471010101010002'
        );
    }

    public function test_same_warga_account_cannot_be_linked_to_two_members(): void
    {
        $kk = $this->createKartuKeluarga();
        $warga = User::factory()->create();

        $this->createAnggota(
            $kk,
            $warga->id,
            '6471010101010003'
        );

        $this->expectException(ValidationException::class);

        $this->createAnggota(
            $kk,
            $warga->id,
            '6471010101010004'
        );
    }

    public function test_member_can_exist_without_user_account(): void
    {
        $kk = $this->createKartuKeluarga();

        $anggota = $this->createAnggota(
            $kk,
            null,
            '6471010101010005'
        );

        $this->assertNull($anggota->user_id);
    }

    public function test_linked_warga_account_cannot_be_promoted_to_admin(): void
    {
        $kk = $this->createKartuKeluarga();
        $warga = User::factory()->create();

        $this->createAnggota(
            $kk,
            $warga->id,
            '6471010101010006'
        );

        try {
            $warga->forceFill([
                'role' => 'admin',
            ])->save();

            $this->fail('Akun warga yang terhubung tidak boleh menjadi admin.');
        } catch (ValidationException $exception) {
            $this->assertNotEmpty($exception->errors());
        }

        $this->assertSame('warga', $warga->fresh()->role);
    }

    public function test_member_can_update_without_breaking_existing_account_link(): void
    {
        $kk = $this->createKartuKeluarga();
        $warga = User::factory()->create();

        $anggota = $this->createAnggota(
            $kk,
            $warga->id,
            '6471010101010007'
        );

        $anggota->update([
            'nama' => 'Nama Diperbarui',
        ]);

        $this->assertSame($warga->id, $anggota->fresh()->user_id);
        $this->assertSame('Nama Diperbarui', $anggota->fresh()->nama);
    }
}