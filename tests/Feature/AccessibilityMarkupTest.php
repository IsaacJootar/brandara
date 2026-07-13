<?php

namespace Tests\Feature;

use Tests\TestCase;

class AccessibilityMarkupTest extends TestCase
{
    public function test_icon_only_controls_have_accessible_names(): void
    {
        $this->assertFileContains('resources/views/livewire/notification-bell.blade.php', [
            'aria-label="{{ $unreadCount > 0',
            'aria-controls="notification-panel"',
        ]);
        $this->assertFileContains('resources/views/livewire/schedule/index.blade.php', [
            'aria-label="Previous month"',
            'aria-label="Next month"',
        ]);
        $this->assertFileContains('resources/views/livewire/post-composer.blade.php', [
            'aria-label="Remove {{ $media[\'name\'] }}"',
        ]);
        $this->assertFileContains('resources/views/livewire/media/media-picker.blade.php', [
            'aria-label="Close media library"',
            'aria-label="Search media files"',
        ]);
        $this->assertFileContains('resources/views/livewire/trends/trends-dashboard.blade.php', [
            'aria-label="Stop tracking {{ $kw->keyword }}"',
        ]);
        $this->assertFileContains('resources/views/livewire/create/variation-picker.blade.php', [
            '<button type="button" wire:click="selectVariation',
            'aria-pressed="{{ $isSelected ? \'true\' : \'false\' }}"',
        ]);
        $this->assertFileContains('resources/views/livewire/grow/lead-tracker.blade.php', [
            'aria-label="Remove {{ $lead->name }}"',
        ]);
    }

    public function test_dialogs_and_mobile_navigation_support_keyboard_use(): void
    {
        $this->assertFileContains('resources/views/components/layouts/app.blade.php', [
            'aria-controls="appSidebar"',
            'aria-expanded="false"',
            "event.key === 'Escape'",
            "document.body.style.overflow = 'hidden'",
            'hamburger.focus()',
        ]);
        $this->assertFileContains('resources/views/livewire/schedule/index.blade.php', [
            'x-on:keydown.escape.window="$wire.closeSchedule()"',
            'role="dialog" aria-modal="true"',
            'aria-labelledby="schedule-dialog-title"',
        ]);
        $this->assertFileContains('resources/views/livewire/media/media-picker.blade.php', [
            'x-on:keydown.escape.window="$wire.closePicker()"',
            'role="dialog" aria-modal="true"',
            'aria-labelledby="media-picker-title"',
        ]);
        $this->assertFileContains('resources/css/app.css', [
            ':where(a, button, input, select, textarea):focus-visible',
        ]);
    }

    public function test_visual_switches_expose_their_current_state(): void
    {
        $this->assertFileContains('resources/views/livewire/settings/brand-settings.blade.php', [
            'role="switch" aria-label="Engagement automation"',
            'role="switch" aria-label="Evergreen recycling"',
            'aria-checked="{{ $this->{$row[\'prop\']} ? \'true\' : \'false\' }}"',
        ]);
        $this->assertFileContains('resources/views/livewire/admin/feature-manager.blade.php', [
            'role="switch"',
            'aria-checked="{{ $hasAccess ? \'true\' : \'false\' }}"',
        ]);
        $this->assertFileContains('resources/views/livewire/ai-visibility/dashboard.blade.php', [
            'role="switch" aria-label="{{ $def[\'label\'] }}"',
        ]);
    }

    /**
     * @param  list<string>  $needles
     */
    private function assertFileContains(string $path, array $needles): void
    {
        $contents = file_get_contents(base_path($path));

        $this->assertIsString($contents);

        foreach ($needles as $needle) {
            $this->assertStringContainsString($needle, $contents, "Missing accessibility markup in {$path}");
        }
    }
}
