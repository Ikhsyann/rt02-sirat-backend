<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_alias_redirects_to_filament_login(): void
    {
        $this->get('/login')
            ->assertRedirect('/admin/login');
    }

    public function test_filament_login_screen_can_be_rendered(): void
    {
        $this->get('/admin/login')
            ->assertOk();
    }

    public function test_breeze_login_submission_is_not_available(): void
    {
        $this->post('/login', [
            'email' => 'warga@example.com',
            'password' => 'password',
        ])->assertMethodNotAllowed();
    }

    public function test_breeze_logout_route_is_not_available(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertNotFound();
    }

    public function test_only_admin_can_access_filament_panel(): void
    {
        $panel = Filament::getPanel('admin');

        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $warga = User::factory()->create();

        $this->assertTrue($admin->canAccessPanel($panel));
        $this->assertFalse($warga->canAccessPanel($panel));
    }

    public function test_role_cannot_be_changed_through_mass_assignment(): void
    {
        $user = User::factory()->create();

        $user->fill([
            'role' => 'admin',
        ]);

        $user->save();

        $this->assertSame('warga', $user->fresh()->role);
    }
}