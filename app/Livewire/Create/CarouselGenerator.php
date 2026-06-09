<?php

namespace App\Livewire\Create;

use App\Models\Brand;
use App\Services\Ai\AiProviderException;
use App\Services\Media\CarouselService;
use Illuminate\View\View;
use Livewire\Component;

class CarouselGenerator extends Component
{
    public string $brandId = '';

    public string $topic = '';

    public string $platform = 'linkedin';

    public string $structure = 'problem-solution';

    /** carousel | quote */
    public string $mode = 'carousel';

    public string $quoteInput = '';

    public string $cardType = 'quote_card';

    /** idle | generating | done | error */
    public string $status = 'idle';

    public string $errorMessage = '';

    /** @var array<string, mixed> */
    public array $result = [];

    public array $platforms = [
        'linkedin' => 'LinkedIn',
        'instagram' => 'Instagram',
        'facebook' => 'Facebook',
    ];

    public array $structures = [
        'problem-solution' => 'Problem → Solution',
        'step-by-step' => 'Step-by-step guide',
        'listicle' => 'Tips list',
        'before-after' => 'Before & After',
        'case-study' => 'Case study',
    ];

    public array $cardTypes = [
        'quote_card' => 'Founder quote card',
        'testimonial_card' => 'Client testimonial',
        'motivational_card' => 'Motivational graphic',
    ];

    public function mount(Brand $brand): void
    {
        $this->brandId = $brand->id;
    }

    public function generate(): void
    {
        $this->errorMessage = '';
        $this->result = [];

        if ($this->mode === 'carousel') {
            $this->validate([
                'topic' => ['required', 'string', 'min:5', 'max:500'],
                'platform' => ['required', 'string'],
                'structure' => ['required', 'string'],
            ]);
        } else {
            $this->validate([
                'quoteInput' => ['required', 'string', 'min:10', 'max:2000'],
            ]);
        }

        $this->status = 'generating';

        try {
            $brand = Brand::findOrFail($this->brandId);
            $service = app(CarouselService::class);

            if ($this->mode === 'carousel') {
                $this->result = $service->generate($brand, $this->topic, $this->platform, $this->structure);
            } else {
                $this->result = $service->generateQuoteCards($brand, $this->quoteInput, $this->cardType);
            }

            $this->status = 'done';
        } catch (AiProviderException $e) {
            $this->errorMessage = $e->isConfigError
                ? 'AI is not configured yet. Add your API key to get started.'
                : 'Something went wrong generating your content. Please try again.';
            $this->status = 'error';
        } catch (\Throwable) {
            $this->errorMessage = 'Something went wrong. Please try again in a moment.';
            $this->status = 'error';
        }
    }

    public function startOver(): void
    {
        $this->topic = '';
        $this->quoteInput = '';
        $this->status = 'idle';
        $this->result = [];
        $this->errorMessage = '';
    }

    public function render(): View
    {
        return view('livewire.create.carousel-generator');
    }
}
