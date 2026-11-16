<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_cannot_be_updated_through_backend(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertNotFound();

        $this->assertTrue(
            Hash::check('password', $user->fresh()->password)
        );
    }

    public function test_invalid_password_update_request_is_not_available(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertNotFound();

        $this->assertTrue(
            Hash::check('password', $user->fresh()->password)
        );
    }
}