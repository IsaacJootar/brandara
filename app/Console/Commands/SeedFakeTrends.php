<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Services\Trends\FakeTrendsSeeder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('trends:seed-fake {brand_slug : The brand slug to seed trends for}')]
#[Description('Seed fake trend signals for a brand (dev/demo use only).')]
class SeedFakeTrends extends Command
{
    public function handle(): int
    {
        $brand = Brand::where('slug', $this->argument('brand_slug'))->first();

        if (! $brand) {
            $this->error("Brand '{$this->argument('brand_slug')}' not found.");

            return Command::FAILURE;
        }

        $count = app(FakeTrendsSeeder::class)->seed($brand);

        $this->info("Seeded {$count} trend signals for '{$brand->name}'.");

        return Command::SUCCESS;
    }
}
