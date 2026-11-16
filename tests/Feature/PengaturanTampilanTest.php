<?php

namespace Tests\Feature;

use App\Filament\Pages\PengaturanTampilan;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PengaturanTampilanTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dapat_membuka_halaman_pengaturan_tampilan(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/pengaturan-tampilan')
            ->assertOk()
            ->assertSee('Media Hero');
    }

    public function test_menyimpan_gambar_hero(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(PengaturanTampilan::class)
            ->set('data.hero_media', [UploadedFile::fake()->image('contoh.png')])
            ->call('save');

        $tersimpan = Pengaturan::current()->hero_media;
        $this->assertStringStartsWith('hero/', $tersimpan);
        Storage::disk('public')->assertExists($tersimpan);
    }

    public function test_format_di_luar_daftar_ditolak(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(PengaturanTampilan::class)
            ->set('data.hero_media', [UploadedFile::fake()->create('hero.heic', 100, 'image/heic')])
            ->call('save')
            ->assertHasErrors();

        $this->assertNull(Pengaturan::current()->hero_media);
    }

    public function test_hero_url_dan_deteksi_video(): void
    {
        $p = Pengaturan::current();
        $this->assertNull($p->heroUrl());
        $this->assertFalse($p->heroIsVideo());

        $p->update(['hero_media' => 'hero/contoh.png']);
        $this->assertStringEndsWith('/storage/hero/contoh.png', $p->fresh()->heroUrl());
        $this->assertFalse($p->fresh()->heroIsVideo());

        $p->update(['hero_media' => 'hero/contoh.MP4']);
        $this->assertTrue($p->fresh()->heroIsVideo());
    }
}
