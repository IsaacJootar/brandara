<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_is_not_part_of_password_authentication(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/verify-email')->assertNotFound();
    }

    public function test_user_schema_does_not_require_email_verification_state(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email,
        ]);
        $this->assertArrayNotHasKey('email_verified_at', $user->getAttributes());
    }

    public function test_email_verification_submission_is_not_exposed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/email/verification-notification')
            ->assertNotFound();
    }
}
