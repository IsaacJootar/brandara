<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Services\Analytics\FakeAnalyticsSeeder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('analytics:seed-fake {brand_slug : The brand slug to seed analytics for} {--days=30 : Number of days to seed}')]
#[Description('Seed fake analytics data for a brand (dev/demo use only).')]
class SeedFakeAnalytics extends Command
{
    public function handle(): int
    {
        $slug = $this->argument('brand_slug');
        $days = (int) $this->option('days');
        $brand = Brand::where('slug', $slug)->first();

        if (! $brand) {
            $this->error("Brand '{$slug}' not found.");

            return Command::FAILURE;
        }

        $count = app(FakeAnalyticsSeeder::class)->seed($brand, $days);

        $this->info("Seeded {$count} analytics records for '{$brand->name}' over {$days} days.");

        return Command::SUCCESS;
    }
}
