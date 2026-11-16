<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_screen_is_not_available(): void
    {
        $this->get('/forgot-password')
            ->assertNotFound();
    }

    public function test_forgot_password_request_is_not_available(): void
    {
        $user = User::factory()->create();

        $this->post('/forgot-password', [
            'email' => $user->email,
        ])->assertNotFound();

        $this->assertDatabaseCount('password_reset_tokens', 0);
    }

    public function test_reset_password_screen_is_not_available(): void
    {
        $this->get('/reset-password/test-token')
            ->assertNotFound();
    }

    public function test_reset_password_submission_is_not_available(): void
    {
        $user = User::factory()->create();

        $this->post('/reset-password', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertNotFound();

        $this->assertTrue(
            Hash::check('password', $user->fresh()->password)
        );
    }
}