<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\MediaFile;
use App\Models\Post;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_demo_command_creates_realistic_data_for_every_plan(): void
    {
        $this->artisan('demo:seed')->assertSuccessful();

        $this->assertDatabaseCount('workspaces', 3);
        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseCount('brands', 4);
        $this->assertSame(
            ['agency', 'pro', 'starter'],
            Workspace::orderBy('plan')->pluck('plan')->all(),
        );

        $growthUser = User::where('email', 'demo.growth@brandara.test')->firstOrFail();
        $this->assertTrue(Hash::check('password', $growthUser->password));

        $demoBrandIds = Brand::pluck('id');
        $this->assertSame(
            ['cancelled', 'draft', 'failed', 'in_review', 'published', 'scheduled'],
            Post::whereIn('brand_id', $demoBrandIds)->distinct()->orderBy('status')->pluck('status')->all(),
        );

        $this->assertGreaterThanOrEqual(90, Post::whereIn('brand_id', $demoBrandIds)->count());
        $this->assertGreaterThanOrEqual(200, DB::table('post_analytics')->whereIn('brand_id', $demoBrandIds)->count());
        $this->assertGreaterThanOrEqual(50, DB::table('leads')->whereIn('brand_id', $demoBrandIds)->count());
        $this->assertGreaterThanOrEqual(15, DB::table('platform_connections')->whereIn('brand_id', $demoBrandIds)->count());
        $this->assertGreaterThanOrEqual(20, DB::table('notifications')->count());
        $this->assertGreaterThanOrEqual(20, DB::table('engagement_actions')->whereIn('brand_id', $demoBrandIds)->count());
        $this->assertGreaterThanOrEqual(90, DB::table('trend_signals')->whereIn('brand_id', $demoBrandIds)->count());
        $this->assertGreaterThanOrEqual(15, DB::table('ai_presence_results')->whereIn('brand_id', $demoBrandIds)->count());
        $this->assertGreaterThanOrEqual(3, DB::table('subscriptions')->count());

        $media = MediaFile::firstOrFail();
        Storage::disk('public')->assertExists($media->storage_path);
    }

    public function test_demo_command_is_repeatable_without_duplicating_data(): void
    {
        $this->artisan('demo:seed')->assertSuccessful();

        $tables = [
            'workspaces', 'users', 'brands', 'content_pillars', 'campaigns', 'posts',
            'media_files', 'platform_connections', 'post_analytics', 'leads',
            'notifications', 'subscriptions', 'engagement_rules', 'engagement_actions',
            'tracked_keywords', 'trend_signals', 'ai_presence_results',
            'ai_visibility_checks', 'ai_visibility_reports', 'ai_generated_assets',
        ];
        $firstCounts = collect($tables)->mapWithKeys(
            fn (string $table) => [$table => DB::table($table)->count()],
        );

        $this->artisan('demo:seed')->assertSuccessful();

        foreach ($firstCounts as $table => $count) {
            $this->assertSame($count, DB::table($table)->count(), "{$table} should not gain duplicates.");
        }
    }

    public function test_demo_command_preserves_existing_non_demo_data(): void
    {
        $workspace = Workspace::factory()->create([
            'name' => 'Real Workspace',
            'slug' => 'real-workspace',
            'owner_email' => 'owner@real.test',
        ]);
        $user = User::factory()->for($workspace)->create(['email' => 'owner@real.test']);
        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'Real Brand',
            'slug' => 'real-brand',
        ]);
        $post = Post::create([
            'brand_id' => $brand->id,
            'created_by' => $user->id,
            'input_type' => 'manual',
            'raw_input' => 'Keep this real post unchanged.',
            'platform_contents' => ['linkedin' => ['body' => 'Keep this real post unchanged.']],
            'tone' => 'professional',
            'status' => 'draft',
        ]);

        $this->artisan('demo:seed')->assertSuccessful();

        $this->assertModelExists($workspace);
        $this->assertModelExists($brand);
        $this->assertModelExists($post);
        $this->assertSame('Keep this real post unchanged.', $post->fresh()->raw_input);
    }

    public function test_demo_command_stops_when_reserved_workspace_slug_has_non_demo_owner(): void
    {
        Workspace::factory()->create([
            'slug' => 'brandara-demo-basic',
            'owner_email' => 'someone@example.com',
        ]);

        $this->artisan('demo:seed')
            ->expectsOutput("Workspace slug 'brandara-demo-basic' is already used by non-demo data.")
            ->assertFailed();

        $this->assertDatabaseMissing('users', ['email' => 'demo.basic@brandara.test']);
        $this->assertDatabaseCount('brands', 0);
    }

    public function test_demo_command_refuses_to_run_in_production(): void
    {
        $originalEnvironment = app()->environment();
        app()->detectEnvironment(fn (): string => 'production');

        try {
            $this->artisan('demo:seed')
                ->expectsOutput('Demo data can only be seeded in local or testing environments.')
                ->assertFailed();
        } finally {
            app()->detectEnvironment(fn (): string => $originalEnvironment);
        }

        $this->assertDatabaseCount('workspaces', 0);
    }

    public function test_default_database_seeder_adds_system_settings_without_demo_accounts(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('billing_plans', 36);
        $this->assertGreaterThan(0, DB::table('admin_settings')->count());
        $this->assertDatabaseMissing('users', ['email' => 'demo.basic@brandara.test']);
    }
}
