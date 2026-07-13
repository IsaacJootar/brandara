<?php

namespace Tests\Feature;

use App\Livewire\AiVisibility\Dashboard as AiVisibilityDashboard;
use App\Livewire\Analytics\ResultsDashboard;
use App\Livewire\Create\CarouselGenerator;
use App\Livewire\Create\PillarAlert;
use App\Livewire\Create\TikTokToolkit;
use App\Livewire\Create\VariationPicker;
use App\Livewire\Create\WhatsAppAssistant;
use App\Livewire\Dashboard\Metrics;
use App\Livewire\Grow\EngagementAutomation;
use App\Livewire\Grow\LeadTracker;
use App\Livewire\Media\MediaLibrary;
use App\Livewire\Media\MediaPicker;
use App\Livewire\MyBrand\BrandKit;
use App\Livewire\MyBrand\BrandProfile;
use App\Livewire\MyBrand\BrandVoice;
use App\Livewire\MyBrand\CompletionScore;
use App\Livewire\Plan\Index as PlanIndex;
use App\Livewire\PostComposer;
use App\Livewire\Schedule\Index as ScheduleIndex;
use App\Livewire\Settings\BrandSettings;
use App\Livewire\Trends\TrendsDashboard;
use App\Models\Brand;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Attributes\Locked;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use ReflectionProperty;
use Tests\TestCase;

class LivewireBrandIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_brand_component_locks_its_brand_identifier(): void
    {
        $components = [
            AiVisibilityDashboard::class,
            ResultsDashboard::class,
            CarouselGenerator::class,
            PillarAlert::class,
            TikTokToolkit::class,
            VariationPicker::class,
            WhatsAppAssistant::class,
            Metrics::class,
            EngagementAutomation::class,
            LeadTracker::class,
            MediaLibrary::class,
            MediaPicker::class,
            BrandKit::class,
            BrandProfile::class,
            BrandVoice::class,
            CompletionScore::class,
            PlanIndex::class,
            PostComposer::class,
            ScheduleIndex::class,
            BrandSettings::class,
            TrendsDashboard::class,
        ];

        foreach ($components as $component) {
            $attributes = (new ReflectionProperty($component, 'brandId'))
                ->getAttributes(Locked::class);

            $this->assertCount(1, $attributes, "{$component} must lock brandId.");
        }
    }

    public function test_client_cannot_change_component_brand_identifier(): void
    {
        [$user, $brand] = $this->makeWorkspace('Owner', 'owner@example.test');
        [, $otherBrand] = $this->makeWorkspace('Other', 'other@example.test');

        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::actingAs($user)
            ->test(MediaPicker::class, ['brand' => $brand])
            ->set('brandId', $otherBrand->id);
    }

    private function makeWorkspace(string $name, string $email): array
    {
        $workspace = Workspace::create([
            'name' => "{$name} Workspace",
            'slug' => str($name)->slug(),
            'owner_email' => $email,
            'country' => 'NG',
            'timezone' => 'Africa/Lagos',
            'plan' => 'agency',
            'trial_ends_at' => now()->addDays(7),
            'subscription_status' => 'trialing',
            'language' => 'en',
        ]);

        $user = User::create([
            'workspace_id' => $workspace->id,
            'name' => $name,
            'email' => $email,
            'password' => 'password',
            'role' => 'owner',
        ]);

        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => "{$name} Brand",
            'slug' => str($name)->slug().'-brand',
            'language' => 'en',
        ]);

        return [$user, $brand];
    }
}
