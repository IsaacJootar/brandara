<?php

namespace App\Livewire\Settings;

use App\Models\Brand;
use Illuminate\View\View;
use Livewire\Component;

class BrandSettings extends Component
{
    public string $brandId = '';

    // ── General ───────────────────────────────────────────────────────────────
    public string $brandName = '';

    public string $timezone = 'Africa/Lagos';

    public string $language = 'en';

    public string $defaultTone = 'professional';

    // ── Engagement ────────────────────────────────────────────────────────────
    public bool $engagementEnabled = false;

    public string $engagementScanFrequency = 'daily';

    // ── Publishing ────────────────────────────────────────────────────────────
    public string $defaultPostTime = '09:00';

    public bool $evergreenRecycling = false;

    // ── Notifications ─────────────────────────────────────────────────────────
    public bool $notifyPostFailed = true;

    public bool $notifyPostPublished = false;

    public bool $notifyLeadCaptured = true;

    public bool $notifyTrialExpiring = true;

    public string $activeSection = 'general';

    public function mount(Brand $brand): void
    {
        $this->brandId = $brand->id;

        // General
        $this->brandName = $brand->name;
        $this->timezone = $brand->setting('timezone') ?? 'Africa/Lagos';
        $this->language = $brand->language ?? 'en';
        $this->defaultTone = $brand->default_tone ?? 'professional';

        // Engagement
        $this->engagementEnabled = (bool) $brand->setting('engagement_enabled');
        $this->engagementScanFrequency = $brand->setting('engagement_scan_frequency') ?? 'daily';

        // Publishing
        $this->defaultPostTime = $brand->setting('default_post_time') ?? '09:00';
        $this->evergreenRecycling = (bool) $brand->setting('evergreen_recycling');

        // Notifications
        $this->notifyPostFailed = (bool) $brand->setting('notify_post_failed');
        $this->notifyPostPublished = (bool) $brand->setting('notify_post_published');
        $this->notifyLeadCaptured = (bool) $brand->setting('notify_lead_captured');
        $this->notifyTrialExpiring = (bool) $brand->setting('notify_trial_expiring');
    }

    public function setSection(string $section): void
    {
        $this->activeSection = $section;
    }

    public function saveGeneral(): void
    {
        $this->validate([
            'brandName' => ['required', 'string', 'max:100'],
            'timezone' => ['required', 'string'],
            'language' => ['required', 'in:en,fr'],
        ]);

        $brand = Brand::findOrFail($this->brandId);
        $brand->update([
            'name' => $this->brandName,
            'default_tone' => $this->defaultTone,
            'language' => $this->language,
        ]);
        $brand->updateSettings([
            'timezone' => $this->timezone,
        ]);

        $this->dispatch('show-toast', message: 'General settings saved.');
    }

    public function saveEngagement(): void
    {
        $brand = Brand::findOrFail($this->brandId);
        $brand->updateSettings([
            'engagement_enabled' => $this->engagementEnabled,
            'engagement_scan_frequency' => $this->engagementScanFrequency,
        ]);

        $this->dispatch('show-toast', message: $this->engagementEnabled
            ? 'Engagement automation enabled.'
            : 'Engagement automation disabled.'
        );
    }

    public function savePublishing(): void
    {
        $this->validate([
            'defaultPostTime' => ['required', 'regex:/^\d{2}:\d{2}$/'],
        ], [
            'defaultPostTime.regex' => 'Enter a valid time in HH:MM format.',
        ]);

        $brand = Brand::findOrFail($this->brandId);
        $brand->updateSettings([
            'default_post_time' => $this->defaultPostTime,
            'evergreen_recycling' => $this->evergreenRecycling,
        ]);

        $this->dispatch('show-toast', message: 'Publishing settings saved.');
    }

    public function saveNotifications(): void
    {
        $brand = Brand::findOrFail($this->brandId);
        $brand->updateSettings([
            'notify_post_failed' => $this->notifyPostFailed,
            'notify_post_published' => $this->notifyPostPublished,
            'notify_lead_captured' => $this->notifyLeadCaptured,
            'notify_trial_expiring' => $this->notifyTrialExpiring,
        ]);

        $this->dispatch('show-toast', message: 'Notification preferences saved.');
    }

    public function render(): View
    {
        return view('livewire.settings.brand-settings');
    }
}
