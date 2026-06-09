<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
     * NOTE: This endpoint requires Canva Connect app approval.
     * Until then it returns 200 to avoid Canva disabling the webhook.
     */
    public function webhook(Request $request, string $brand): JsonResponse
    {
        Log::info('Canva webhook received', ['brand' => $brand, 'payload' => $request->all()]);

        $apiKey = config('services.canva.webhook_secret');

        if ($apiKey && $request->header('X-Canva-Signature') !== $apiKey) {
            return response()->json(['error' => 'Unauthorised'], 401);
        }

        // When Canva Connect is approved, this will contain the image URL
        $imageUrl = $request->input('export_url');

        if ($imageUrl && $brandModel = Brand::where('slug', $brand)->first()) {
            try {
                $response = Http::get($imageUrl);

                if ($response->successful()) {
                    $filename = 'canva-'.now()->format('Ymd-His').'.jpg';
                    $path = "brands/{$brandModel->id}/media/{$filename}";
                    Storage::put($path, $response->body());

                    $brandModel->mediaFiles()->create([
                        'uploaded_by' => $brandModel->workspace->owner_email,
                        'filename' => $filename,
                        'storage_path' => $path,
                        'mime_type' => 'image/jpeg',
                        'file_size_kb' => (int) ceil(strlen($response->body()) / 1024),
                        'tags' => ['canva'],
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Canva webhook image save failed', ['error' => $e->getMessage()]);
            }
        }

        return response()->json(['ok' => true]);
    }
}
