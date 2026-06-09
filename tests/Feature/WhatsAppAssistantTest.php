<?php

namespace Tests\Feature;

use App\Livewire\Create\WhatsAppAssistant;
use App\Models\Brand;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Ai\AiProvider;
use App\Services\Ai\AiProviderFactory;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WhatsAppAssistantTest extends TestCase
{
    use RefreshDatabase;

    private function makeWorkspace(): array
    {
        $workspace = Workspace::create([
            'name' => 'WA Co', 'slug' => 'wa-co',
            'owner_email' => 'owner@wa.test', 'country' => 'NG',
            'timezone' => 'Africa/Lagos', 'plan' => 'starter',
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trialing', 'language' => 'en',
        ]);
        $user = User::create([
            'workspace_id' => $workspace->id, 'name' => 'Owner',
            'email' => 'owner@wa.test', 'password' => bcrypt('secret'),
            'role' => 'owner',
        ]);
        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'WA Brand', 'slug' => 'wa-brand',
            'language' => 'en',
        ]);

        return [$user, $brand];
    }

    // ── Route ─────────────────────────────────────────────────────────────────

    public function test_whatsapp_page_loads_for_authenticated_user(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        $response = $this->get(route('create.whatsapp', ['brand' => $brand->slug]));

        $response->assertStatus(200);
        $response->assertSee('WhatsApp');
    }

    public function test_whatsapp_page_requires_auth(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        $response = $this->get(route('create.whatsapp', ['brand' => $brand->slug]));

        $response->assertRedirect();
    }

    // ── Livewire component ────────────────────────────────────────────────────

    public function test_component_mounts_with_defaults(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        Livewire::actingAs($user)
            ->test(WhatsAppAssistant::class, ['brand' => $brand])
            ->assertSet('brandId', $brand->id)
            ->assertSet('type', 'broadcast')
            ->assertSet('status', 'idle');
    }

    public function test_set_type_switches_type_and_resets_state(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        Livewire::actingAs($user)
            ->test(WhatsAppAssistant::class, ['brand' => $brand])
            ->call('setType', 'promo')
            ->assertSet('type', 'promo')
            ->assertSet('status', 'idle')
            ->assertSet('result', []);
    }

    public function test_set_type_ignores_invalid_type(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        Livewire::actingAs($user)
            ->test(WhatsAppAssistant::class, ['brand' => $brand])
            ->call('setType', 'invalid_type')
            ->assertSet('type', 'broadcast');
    }

    public function test_generate_validates_context_required(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        Livewire::actingAs($user)
            ->test(WhatsAppAssistant::class, ['brand' => $brand])
            ->set('context', '')
            ->call('generate')
            ->assertHasErrors(['context' => 'required']);
    }

    public function test_generate_validates_context_min_length(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        Livewire::actingAs($user)
            ->test(WhatsAppAssistant::class, ['brand' => $brand])
            ->set('context', 'short')
            ->call('generate')
            ->assertHasErrors(['context' => 'min']);
    }

    public function test_generate_calls_service_and_sets_done(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        $fakeResult = [
            'type'     => 'broadcast',
            'messages' => [
                ['label' => 'Variation 1 — Direct', 'body' => 'Hey, just wanted to share something with you...', 'emoji_note' => ''],
                ['label' => 'Variation 2 — Warm',   'body' => 'Something exciting is happening this week...', 'emoji_note' => 'One emoji at the start works well here.'],
            ],
            'do_tips'   => ['Send before 10am for best open rates', 'Use their name if possible'],
            'dont_tips' => ['Don\'t send on a Sunday evening', 'Don\'t include more than one link'],
        ];

        $mockFactory = $this->createMock(AiProviderFactory::class);
        $mockProvider = $this->createMock(AiProvider::class);
        $mockProvider->method('generate')->willReturn(json_encode($fakeResult));
        $mockFactory->method('make')->willReturn($mockProvider);
        $this->app->instance(AiProviderFactory::class, $mockFactory);

        Livewire::actingAs($user)
            ->test(WhatsAppAssistant::class, ['brand' => $brand])
            ->set('context', 'Launching a new 6-week consulting programme for Lagos founders starting July 1st')
            ->call('generate')
            ->assertSet('status', 'done')
            ->assertSee('Variation 1 — Direct');
    }

    public function test_start_over_resets_all_state(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        Livewire::actingAs($user)
            ->test(WhatsAppAssistant::class, ['brand' => $brand])
            ->set('context', 'Some context here to use')
            ->set('status', 'done')
            ->call('startOver')
            ->assertSet('context', '')
            ->assertSet('status', 'idle')
            ->assertSet('result', []);
    }

    // ── WhatsAppService ───────────────────────────────────────────────────────

    public function test_service_parses_valid_json(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        $payload = [
            'type'     => 'promo',
            'messages' => [
                ['label' => 'Variation 1', 'body' => 'Promo message body', 'emoji_note' => ''],
            ],
            'do_tips'   => ['Be specific about the offer'],
            'dont_tips' => ['Don\'t bury the price'],
        ];

        $mockFactory = $this->createMock(AiProviderFactory::class);
        $mockProvider = $this->createMock(AiProvider::class);
        $mockProvider->method('generate')->willReturn(json_encode($payload));
        $mockFactory->method('make')->willReturn($mockProvider);
        $this->app->instance(AiProviderFactory::class, $mockFactory);

        $result = app(WhatsAppService::class)->generate($brand, 'promo', 'Flash sale this weekend only');

        $this->assertEquals('promo', $result['type']);
        $this->assertCount(1, $result['messages']);
        $this->assertEquals('Promo message body', $result['messages'][0]['body']);
    }

    public function test_service_falls_back_on_invalid_json(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        $mockFactory = $this->createMock(AiProviderFactory::class);
        $mockProvider = $this->createMock(AiProvider::class);
        $mockProvider->method('generate')->willReturn('not valid json at all');
        $mockFactory->method('make')->willReturn($mockProvider);
        $this->app->instance(AiProviderFactory::class, $mockFactory);

        $result = app(WhatsAppService::class)->generate($brand, 'broadcast', 'Some context for the message');

        $this->assertEquals('broadcast', $result['type']);
        $this->assertCount(1, $result['messages']);
        $this->assertEquals('not valid json at all', $result['messages'][0]['body']);
    }

    public function test_service_types_constant_has_four_entries(): void
    {
        $this->assertCount(4, WhatsAppService::TYPES);
        $this->assertArrayHasKey('broadcast', WhatsAppService::TYPES);
        $this->assertArrayHasKey('status', WhatsAppService::TYPES);
        $this->assertArrayHasKey('promo', WhatsAppService::TYPES);
        $this->assertArrayHasKey('follow_up', WhatsAppService::TYPES);
    }
}
