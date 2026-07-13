<?php

namespace Tests\Feature\Auth;

use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/get-started');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/get-started', [
            'workspace_name' => 'Test Workspace',
            'brand_name' => 'Test Brand',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'country' => 'NG',
        ]);

        $this->assertAuthenticated();
        $brand = Brand::where('slug', 'test-brand')->sole();
        $response->assertRedirect(route('dashboard', ['brand' => $brand->slug], absolute: false));
        $this->assertDatabaseHas('workspaces', ['slug' => 'test-workspace']);
    }
}
