<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_is_not_available(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get('/verify-email')
            ->assertNotFound();
    }

    public function test_email_verification_endpoint_is_not_available(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get('/verify-email/'.$user->id.'/'.sha1($user->email))
            ->assertNotFound();
    }

    public function test_email_verification_notification_endpoint_is_not_available(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->post('/email/verification-notification')
            ->assertNotFound();
    }
}
