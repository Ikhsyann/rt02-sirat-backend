<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_not_available(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/profile')
            ->assertNotFound();
    }

    public function test_profile_information_cannot_be_updated(): void
    {
        $user = User::factory()->create();

        $originalName = $user->name;
        $originalEmail = $user->email;

        $this->actingAs($user)
            ->patch('/profile', [
                'name' => 'Changed Name',
                'email' => 'changed@example.com',
            ])
            ->assertNotFound();

        $user->refresh();

        $this->assertSame($originalName, $user->name);
        $this->assertSame($originalEmail, $user->email);
    }

    public function test_user_account_cannot_be_deleted_through_backend(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email,
        ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_account_deletion_request_is_not_available(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->delete('/profile', [
                'password' => 'wrong-password',
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }
}