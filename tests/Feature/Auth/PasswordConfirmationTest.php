<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_confirmation_screen_is_not_available(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/confirm-password')
            ->assertNotFound();
    }

    public function test_password_confirmation_submission_is_not_available(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/confirm-password', [
                'password' => 'password',
            ])
            ->assertNotFound();
    }

    public function test_invalid_password_confirmation_endpoint_is_not_available(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/confirm-password', [
                'password' => 'wrong-password',
            ])
            ->assertNotFound();
    }
}
