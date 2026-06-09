<?php

namespace Tests\Feature;

use App\Livewire\Create\CarouselGenerator;
use App\Models\Brand;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Ai\AiProvider;
use App\Services\Ai\AiProviderFactory;
use App\Services\Media\CarouselService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CarouselGeneratorTest extends TestCase
{
    use RefreshDatabase;

    private function makeWorkspace(): array
    {
        $workspace = Workspace::create([
            'name' => 'Carousel Co', 'slug' => 'carousel-co',
            'owner_email' => 'owner@carousel.test', 'country' => 'NG',
            'timezone' => 'Africa/Lagos', 'plan' => 'starter',
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trialing', 'language' => 'en',
        ]);
        $user = User::create([
            'workspace_id' => $workspace->id, 'name' => 'Owner',
            'email' => 'owner@carousel.test', 'password' => bcrypt('secret'),
            'role' => 'owner',
        ]);
        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'Carousel Brand', 'slug' => 'carousel-brand',
            'language' => 'en',
        ]);

        return [$user, $brand];
    }

    // ── Route ─────────────────────────────────────────────────────────────────

    public function test_carousel_page_loads_for_authenticated_user(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        $response = $this->get(route('create.carousel', ['brand' => $brand->slug]));

        $response->assertStatus(200);
        $response->assertSee('Carousel');
    }

    public function test_carousel_page_requires_auth(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        $response = $this->get(route('create.carousel', ['brand' => $brand->slug]));

        $response->assertRedirect();
    }

    // ── Livewire component ────────────────────────────────────────────────────

    public function test_component_mounts(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        Livewire::actingAs($user)
            ->test(CarouselGenerator::class, ['brand' => $brand])
            ->assertSet('brandId', $brand->id)
            ->assertSet('status', 'idle')
            ->assertSet('mode', 'carousel');
    }

    public function test_carousel_mode_validates_topic_required(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        Livewire::actingAs($user)
            ->test(CarouselGenerator::class, ['brand' => $brand])
            ->set('mode', 'carousel')
            ->set('topic', '')
            ->call('generate')
            ->assertHasErrors(['topic' => 'required']);
    }

    public function test_quote_mode_validates_input_required(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        Livewire::actingAs($user)
            ->test(CarouselGenerator::class, ['brand' => $brand])
            ->set('mode', 'quote')
            ->set('quoteInput', '')
            ->call('generate')
            ->assertHasErrors(['quoteInput' => 'required']);
    }

    public function test_carousel_generate_calls_service_and_shows_result(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        $fakeResult = [
            'platform'     => 'linkedin',
            'structure'    => 'problem-solution',
            'total_slides' => 6,
            'slides'       => [
                ['slide' => 1, 'type' => 'hook', 'headline' => 'Hook headline', 'body' => '', 'visual_note' => 'Bold background'],
                ['slide' => 6, 'type' => 'cta',  'headline' => 'CTA headline',  'body' => 'Follow for more', 'visual_note' => 'Green CTA'],
            ],
            'canva_tip' => 'Use consistent brand colours.',
        ];

        $mockFactory = $this->createMock(AiProviderFactory::class);
        $mockProvider = $this->createMock(AiProvider::class);
        $mockProvider->method('generate')->willReturn(json_encode($fakeResult));
        $mockFactory->method('make')->willReturn($mockProvider);
        $this->app->instance(AiProviderFactory::class, $mockFactory);

        Livewire::actingAs($user)
            ->test(CarouselGenerator::class, ['brand' => $brand])
            ->set('topic', 'How to price consulting services in Nigeria')
            ->set('platform', 'linkedin')
            ->set('structure', 'problem-solution')
            ->call('generate')
            ->assertSet('status', 'done')
            ->assertSet('result.platform', 'linkedin')
            ->assertSee('Hook headline');
    }

    public function test_start_over_resets_state(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        Livewire::actingAs($user)
            ->test(CarouselGenerator::class, ['brand' => $brand])
            ->set('topic', 'Some topic')
            ->set('status', 'done')
            ->call('startOver')
            ->assertSet('topic', '')
            ->assertSet('status', 'idle')
            ->assertSet('result', []);
    }

    // ── CarouselService parse ─────────────────────────────────────────────────

    public function test_service_parses_valid_carousel_json(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        $payload = [
            'platform' => 'instagram', 'structure' => 'listicle', 'total_slides' => 5,
            'slides' => [['slide' => 1, 'type' => 'hook', 'headline' => 'Test hook', 'body' => '', 'visual_note' => '']],
            'canva_tip' => 'Keep it clean.',
        ];

        $mockFactory = $this->createMock(AiProviderFactory::class);
        $mockProvider = $this->createMock(AiProvider::class);
        $mockProvider->method('generate')->willReturn(json_encode($payload));
        $mockFactory->method('make')->willReturn($mockProvider);
        $this->app->instance(AiProviderFactory::class, $mockFactory);

        $result = app(CarouselService::class)->generate($brand, 'Test topic here long enough', 'instagram', 'listicle');

        $this->assertEquals('instagram', $result['platform']);
        $this->assertCount(1, $result['slides']);
        $this->assertEquals('Test hook', $result['slides'][0]['headline']);
    }

    public function test_service_falls_back_gracefully_on_invalid_json(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        $mockFactory = $this->createMock(AiProviderFactory::class);
        $mockProvider = $this->createMock(AiProvider::class);
        $mockProvider->method('generate')->willReturn('not valid json');
        $mockFactory->method('make')->willReturn($mockProvider);
        $this->app->instance(AiProviderFactory::class, $mockFactory);

        $result = app(CarouselService::class)->generate($brand, 'Test topic here long enough', 'linkedin', 'step-by-step');

        $this->assertEquals('unknown', $result['platform']);
        $this->assertCount(1, $result['slides']);
    }
}
