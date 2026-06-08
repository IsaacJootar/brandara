<?php

namespace App\Providers;

use App\Services\Ai\AiProviderFactory;
use App\Services\Ai\ContentGenerationService;
use App\Services\BrandVoice\BrandVoiceService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AiProviderFactory::class);
        $this->app->singleton(ContentGenerationService::class, fn ($app) => new ContentGenerationService(
            $app->make(AiProviderFactory::class)
        ));
        $this->app->singleton(BrandVoiceService::class, fn ($app) => new BrandVoiceService(
            $app->make(AiProviderFactory::class)
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
