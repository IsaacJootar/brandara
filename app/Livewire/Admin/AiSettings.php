<?php

namespace App\Livewire\Admin;

use App\Models\AdminSetting;
use Illuminate\View\View;
use Livewire\Component;

class AiSettings extends Component
{
    public string $defaultProvider = 'claude';

    public bool $claudeEnabled = true;

    public bool $chatgptEnabled = true;

    public bool $geminiEnabled = true;

    public bool $perplexityEnabled = false;

    public function mount(): void
    {
        $this->defaultProvider = AdminSetting::get('ai_default_provider', 'claude');
        $this->claudeEnabled = (bool) AdminSetting::get('ai_presence_claude_enabled', '1');
        $this->chatgptEnabled = (bool) AdminSetting::get('ai_presence_chatgpt_enabled', '1');
        $this->geminiEnabled = (bool) AdminSetting::get('ai_presence_gemini_enabled', '1');
        $this->perplexityEnabled = (bool) AdminSetting::get('ai_presence_perplexity_enabled', '0');
    }

    public function save(): void
    {
        AdminSetting::set('ai_default_provider', $this->defaultProvider, 'ai');
        AdminSetting::set('ai_presence_claude_enabled', $this->claudeEnabled ? '1' : '0', 'ai');
        AdminSetting::set('ai_presence_chatgpt_enabled', $this->chatgptEnabled ? '1' : '0', 'ai');
        AdminSetting::set('ai_presence_gemini_enabled', $this->geminiEnabled ? '1' : '0', 'ai');
        AdminSetting::set('ai_presence_perplexity_enabled', $this->perplexityEnabled ? '1' : '0', 'ai');
        $this->dispatch('show-toast', message: 'AI settings saved.', type: 'success');
    }

    public function render(): View
    {
        return view('livewire.admin.ai-settings');
    }
}
