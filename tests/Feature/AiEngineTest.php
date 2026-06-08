<?php

namespace Tests\Feature;

use App\Livewire\Create\VariationPicker;
use App\Models\Brand;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Ai\AiProvider;
use App\Services\Ai\AiProviderException;
use App\Services\Ai\AiProviderFactory;
use App\Services\Ai\ClaudeProvider;
use App\Services\Ai\ContentGenerationService;
use App\Services\Ai\OpenAiProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AiEngineTest extends TestCase
{
    use RefreshDatabase;

    private function makeWorkspace(): array
    {
        $workspace = Workspace::create([
            'name' => 'Acme', 'slug' => 'acme',
            'owner_email' => 'owner@acme.test', 'country' => 'NG',
            'timezone' => 'Africa/Lagos', 'plan' => 'starter',
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trialing', 'language' => 'en',
        ]);
        $user = User::create([
            'workspace_id' => $workspace->id, 'name' => 'Owner',
            'email' => 'owner@acme.test', 'password' => bcrypt('secret'),
            'role' => 'owner',
        ]);
        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'Acme Consulting', 'slug' => 'acme-consulting',
            'language' => 'en',
        ]);

        return [$user, $brand];
    }

    // ── Provider factory ──────────────────────────────────────────────────────

    public function test_factory_returns_claude_by_default(): void
    {
        config(['ai.default' => 'claude']);
        $provider = app(AiProviderFactory::class)->make();
        $this->assertInstanceOf(ClaudeProvider::class, $provider);
    }

    public function test_factory_returns_openai_when_configured(): void
    {
        config(['ai.default' => 'openai']);
        $provider = app(AiProviderFactory::class)->make();
        $this->assertInstanceOf(OpenAiProvider::class, $provider);
    }

    public function test_factory_falls_back_to_claude_for_unknown_provider(): void
    {
        config(['ai.default' => 'some_unknown_provider']);
        $provider = app(AiProviderFactory::class)->make();
        $this->assertInstanceOf(ClaudeProvider::class, $provider);
    }

    // ── Provider names ────────────────────────────────────────────────────────

    public function test_claude_provider_has_correct_name(): void
    {
        $this->assertStringContainsString('Claude', (new ClaudeProvider)->name());
    }

    public function test_openai_provider_has_correct_name(): void
    {
        $this->assertStringContainsString('OpenAI', (new OpenAiProvider)->name());
    }

    // ── Config errors ─────────────────────────────────────────────────────────

    public function test_claude_throws_config_error_when_no_api_key(): void
    {
        config(['ai.claude.api_key' => null]);
        $this->expectException(AiProviderException::class);
        (new ClaudeProvider)->generate('system', 'user');
    }

    public function test_openai_throws_config_error_when_no_api_key(): void
    {
        config(['ai.openai.api_key' => null]);
        $this->expectException(AiProviderException::class);
        (new OpenAiProvider)->generate('system', 'user');
    }

    // ── Content generation service with mock provider ─────────────────────────

    public function test_generation_service_parses_3_variations(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        // Mock provider returns valid JSON
        $mockProvider = new class implements AiProvider
        {
            public function name(): string
            {
                return 'Mock';
            }

            public function generate(string $sys, string $usr, int $max = 4096): string
            {
                return json_encode([
                    'authority' => ['angle' => 'Expert', 'platforms' => ['linkedin' => ['body' => 'Authority post', 'hashtags' => ['leadership']]]],
                    'story' => ['angle' => 'Story',  'platforms' => ['linkedin' => ['body' => 'Story post',     'hashtags' => ['growth']]]],
                    'bold' => ['angle' => 'Bold',   'platforms' => ['linkedin' => ['body' => 'Bold post',      'hashtags' => ['truth']]]],
                ]);
            }
        };

        $factory = $this->createMock(AiProviderFactory::class);
        $factory->method('make')->willReturn($mockProvider);

        $service = new ContentGenerationService($factory);
        $result = $service->generate($brand, 'topic', 'Why founders undercharge', ['linkedin'], 'professional');

        $this->assertArrayHasKey('authority', $result);
        $this->assertArrayHasKey('story', $result);
        $this->assertArrayHasKey('bold', $result);
        $this->assertSame('Authority post', $result['authority']['platforms']['linkedin']['body']);
    }

    // ── VariationPicker Livewire component ────────────────────────────────────

    public function test_variation_picker_shows_error_with_no_api_key(): void
    {
        config(['ai.claude.api_key' => null]);
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        Livewire::test(VariationPicker::class, ['brand' => $brand])
            ->set('input', 'Why Nigerian founders undercharge')
            ->set('inputType', 'topic')
            ->set('platforms', ['linkedin'])
            ->call('generate')
            ->assertSet('status', 'error')
            ->assertSet('errorMessage', 'AI is not configured yet. Add your API key to get started.');
    }

    public function test_variation_picker_shows_error_with_empty_input(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        Livewire::test(VariationPicker::class, ['brand' => $brand])
            ->set('input', '   ')
            ->call('generate')
            ->assertSet('status', 'error');
    }
}
