<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperadminBootstrapTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_can_create_first_superadmin(): void
    {
        $password = 'SecurePassword123!';

        $this->artisan('app:create-superadmin')
            ->expectsQuestion('Nama lengkap', 'Superadmin Utama')
            ->expectsQuestion('Alamat email', 'superadmin@rt02.test')
            ->expectsQuestion('Kata sandi (minimal 12 karakter)', $password)
            ->expectsQuestion('Ulangi kata sandi', $password)
            ->expectsOutput('Superadmin berhasil dibuat.')
            ->doesntExpectOutput($password)
            ->assertExitCode(0);

        $user = User::where('email', 'superadmin@rt02.test')->first();

        $this->assertNotNull($user);
        $this->assertSame('superadmin', $user->role);
        $this->assertTrue(Hash::check($password, $user->password));
    }

    public function test_command_refuses_to_create_second_bootstrap_superadmin(): void
    {
        User::factory()->admin()->create();

        $superadmin = User::factory()->create();
        $superadmin->forceFill(['role' => 'superadmin'])->save();

        $this->artisan('app:create-superadmin')
            ->expectsOutput('Akun superadmin sudah tersedia. Pembuatan pertama dibatalkan.')
            ->assertExitCode(1);

        $this->assertSame(
            1,
            User::where('role', 'superadmin')->count()
        );
    }
}
