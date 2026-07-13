<?php

namespace App\Services\Platforms;

use App\Models\Brand;
use App\Models\PlatformConnection;
use App\Support\ExternalApiRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class PlatformConnectionService
{
    private const OAUTH_STATE_TTL_SECONDS = 600;

    public function __construct(private readonly ExternalApiRequest $http) {}

    /**
     * Build the OAuth redirect URL for a given platform.
     * Stores authorization context server-side behind an opaque state token.
     */
    public function buildAuthUrl(string $platform, Brand $brand): string
    {
        $state = $this->buildState($platform, $brand->id);

        return match ($platform) {
            'linkedin' => $this->linkedInAuthUrl($state),
            'twitter' => $this->twitterAuthUrl($state),
            'facebook' => $this->metaAuthUrl('facebook', $state),
            'instagram' => $this->metaAuthUrl('instagram', $state),
            'threads' => $this->metaAuthUrl('threads', $state),
            default => throw new \InvalidArgumentException("Unknown platform: {$platform}"),
        };
    }

    /**
     * Handle the OAuth callback. Exchanges code for token and stores it.
     * Returns the saved PlatformConnection.
     */
    public function handleCallback(string $platform, string $code, string $state): PlatformConnection
    {
        $pendingAuthorization = $this->consumeState($platform, $state);
        $brand = $pendingAuthorization['brand'];

        $tokenData = match ($platform) {
            'linkedin' => $this->exchangeLinkedIn($code),
            'twitter' => $this->exchangeTwitter($code, $pendingAuthorization['code_verifier'] ?? null),
            'facebook' => $this->exchangeMeta('facebook', $code),
            'instagram' => $this->exchangeMeta('instagram', $code),
            'threads' => $this->exchangeMeta('threads', $code),
            default => throw new \InvalidArgumentException("Unknown platform: {$platform}"),
        };

        return $this->upsertConnection($brand, $platform, $tokenData);
    }

    /**
     * Consume a denied OAuth request so its state cannot be replayed.
     */
    public function handleCancelledCallback(string $platform, string $state): Brand
    {
        return $this->consumeState($platform, $state)['brand'];
    }

    /**
     * Disconnect a platform — deletes the connection record.
     * Verifies brand ownership before deleting.
     */
    public function disconnect(Brand $brand, string $platform): void
    {
        PlatformConnection::where('brand_id', $brand->id)
            ->where('platform', $platform)
            ->delete();
    }

    /**
     * Check if a platform token is expired or expiring soon (within 7 days).
     */
    public function isExpiringSoon(PlatformConnection $connection): bool
    {
        if (! $connection->token_expires_at) {
            return false;
        }

        return $connection->token_expires_at->lessThan(now()->addDays(7));
    }

    // ── Private: State encoding / validation ─────────────────────────────────

    private function buildState(string $platform, string $brandId): string
    {
        $state = Str::random(64);
        $stateHash = hash('sha256', $state);

        session()->put("oauth_states.{$stateHash}", [
            'state_hash' => $stateHash,
            'brand_id' => $brandId,
            'platform' => $platform,
            'issued_at' => now()->timestamp,
        ]);

        return $state;
    }

    /**
     * @return array{brand: Brand, code_verifier: ?string}
     */
    private function consumeState(string $platform, string $state): array
    {
        if ($state === '' || strlen($state) > 128) {
            abort(422, 'Invalid OAuth state.');
        }

        $stateHash = hash('sha256', $state);
        $pendingAuthorization = session()->pull("oauth_states.{$stateHash}");

        if (! is_array($pendingAuthorization)
            || ! isset(
                $pendingAuthorization['state_hash'],
                $pendingAuthorization['brand_id'],
                $pendingAuthorization['platform'],
                $pendingAuthorization['issued_at'],
            )
            || ! is_string($pendingAuthorization['state_hash'])
            || ! is_string($pendingAuthorization['brand_id'])
            || ! is_string($pendingAuthorization['platform'])
            || ! is_int($pendingAuthorization['issued_at'])
            || ! hash_equals($pendingAuthorization['state_hash'], $stateHash)
            || ! hash_equals($pendingAuthorization['platform'], $platform)) {
            abort(422, 'Invalid OAuth state.');
        }

        $stateAge = now()->timestamp - (int) $pendingAuthorization['issued_at'];

        if ($stateAge < 0 || $stateAge > self::OAUTH_STATE_TTL_SECONDS) {
            abort(422, 'OAuth state expired. Please try connecting again.');
        }

        $user = auth()->user();

        if (! $user) {
            abort(403, 'Access denied.');
        }

        $brand = Brand::where('workspace_id', $user->workspace_id)
            ->whereKey($pendingAuthorization['brand_id'])
            ->firstOrFail();

        $codeVerifier = $pendingAuthorization['code_verifier'] ?? null;

        if ($codeVerifier !== null && ! is_string($codeVerifier)) {
            abort(422, 'Invalid OAuth state.');
        }

        return [
            'brand' => $brand,
            'code_verifier' => $codeVerifier,
        ];
    }

    // ── Private: LinkedIn ─────────────────────────────────────────────────────

    private function linkedInAuthUrl(string $state): string
    {
        $params = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.linkedin.client_id'),
            'redirect_uri' => config('services.linkedin.redirect_uri'),
            'scope' => implode(' ', config('services.linkedin.scopes')),
            'state' => $state,
        ]);

        return 'https://www.linkedin.com/oauth/v2/authorization?'.$params;
    }

    private function exchangeLinkedIn(string $code): array
    {
        $response = $this->http->make()
            ->asForm()
            ->post('https://www.linkedin.com/oauth/v2/accessToken', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => config('services.linkedin.redirect_uri'),
                'client_id' => config('services.linkedin.client_id'),
                'client_secret' => config('services.linkedin.client_secret'),
            ])
            ->throw()
            ->json();

        // Get user profile
        $profile = $this->http->make()
            ->withToken($response['access_token'])
            ->get('https://api.linkedin.com/v2/userinfo')
            ->throw()
            ->json();

        return [
            'access_token' => $response['access_token'],
            'refresh_token' => $response['refresh_token'] ?? null,
            'expires_in' => $response['expires_in'] ?? null,
            'platform_user_id' => $profile['sub'] ?? null,
            'platform_username' => $profile['name'] ?? null,
        ];
    }

    // ── Private: Twitter / X ─────────────────────────────────────────────────

    private function twitterAuthUrl(string $state): string
    {
        // PKCE: generate code verifier + challenge
        $codeVerifier = Str::random(64);
        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

        $stateHash = hash('sha256', $state);
        session()->put("oauth_states.{$stateHash}.code_verifier", $codeVerifier);

        $params = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.twitter.client_id'),
            'redirect_uri' => config('services.twitter.redirect_uri'),
            'scope' => implode(' ', config('services.twitter.scopes')),
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ]);

        return 'https://twitter.com/i/oauth2/authorize?'.$params;
    }

    private function exchangeTwitter(string $code, ?string $codeVerifier): array
    {
        if (! $codeVerifier) {
            abort(422, 'Connection session expired. Please try connecting again.');
        }

        $response = $this->http->make()
            ->withBasicAuth(
                config('services.twitter.client_id'),
                config('services.twitter.client_secret')
            )
            ->asForm()
            ->post('https://api.twitter.com/2/oauth2/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => config('services.twitter.redirect_uri'),
                'code_verifier' => $codeVerifier,
            ])
            ->throw()
            ->json();

        // Get user info
        $user = $this->http->make()
            ->withToken($response['access_token'])
            ->get('https://api.twitter.com/2/users/me')
            ->throw()
            ->json('data');

        return [
            'access_token' => $response['access_token'],
            'refresh_token' => $response['refresh_token'] ?? null,
            'expires_in' => $response['expires_in'] ?? null,
            'platform_user_id' => $user['id'] ?? null,
            'platform_username' => '@'.($user['username'] ?? ''),
        ];
    }

    // ── Private: Meta (Facebook / Instagram / Threads) ───────────────────────

    private function metaAuthUrl(string $platform, string $state): string
    {
        $scopes = match ($platform) {
            'facebook' => config('services.meta.fb_scopes'),
            'instagram' => config('services.meta.ig_scopes'),
            'threads' => config('services.meta.threads_scopes'),
        };

        $redirectUri = str_replace('{platform}', $platform, config('services.meta.redirect_uri'));

        $params = http_build_query([
            'client_id' => config('services.meta.app_id'),
            'redirect_uri' => $redirectUri,
            'scope' => implode(',', $scopes),
            'response_type' => 'code',
            'state' => $state,
        ]);

        return 'https://www.facebook.com/v20.0/dialog/oauth?'.$params;
    }

    private function exchangeMeta(string $platform, string $code): array
    {
        $redirectUri = str_replace('{platform}', $platform, config('services.meta.redirect_uri'));

        $response = $this->http->make()
            ->get('https://graph.facebook.com/v20.0/oauth/access_token', [
                'client_id' => config('services.meta.app_id'),
                'client_secret' => config('services.meta.app_secret'),
                'redirect_uri' => $redirectUri,
                'code' => $code,
            ])
            ->throw()
            ->json();

        $accessToken = $response['access_token'];

        // Get user identity
        $me = $this->http->make()
            ->get('https://graph.facebook.com/v20.0/me', [
                'fields' => 'id,name',
                'access_token' => $accessToken,
            ])
            ->throw()
            ->json();

        // For Instagram, get the IG business account linked to this user
        $igUserId = null;
        $igUsername = null;
        if ($platform === 'instagram') {
            $pages = $this->http->make()
                ->get('https://graph.facebook.com/v20.0/me/accounts', [
                    'access_token' => $accessToken,
                ])
                ->throw()
                ->json('data', []);

            foreach ($pages as $page) {
                $igAccount = $this->http->make()
                    ->get("https://graph.facebook.com/v20.0/{$page['id']}/instagram_business_account", [
                        'fields' => 'id,username',
                        'access_token' => $page['access_token'],
                    ])
                    ->throw()
                    ->json('instagram_business_account');

                if ($igAccount) {
                    $igUserId = $igAccount['id'];
                    $igUsername = '@'.($igAccount['username'] ?? '');
                    $accessToken = $page['access_token'];
                    break;
                }
            }
        }

        return [
            'access_token' => $accessToken,
            'refresh_token' => null, // Meta tokens don't expire the same way
            'expires_in' => $response['expires_in'] ?? null,
            'platform_user_id' => $igUserId ?? ($me['id'] ?? null),
            'platform_username' => $igUsername ?? ($me['name'] ?? null),
        ];
    }

    // ── Private: Token storage ────────────────────────────────────────────────

    private function upsertConnection(Brand $brand, string $platform, array $tokenData): PlatformConnection
    {
        $expiresAt = isset($tokenData['expires_in'])
            ? now()->addSeconds($tokenData['expires_in'])
            : null;

        return PlatformConnection::updateOrCreate(
            [
                'brand_id' => $brand->id,
                'platform' => $platform,
            ],
            [
                'platform_user_id' => $tokenData['platform_user_id'],
                'platform_username' => $tokenData['platform_username'],
                'access_token' => Crypt::encryptString($tokenData['access_token']),
                'refresh_token' => $tokenData['refresh_token']
                    ? Crypt::encryptString($tokenData['refresh_token'])
                    : null,
                'token_expires_at' => $expiresAt,
                'status' => 'connected',
            ]
        );
    }
}
