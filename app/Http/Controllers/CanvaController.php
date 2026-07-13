<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Support\PublicUrlGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CanvaController extends Controller
{
    /**
     * Generate a Canva deep link that opens a new design with pre-filled text.
     * This works without an API key — uses Canva's public design URL scheme.
     */
    public function link(Request $request): JsonResponse
    {
        $request->validate([
            'text' => ['required', 'string', 'max:5000'],
            'title' => ['nullable', 'string', 'max:200'],
        ]);

        // Canva "create design" deep link — opens Canva with a blank template
        // User pastes the generated text. Full API pre-population requires Canva Connect app approval.
        $canvaUrl = 'https://www.canva.com/design/create?';

        // Pass the text as a note via Canva's URL scheme
        $params = http_build_query([
            'type' => 'social_media',
        ]);

        return response()->json([
            'url' => $canvaUrl.$params,
            'text' => $request->string('text')->toString(),
        ]);
    }

    /**
     * Receive a finished image from Canva via webhook.
     * Stores it in the brand's media library.
     *
     * This endpoint remains unavailable until a webhook secret is configured.
     */
    public function webhook(Request $request, string $brand, PublicUrlGuard $urlGuard): JsonResponse
    {
        $webhookSecret = (string) config('services.canva.webhook_secret', '');

        if ($webhookSecret === '') {
            Log::critical('Canva webhook rejected because no secret is configured');

            return response()->json(['message' => 'Canva connection is not configured.'], 503);
        }

        $signature = (string) $request->header('X-Canva-Signature', '');

        if ($signature === '' || ! hash_equals($webhookSecret, $signature)) {
            return response()->json(['message' => 'Canva request could not be verified.'], 401);
        }

        $brandModel = Brand::query()->find($brand);

        if (! $brandModel) {
            return response()->json(['message' => 'Brand was not found.'], 404);
        }

        $imageUrl = $request->input('export_url');
        $allowedHosts = config('services.canva.export_hosts', []);

        if (! is_string($imageUrl) || strlen($imageUrl) > 2048 || ! $urlGuard->allows($imageUrl, $allowedHosts)) {
            return response()->json(['message' => 'Canva export address is not allowed.'], 422);
        }

        $owner = $brandModel->workspace->users()->where('role', 'owner')->first();

        if (! $owner) {
            Log::error('Canva webhook could not find workspace owner', ['brand_id' => $brandModel->id]);

            return response()->json(['message' => 'Brand owner could not be found.'], 422);
        }

        $path = null;

        try {
            $response = Http::connectTimeout(5)
                ->timeout(15)
                ->retry(2, 200, throw: false)
                ->withOptions(['allow_redirects' => false])
                ->get($imageUrl);

            if (! $response->successful()) {
                Log::warning('Canva export download failed', [
                    'brand_id' => $brandModel->id,
                    'status' => $response->status(),
                ]);

                return response()->json(['message' => 'Canva export could not be downloaded.'], 502);
            }

            $body = $response->body();
            $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->buffer($body);
            $extensions = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
            ];

            if (! is_string($mimeType) || ! isset($extensions[$mimeType]) || strlen($body) > 20 * 1024 * 1024) {
                return response()->json(['message' => 'Canva export is not a supported image.'], 422);
            }

            $filename = 'canva-'.Str::uuid().'.'.$extensions[$mimeType];
            $path = "brands/{$brandModel->id}/media/{$filename}";

            if (! Storage::disk('public')->put($path, $body)) {
                throw new \RuntimeException('Canva export could not be written to storage.');
            }

            $brandModel->mediaFiles()->create([
                'uploaded_by' => $owner->id,
                'filename' => $filename,
                'storage_path' => $path,
                'mime_type' => $mimeType,
                'file_size_kb' => (int) ceil(strlen($body) / 1024),
                'tags' => ['canva'],
            ]);
        } catch (\Throwable $exception) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }

            Log::error('Canva webhook image save failed', [
                'brand_id' => $brandModel->id,
                'exception' => $exception::class,
            ]);

            return response()->json(['message' => 'Canva export could not be saved.'], 502);
        }

        return response()->json(['ok' => true], 201);
    }
}
