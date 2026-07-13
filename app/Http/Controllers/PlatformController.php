<?php

namespace App\Http\Controllers;

use App\Models\PlatformConnection;
use App\Services\Platforms\PlatformConnectionService;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    public function __construct(
        private readonly PlatformConnectionService $service
    ) {}

    /**
     * Connections dashboard — shows all 5 platforms with status.
     */
    public function index()
    {
        $brand = currentBrand();

        // All connections for this brand — scoped by brand_id
        $connections = PlatformConnection::where('brand_id', $brand->id)
            ->get()
            ->keyBy('platform');

        $platforms = [
            'linkedin' => ['name' => 'LinkedIn',   'color' => '#0077B5', 'icon' => 'linkedin'],
            'twitter' => ['name' => 'X (Twitter)', 'color' => '#000000', 'icon' => 'twitter'],
            'facebook' => ['name' => 'Facebook',   'color' => '#1877F2', 'icon' => 'facebook'],
            'instagram' => ['name' => 'Instagram',  'color' => '#E4405F', 'icon' => 'instagram'],
            'threads' => ['name' => 'Threads',    'color' => '#000000', 'icon' => 'threads'],
        ];

        return view('connections.index', compact('brand', 'connections', 'platforms'));
    }

    /**
     * Initiate OAuth for a platform — redirects user to the provider.
     */
    public function connect(string $brand, string $platform)
    {
        $this->validatePlatform($platform);

        $brandModel = currentBrand();
        $authUrl = $this->service->buildAuthUrl($platform, $brandModel);

        // Store brand slug in session for post-callback redirect
        session(['oauth_brand_slug' => $brand]);

        return redirect($authUrl);
    }

    /**
     * OAuth callback — fixed URL registered with each provider.
     * Not prefixed with {brand} because providers require a static redirect URI.
     */
    public function callback(Request $request, string $platform)
    {
        $this->validatePlatform($platform);
        $brandSlug = session()->pull('oauth_brand_slug');
        $state = $request->input('state');

        try {
            if (! is_string($state) || $state === '') {
                abort(422, 'Invalid OAuth state.');
            }

            // Handle provider errors (user denied access)
            if ($request->has('error')) {
                $brand = $this->service->handleCancelledCallback($platform, $state);

                return redirect()
                    ->route('connections', ['brand' => $brand->slug])
                    ->with('error', 'Connection cancelled. You can try again anytime.');
            }

            $code = $request->input('code');

            if (! is_string($code) || $code === '') {
                abort(422, 'Missing connection code.');
            }

            $connection = $this->service->handleCallback($platform, $code, $state);

            return redirect()
                ->route('connections', ['brand' => $connection->brand->slug])
                ->with('success', "{$connection->platform_username} connected successfully.");
        } catch (\Exception $e) {
            report($e);

            if (! is_string($brandSlug) || $brandSlug === '') {
                return redirect()
                    ->route('home')
                    ->with('error', 'Connection failed. Please try again.');
            }

            return redirect()
                ->route('connections', ['brand' => $brandSlug])
                ->with('error', 'Connection failed. Please check your credentials and try again.');
        }
    }

    /**
     * Disconnect a platform — deletes the connection record.
     */
    public function disconnect(Request $request, string $brand, string $platform)
    {
        $this->validatePlatform($platform);

        $brandModel = currentBrand();
        $this->service->disconnect($brandModel, $platform);

        return redirect()
            ->route('connections', ['brand' => $brand])
            ->with('success', ucfirst($platform).' disconnected.');
    }

    private function validatePlatform(string $platform): void
    {
        $valid = ['linkedin', 'twitter', 'facebook', 'instagram', 'threads'];
        if (! in_array($platform, $valid)) {
            abort(404);
        }
    }
}
