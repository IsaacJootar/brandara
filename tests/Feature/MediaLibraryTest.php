<?php

namespace Tests\Feature;

use App\Livewire\Media\MediaLibrary;
use App\Models\Brand;
use App\Models\MediaFile;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Media\MediaLibraryService;
use App\Services\Media\MediaUploadException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    private function makeWorkspace(): array
    {
        $workspace = Workspace::create([
            'name' => 'Media Co', 'slug' => 'media-co',
            'owner_email' => 'owner@media.test', 'country' => 'NG',
            'timezone' => 'Africa/Lagos', 'plan' => 'starter',
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trialing', 'language' => 'en',
        ]);
        $user = User::create([
            'workspace_id' => $workspace->id, 'name' => 'Owner',
            'email' => 'owner@media.test', 'password' => bcrypt('secret'),
            'role' => 'owner',
        ]);
        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'Media Brand', 'slug' => 'media-brand',
            'language' => 'en',
        ]);

        return [$user, $brand];
    }

    // ── Route ─────────────────────────────────────────────────────────────────

    public function test_media_page_loads_for_authenticated_user(): void
    {
        [$user, $brand] = $this->makeWorkspace();
        $this->actingAs($user);

        $response = $this->get(route('media', ['brand' => $brand->slug]));

        $response->assertStatus(200);
        $response->assertSee('Media');
    }

    public function test_media_page_requires_auth(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        $response = $this->get(route('media', ['brand' => $brand->slug]));

        $response->assertRedirect();
    }

    // ── MediaLibraryService ───────────────────────────────────────────────────

    public function test_service_stores_image_and_creates_record(): void
    {
        Storage::fake('local');
        [$user, $brand] = $this->makeWorkspace();

        $file = UploadedFile::fake()->image('photo.jpg', 800, 600);
        $service = app(MediaLibraryService::class);

        $media = $service->store($file, $brand, $user->id);

        $this->assertDatabaseHas('media_files', [
            'brand_id' => $brand->id,
            'filename' => 'photo.jpg',
            'mime_type' => 'image/jpeg',
        ]);
        Storage::assertExists($media->storage_path);
    }

    public function test_service_rejects_invalid_file_type(): void
    {
        Storage::fake('local');
        [$user, $brand] = $this->makeWorkspace();

        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');
        $service = app(MediaLibraryService::class);

        $this->expectException(MediaUploadException::class);
        $service->store($file, $brand, $user->id);
    }

    public function test_service_deletes_file_and_record(): void
    {
        Storage::fake('local');
        [$user, $brand] = $this->makeWorkspace();

        $file = UploadedFile::fake()->image('todelete.jpg', 400, 300);
        $service = app(MediaLibraryService::class);
        $media = $service->store($file, $brand, $user->id);

        $path = $media->storage_path;
        $service->delete($media);

        $this->assertDatabaseMissing('media_files', ['id' => $media->id]);
        Storage::assertMissing($path);
    }

    public function test_platform_check_warns_on_oversized_instagram_image(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        $media = MediaFile::create([
            'brand_id'    => $brand->id,
            'uploaded_by' => $user->id,
            'filename'    => 'big.jpg',
            'storage_path' => 'brands/x/media/big.jpg',
            'mime_type'   => 'image/jpeg',
            'file_size_kb' => 10000,
            'width'       => 1200,
            'height'      => 900,
            'tags'        => [],
        ]);

        $result = app(MediaLibraryService::class)->platformCheck($media, 'instagram');

        $this->assertFalse($result['ok']);
        $this->assertNotEmpty($result['warnings']);
    }

    public function test_platform_check_passes_valid_image(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        $media = MediaFile::create([
            'brand_id'    => $brand->id,
            'uploaded_by' => $user->id,
            'filename'    => 'ok.jpg',
            'storage_path' => 'brands/x/media/ok.jpg',
            'mime_type'   => 'image/jpeg',
            'file_size_kb' => 500,
            'width'       => 1200,
            'height'      => 900,
            'tags'        => [],
        ]);

        $result = app(MediaLibraryService::class)->platformCheck($media, 'instagram');

        $this->assertTrue($result['ok']);
        $this->assertEmpty($result['warnings']);
    }

    public function test_storage_used_sums_correctly(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        MediaFile::create(['brand_id' => $brand->id, 'uploaded_by' => $user->id, 'filename' => 'a.jpg', 'storage_path' => 'a', 'mime_type' => 'image/jpeg', 'file_size_kb' => 200, 'tags' => []]);
        MediaFile::create(['brand_id' => $brand->id, 'uploaded_by' => $user->id, 'filename' => 'b.jpg', 'storage_path' => 'b', 'mime_type' => 'image/jpeg', 'file_size_kb' => 350, 'tags' => []]);

        $used = app(MediaLibraryService::class)->storageUsedKb($brand);

        $this->assertEquals(550, $used);
    }

    // ── Livewire component ────────────────────────────────────────────────────

    public function test_component_mounts_for_brand(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        Livewire::actingAs($user)
            ->test(MediaLibrary::class, ['brand' => $brand])
            ->assertSet('brandId', $brand->id)
            ->assertSet('uploadStatus', 'idle');
    }

    public function test_component_shows_uploaded_files(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        MediaFile::create([
            'brand_id' => $brand->id, 'uploaded_by' => $user->id,
            'filename' => 'hero.jpg', 'storage_path' => 'brands/x/media/hero.jpg',
            'mime_type' => 'image/jpeg', 'file_size_kb' => 100, 'tags' => [],
        ]);

        Livewire::actingAs($user)
            ->test(MediaLibrary::class, ['brand' => $brand])
            ->assertSee('hero.jpg');
    }

    public function test_component_upload_validates_required(): void
    {
        [$user, $brand] = $this->makeWorkspace();

        Livewire::actingAs($user)
            ->test(MediaLibrary::class, ['brand' => $brand])
            ->call('upload')
            ->assertHasErrors(['uploads']);
    }

    public function test_component_delete_removes_file(): void
    {
        Storage::fake('local');
        [$user, $brand] = $this->makeWorkspace();

        $media = MediaFile::create([
            'brand_id' => $brand->id, 'uploaded_by' => $user->id,
            'filename' => 'gone.jpg', 'storage_path' => 'brands/x/media/gone.jpg',
            'mime_type' => 'image/jpeg', 'file_size_kb' => 50, 'tags' => [],
        ]);

        Livewire::actingAs($user)
            ->test(MediaLibrary::class, ['brand' => $brand])
            ->call('deleteFile', $media->id);

        $this->assertDatabaseMissing('media_files', ['id' => $media->id]);
    }
}
