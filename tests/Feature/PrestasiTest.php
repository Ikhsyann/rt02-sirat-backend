<?php

namespace Tests\Feature;

use App\Filament\Resources\Prestasis\Pages\CreatePrestasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PrestasiTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_halaman_resource_prestasi_dirender(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/prestasis')
            ->assertOk();

        $this->actingAs($this->admin())
            ->get('/admin/prestasis/create')
            ->assertOk();
    }

    public function test_admin_dapat_membuat_prestasi(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreatePrestasi::class)
            ->fillForm([
                'judul' => 'Juara 1 Kebersihan',
                'jenis' => 'piala',
                'tanggal' => '2026-11-15',
                'deskripsi' => 'Piala juara 1 lomba kebersihan.',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        // SQLite menyimpan kolom date sebagai datetime penuh.
        $this->assertDatabaseHas('prestasis', [
            'judul' => 'Juara 1 Kebersihan',
            'jenis' => 'piala',
            'tanggal' => '2026-11-15 00:00:00',
        ]);
    }

    public function test_jenis_di_luar_dua_nilai_ditolak(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreatePrestasi::class)
            ->fillForm([
                'judul' => 'Medali Emas',
                'jenis' => 'medali',
                'tanggal' => '2026-11-15',
            ])
            ->call('create')
            ->assertHasFormErrors(['jenis']);

        $this->assertDatabaseMissing('prestasis', [
            'judul' => 'Medali Emas',
        ]);
    }

    public function test_upload_format_foto_salah_ditolak(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        Livewire::test(CreatePrestasi::class)
            ->fillForm([
                'judul' => 'Sertifikat K3',
                'jenis' => 'sertifikat',
                'tanggal' => '2026-11-15',
            ])
            ->set('data.foto', [
                UploadedFile::fake()->create('foto.heic', 100, 'image/heic'),
            ])
            ->call('create')
            ->assertHasErrors();

        $this->assertDatabaseMissing('prestasis', [
            'judul' => 'Sertifikat K3',
        ]);
    }
}
