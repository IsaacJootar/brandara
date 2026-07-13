<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use App\Models\Workspace;
use App\Support\PublicUrlGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CanvaWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'canva-webhook-test-secret';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.canva.webhook_secret', self::SECRET);
        config()->set('services.canva.export_hosts', ['export-download.canva.com']);
    }

    public function test_webhook_requires_a_configured_secret(): void
    {
        [, , $brand] = $this->makeWorkspace();
        config()->set('services.canva.webhook_secret', null);

        $this->postJson(route('canva.webhook', ['brand' => $brand->id]), [
            'export_url' => 'https://export-download.canva.com/design.png',
        ])->assertStatus(503);
    }

    public function test_webhook_rejects_an_invalid_signature(): void
    {
        [, , $brand] = $this->makeWorkspace();

        $this->withHeader('X-Canva-Signature', 'wrong-secret')
            ->postJson(route('canva.webhook', ['brand' => $brand->id]), [
                'export_url' => 'https://export-download.canva.com/design.png',
            ])
            ->assertUnauthorized();
    }

    public function test_webhook_rejects_an_unsafe_export_address(): void
    {
        [, , $brand] = $this->makeWorkspace();
        Http::fake();

        $this->withHeader('X-Canva-Signature', self::SECRET)
            ->postJson(route('canva.webhook', ['brand' => $brand->id]), [
                'export_url' => 'https://127.0.0.1/private.png',
            ])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Canva export address is not allowed.');

        Http::assertNothingSent();
    }

    public function test_webhook_returns_failure_when_export_download_fails(): void
    {
        [, , $brand] = $this->makeWorkspace();
        $this->allowExportUrl();
        Http::fake(['*' => Http::response('Unavailable', 503)]);

        $this->withHeader('X-Canva-Signature', self::SECRET)
            ->postJson(route('canva.webhook', ['brand' => $brand->id]), [
                'export_url' => 'https://export-download.canva.com/design.png',
            ])
            ->assertStatus(502)
            ->assertJsonPath('message', 'Canva export could not be downloaded.');
    }

    public function test_webhook_rejects_non_image_content(): void
    {
        [, , $brand] = $this->makeWorkspace();
        $this->allowExportUrl();
        Http::fake(['*' => Http::response('<html>Not an image</html>', 200)]);

        $this->withHeader('X-Canva-Signature', self::SECRET)
            ->postJson(route('canva.webhook', ['brand' => $brand->id]), [
                'export_url' => 'https://export-download.canva.com/design.png',
            ])
            ->assertUnprocessable();

        $this->assertDatabaseCount('media_files', 0);
    }

    public function test_verified_webhook_stores_image_with_owner_user_id(): void
    {
        Storage::fake('public');
        [, $owner, $brand] = $this->makeWorkspace();
        $this->allowExportUrl();
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
        Http::fake(['*' => Http::response($png, 200, ['Content-Type' => 'image/png'])]);

        $this->withHeader('X-Canva-Signature', self::SECRET)
            ->postJson(route('canva.webhook', ['brand' => $brand->id]), [
                'export_url' => 'https://export-download.canva.com/design.png',
            ])
            ->assertCreated()
            ->assertJson(['ok' => true]);

        $media = $brand->mediaFiles()->sole();

        $this->assertSame($owner->id, $media->uploaded_by);
        $this->assertSame('image/png', $media->mime_type);
        Storage::disk('public')->assertExists($media->storage_path);
    }

    public function test_public_url_guard_rejects_private_and_unapproved_hosts(): void
    {
        $guard = app(PublicUrlGuard::class);

        $this->assertFalse($guard->allows('https://127.0.0.1/file.png'));
        $this->assertFalse($guard->allows('http://8.8.8.8/file.png'));
        $this->assertFalse($guard->allows(
            'https://example.com/file.png',
            ['export-download.canva.com'],
        ));
    }

    private function allowExportUrl(): void
    {
        $mock = $this->mock(PublicUrlGuard::class);
        $mock->shouldReceive('allows')
            ->once()
            ->with('https://export-download.canva.com/design.png', ['export-download.canva.com'])
            ->andReturnTrue();
    }

    /**
     * @return array{Workspace, User, Brand}
     */
    private function makeWorkspace(): array
    {
        $workspace = Workspace::create([
            'name' => 'Canva Co',
            'slug' => 'canva-co',
            'owner_email' => 'owner@canva.test',
            'country' => 'NG',
            'timezone' => 'Africa/Lagos',
            'plan' => 'pro',
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trialing',
            'language' => 'en',
        ]);
        $owner = User::create([
            'workspace_id' => $workspace->id,
            'name' => 'Owner',
            'email' => 'owner@canva.test',
            'password' => bcrypt('secret'),
            'role' => 'owner',
        ]);
        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'Canva Brand',
            'slug' => 'canva-brand',
            'language' => 'en',
        ]);

        return [$workspace, $owner, $brand];
    }
}
