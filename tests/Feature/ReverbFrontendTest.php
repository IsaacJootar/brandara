<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReverbFrontendTest extends TestCase
{
    public function test_frontend_initializes_echo_with_reverb(): void
    {
        $javascript = file_get_contents(resource_path('js/app.js'));

        $this->assertIsString($javascript);
        $this->assertStringContainsString("import Echo from 'laravel-echo';", $javascript);
        $this->assertStringContainsString("import Pusher from 'pusher-js';", $javascript);
        $this->assertStringContainsString("broadcaster: 'reverb'", $javascript);
        $this->assertStringContainsString('VITE_REVERB_APP_KEY', $javascript);
        $this->assertStringContainsString("enabledTransports: ['ws', 'wss']", $javascript);
    }

    public function test_example_environment_enables_reverb_for_backend_and_frontend(): void
    {
        $environment = file_get_contents(base_path('.env.example'));

        $this->assertIsString($environment);
        $this->assertStringContainsString('BROADCAST_CONNECTION=reverb', $environment);
        $this->assertStringContainsString('REVERB_APP_ID=', $environment);
        $this->assertStringContainsString('VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"', $environment);
        $this->assertStringContainsString('VITE_REVERB_SCHEME="${REVERB_SCHEME}"', $environment);
    }
}
