<?php

namespace App\Services\Media;

use App\Models\Brand;
use App\Models\MediaFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;

class MediaLibraryService
{
    private const MAX_DIMENSION = 2048;

    private const PLATFORM_LIMITS = [
        'instagram' => ['max_kb' => 8192,  'types' => ['image/jpeg', 'image/png']],
        'linkedin' => ['max_kb' => 20480, 'types' => ['image/jpeg', 'image/png', 'image/gif', 'video/mp4']],
        'twitter' => ['max_kb' => 5120,  'types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp']],
        'facebook' => ['max_kb' => 10240, 'types' => ['image/jpeg', 'image/png', 'image/gif']],
        'threads' => ['max_kb' => 10240, 'types' => ['image/jpeg', 'image/png']],
    ];

    /**
     * Store, compress, and register an uploaded file in the media library.
     */
    public function store(UploadedFile $file, Brand $brand, string $uploadedBy, ?string $altText = null): MediaFile
    {
        $this->validateFile($file);

        $isImage = str_starts_with($file->getMimeType(), 'image/');
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = Str::uuid().'.'.$extension;
        $directory = "brands/{$brand->id}/media";
        $path = "{$directory}/{$filename}";

        if ($isImage) {
            $this->storeCompressedImage($file, $path);
            [$width, $height] = $this->imageDimensions($path);
        } else {
            Storage::put($path, file_get_contents($file->getRealPath()));
            $width = null;
            $height = null;
        }

        $sizeKb = (int) ceil(Storage::size($path) / 1024);

        return MediaFile::create([
            'brand_id' => $brand->id,
            'uploaded_by' => $uploadedBy,
            'filename' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size_kb' => $sizeKb,
            'width' => $width,
            'height' => $height,
            'alt_text' => $altText,
            'tags' => [],
        ]);
    }

    /**
     * Delete a media file — storage + DB record.
     */
    public function delete(MediaFile $file): void
    {
        Storage::delete($file->storage_path);
        $file->delete();
    }

    /**
     * Return URL for a stored file.
     */
    public function url(MediaFile $file): string
    {
        return Storage::url($file->storage_path);
    }

    /**
     * Validate file type and global 20 MB cap.
     *
     * @throws MediaUploadException
     */
    public function validateFile(UploadedFile $file): void
    {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'video/mp4'];

        if (! in_array($file->getMimeType(), $allowed)) {
            throw new MediaUploadException('Only JPG, PNG, GIF, WEBP images and MP4 videos are allowed.');
        }

        if ($file->getSize() > 20 * 1024 * 1024) {
            throw new MediaUploadException('File is too large. Maximum size is 20 MB.');
        }
    }

    /**
     * Check a stored file against a specific platform's requirements.
     *
     * @return array{ok: bool, warnings: string[]}
     */
    public function platformCheck(MediaFile $file, string $platform): array
    {
        $limits = self::PLATFORM_LIMITS[$platform] ?? null;

        if (! $limits) {
            return ['ok' => true, 'warnings' => []];
        }

        $warnings = [];

        if (! in_array($file->mime_type, $limits['types'])) {
            $warnings[] = ucfirst($platform).' does not support '.$file->mime_type.' files.';
        }

        if ($file->file_size_kb > $limits['max_kb']) {
            $mb = $limits['max_kb'] / 1024;
            $warnings[] = "This file is too large for {$platform}. Maximum allowed is {$mb} MB.";
        }

        if ($platform === 'instagram' && $file->width && $file->width < 320) {
            $warnings[] = 'Instagram requires images to be at least 320px wide.';
        }

        return ['ok' => empty($warnings), 'warnings' => $warnings];
    }

    /**
     * Total storage used by a brand in KB.
     */
    public function storageUsedKb(Brand $brand): int
    {
        return (int) MediaFile::where('brand_id', $brand->id)->sum('file_size_kb');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function storeCompressedImage(UploadedFile $file, string $storagePath): void
    {
        $manager = new ImageManager(new Driver);
        $image = $manager->decode(file_get_contents($file->getRealPath()));

        if ($image->width() > self::MAX_DIMENSION || $image->height() > self::MAX_DIMENSION) {
            $image->scaleDown(self::MAX_DIMENSION, self::MAX_DIMENSION);
        }

        Storage::put($storagePath, (string) $image->encode(new JpegEncoder(82)));
    }

    /**
     * @return array{int, int}
     */
    private function imageDimensions(string $storagePath): array
    {
        $manager = new ImageManager(new Driver);
        $image = $manager->decode(Storage::get($storagePath));

        return [$image->width(), $image->height()];
    }
}
