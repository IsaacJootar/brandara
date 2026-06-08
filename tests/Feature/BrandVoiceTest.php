<?php

namespace Tests\Feature;

use App\Livewire\MyBrand\BrandVoice;
use App\Models\Brand;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Ai\AiProvider;
use App\Services\Ai\AiProviderException;
use App\Services\Ai\AiProviderFactory;
use App\Services\BrandVoice\BrandVoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BrandVoiceTest extends TestCase
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

    private function mockProvider(string $returnJson): AiProviderFactory
    {
        $mockProvider = new class($returnJson) implements AiProvider
        {
            public function __construct(private readonly string $json) {}

            public function name(): string
            {
                return 'Mock';
            }

            public function generate(string $sys, string $usr, int $max = 4096): string
            {
                return $this->json;
            }
        };

        $factory = $this->createMock(AiProviderFactory::class);
        $factory->method('make')->willReturn($mockProvider);

        return $factory;
    }

    // ── BrandVoiceService ─────────────────────────────────────────────────────

    public function test_service_stores_profile_on_brand(): void
    {
        [, $brand] = $this->makeWorkspace();

        $profileJson = json_encode([
            'sentence_length' => 'short',
            'sentence_rhythm' => 'punchy and direct',
            'vocabulary_level' => 'conversational',
            'preferred_words' => ['founder', 'build', 'Africa'],
            'avoided_words' => ['synergy', 'leverage'],
            'structure_preference' => 'prose',
            'opening_style' => 'Starts with a bold statement',
            'closing_style' => 'Ends with a direct question',
            'tone_characteristics' => [
                'humour' => 'dry',
                'warmth' => 'warm',
                'directness' => 'very_direct',
                'confidence' => 'bold',
            ],
            'emoji_usage' => 'rare',
            'punctuation_style' => 'Short sentences. Full stops.',
            'recurring_themes' => ['entrepreneurship', 'Africa'],
            'signature_phrases' => ['Here is what I know', 'Let me be direct'],
            'writing_summary' => 'Direct, warm, and confident.',
        ]);

        $service = new BrandVoiceService($this->mockProvider($profileJson));
        $profile = $service->train($brand, "Post one.\n\nPost two.\n\nPost three.");

        $this->assertSame('short', $profile['sentence_length']);
        $this->assertSame('conversational', $profile['vocabulary_level']);

        $brand->refresh();
        $this->assertNotNull($brand->brand_voice);
        $this->assertSame('short', $brand->brand_voice['sentence_length']);
        $this->assertSame(3, $brand->voice_samples_count);
    }

    public function test_service_handles_malformed_json_with_fallback(): void
    {
        [, $brand] = $this->makeWorkspace();

        $service = new BrandVoiceService($this->mockProvider('not valid json at all'));
        $profile = $service->train($brand, "Sample post one.\n\nSample post two.");

        $this->assertArrayHasKey('writing_summary', $profile);
        $this->assertArrayHasKey('tone_characteristics', $profile);

        $brand->refresh();
        $this->assertNotNull($brand->brand_voice);
    }

    public function test_service_throws_when_no_api_key(): void
    {
        config(['ai.claude.api_key' => null]);
        [, $brand] = $this->makeWorkspace();

        $service = app(BrandVoiceService::class);
        $this->expectException(AiProviderException::class);
        $service->train($brand, 'Sample post one. Sample post two. Sample post three.');
    }

    // ── Livewire component ────────────────────────────────────────────────────

    public function test_component_shows_idle_state_when_no_profile(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        app()->instance('current.brand', $brand);

        Livewire::withoutLazyLoading()
            ->test(BrandVoice::class)
            ->assertSet('status', 'idle');
    }

    public function test_component_shows_trained_state_when_profile_exists(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $brand->brand_voice = ['writing_summary' => 'Direct and warm.', 'sentence_length' => 'short'];
        $brand->save();

        $this->actingAs($user);
        app()->instance('current.brand', $brand);

        Livewire::withoutLazyLoading()
            ->test(BrandVoice::class)
            ->assertSet('status', 'trained');
    }

    public function test_component_validates_minimum_sample_length(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        app()->instance('current.brand', $brand);

        Livewire::withoutLazyLoading()
            ->test(BrandVoice::class)
            ->set('samples', 'too short')
            ->call('train')
            ->assertSet('status', 'error');
    }

    public function test_component_shows_error_when_no_api_key(): void
    {
        config(['ai.claude.api_key' => null]);
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        app()->instance('current.brand', $brand);

        Livewire::withoutLazyLoading()
            ->test(BrandVoice::class)
            ->set('samples', str_repeat('This is a sample post about running a business in Lagos. ', 5))
            ->call('train')
            ->assertSet('status', 'error')
            ->assertSet('errorMessage', 'AI is not configured yet. Ask your administrator to add the API key.');
    }

    public function test_retrain_resets_to_idle(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $brand->brand_voice = ['writing_summary' => 'Some profile'];
        $brand->save();

        $this->actingAs($user);
        app()->instance('current.brand', $brand);

        Livewire::withoutLazyLoading()
            ->test(BrandVoice::class)
            ->assertSet('status', 'trained')
            ->call('retrain')
            ->assertSet('status', 'idle')
            ->assertSet('samples', '');
    }

    // ── My Brand page ─────────────────────────────────────────────────────────

    public function test_my_brand_page_loads(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        $response = $this->get("/{$brand->slug}/my-brand");
        $response->assertStatus(200);
        $response->assertSee('Brand Voice');
        $response->assertSee('Brand Kit');
        $response->assertSee('Brand Profile');
    }
}
