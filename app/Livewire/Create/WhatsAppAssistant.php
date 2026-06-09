<?php

namespace App\Livewire\Create;

use App\Models\Brand;
use App\Services\Ai\AiProviderException;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\View\View;
use Livewire\Component;

class WhatsAppAssistant extends Component
{
    public string $brandId = '';

    public string $type = 'broadcast';

    public string $context = '';

    /** idle | generating | done | error */
    public string $status = 'idle';

    public string $errorMessage = '';

    /** @var array<string, mixed> */
    public array $result = [];

    public function mount(Brand $brand): void
    {
        $this->brandId = $brand->id;
    }

    public function setType(string $type): void
    {
        if (array_key_exists($type, WhatsAppService::TYPES)) {
            $this->type = $type;
            $this->status = 'idle';
            $this->result = [];
            $this->errorMessage = '';
        }
    }

    public function generate(): void
    {
        $this->errorMessage = '';
        $this->result = [];

        $this->validate([
            'context' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'context.required' => 'Tell us what this message is about.',
            'context.min' => 'Give a bit more detail — at least 10 characters.',
        ]);

        $this->status = 'generating';

        try {
            $brand = Brand::findOrFail($this->brandId);
            $this->result = app(WhatsAppService::class)->generate($brand, $this->type, $this->context);
            $this->status = 'done';
        } catch (AiProviderException $e) {
            $this->errorMessage = $e->isConfigError
                ? 'AI is not configured yet. Add your API key to get started.'
                : 'Something went wrong generating your message. Please try again.';
            $this->status = 'error';
        } catch (\Throwable) {
            $this->errorMessage = 'Something went wrong. Please try again in a moment.';
            $this->status = 'error';
        }
    }

    public function startOver(): void
    {
        $this->context = '';
        $this->status = 'idle';
        $this->result = [];
        $this->errorMessage = '';
    }

    public function render(): View
    {
        return view('livewire.create.whatsapp-assistant', [
            'types' => WhatsAppService::TYPES,
        ]);
    }
}
