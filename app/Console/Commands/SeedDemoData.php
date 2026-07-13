<?php

namespace App\Console\Commands;

use Database\Seeders\DemoSeeder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Signature('demo:seed')]
#[Description('Create or refresh safe local demo accounts and realistic test data.')]
class SeedDemoData extends Command
{
    public function handle(): int
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->error('Demo data can only be seeded in local or testing environments.');

            return Command::FAILURE;
        }

        try {
            app(DemoSeeder::class)->run();
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return Command::FAILURE;
        }

        $this->info('Demo accounts are ready. Existing non-demo data was not changed.');
        $this->table(
            ['Plan', 'Email', 'Password', 'Main brand'],
            [
                ['Basic', 'demo.basic@brandara.test', DemoSeeder::PASSWORD, 'Kora Advisory'],
                ['Growth', 'demo.growth@brandara.test', DemoSeeder::PASSWORD, 'Northstar Growth Studio'],
                ['Agency', 'demo.agency@brandara.test', DemoSeeder::PASSWORD, 'Lagos Launch Lab'],
            ],
        );

        return Command::SUCCESS;
    }
}
