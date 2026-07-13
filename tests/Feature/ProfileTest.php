<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_profile_page_is_not_exposed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/profile')->assertNotFound();
    }

    public function test_legacy_profile_update_is_not_exposed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', [
            'name' => 'Changed Name',
            'email' => 'changed@example.com',
        ])->assertNotFound();

        $this->assertNotSame('Changed Name', $user->fresh()->name);
        $this->assertNotSame('changed@example.com', $user->fresh()->email);
    }

    public function test_legacy_profile_request_does_not_change_identity(): void
    {
        $user = User::factory()->create();
        $originalEmail = $user->email;

        $this->actingAs($user)->patch('/profile', [
            'name' => 'Test User',
            'email' => $originalEmail,
        ])->assertNotFound();

        $this->assertSame($originalEmail, $user->fresh()->email);
    }

    public function test_legacy_account_deletion_is_not_exposed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->delete('/profile', ['password' => 'password'])
            ->assertNotFound();

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh());
    }

    public function test_legacy_account_deletion_never_accepts_an_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->delete('/profile', ['password' => 'wrong-password'])
            ->assertNotFound();

        $this->assertNotNull($user->fresh());
    }
}
