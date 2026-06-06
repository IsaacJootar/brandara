# Brandara — API Integrations

## Complete .env template

```env
# App
APP_NAME=Brandara
APP_ENV=local
APP_KEY=                          # php artisan key:generate
APP_URL=http://localhost

# Database (local dev)
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

# Database (production — Supabase)
# DB_CONNECTION=pgsql
# DATABASE_URL=postgresql://...

# Multi-tenancy
TENANCY_DATABASE_AUTO_DELETE=false

# Queue (local — no Redis needed)
QUEUE_CONNECTION=database

# Queue (production — Horizon requires Redis)
# QUEUE_CONNECTION=redis
# REDIS_HOST=
# REDIS_PASSWORD=
# REDIS_PORT=6379

# Laravel Reverb (WebSockets)
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# AI — Anthropic
ANTHROPIC_API_KEY=sk-ant-...
ANTHROPIC_MODEL=claude-sonnet-4-5

# Email — Resend
MAIL_MAILER=resend
RESEND_API_KEY=re_...
MAIL_FROM_ADDRESS=hello@brandara.co
MAIL_FROM_NAME=Brandara

# SMS — Africa's Talking
AT_USERNAME=brandara
AT_API_KEY=
AT_SENDER_ID=Brandara

# Web Push Notifications
VAPID_PUBLIC_KEY=
VAPID_PRIVATE_KEY=
VAPID_SUBJECT=https://brandara.co

# Payments — Paystack
PAYSTACK_PUBLIC_KEY=pk_live_...
PAYSTACK_SECRET_KEY=sk_live_...
PAYSTACK_PAYMENT_URL=https://api.paystack.co

# Payments — Flutterwave
FLW_PUBLIC_KEY=FLWPUBK-...
FLW_SECRET_KEY=FLWSECK-...
FLW_SECRET_HASH=

# Social Platforms
LINKEDIN_CLIENT_ID=
LINKEDIN_CLIENT_SECRET=
TWITTER_CLIENT_ID=
TWITTER_CLIENT_SECRET=
META_APP_ID=                      # Covers Facebook + Instagram + Threads
META_APP_SECRET=

# Canva
CANVA_CLIENT_ID=
CANVA_CLIENT_SECRET=

# Storage
FILESYSTEM_DISK=local
```

---

## Anthropic Claude API

**Model:** `claude-sonnet-4-5`
**Rate limits:** Check dashboard. At 20 customers usage is minimal.
**Cost:** ~$0.001–$0.003 per post generation.

**Basic usage pattern:**
```php
use Anthropic\Laravel\Facades\Anthropic;

$response = Anthropic::messages()->create([
    'model' => config('services.anthropic.model'),
    'max_tokens' => 4096,
    'messages' => [
        ['role' => 'user', 'content' => $prompt]
    ]
]);

$content = $response->content[0]->text;
```

**All prompts live in** `docs/prompts/` — never hardcode prompts in PHP files.
Always load from the prompt service: `app/Services/AI/Prompts/`.

---

## LinkedIn API

**App setup:** developer.linkedin.com → Create app
**OAuth scopes:** `w_member_social`, `r_basicprofile`, `r_emailaddress`
**Token lifetime:** ~60 days. Refresh flow required.

**OAuth flow:**
1. Redirect to `https://www.linkedin.com/oauth/v2/authorization`
2. User grants permission
3. Receive `code` callback
4. Exchange for access token via `POST /oauth/v2/accessToken`
5. Store encrypted token in platform_connections

**Post endpoint:** `POST /v2/ugcPosts`
**Analytics endpoint:** `GET /v2/organizationalEntityShareStatistics`

---

## X (Twitter) API v2

**App setup:** developer.twitter.com → Create app → OAuth 2.0
**Scopes:** `tweet.read`, `tweet.write`, `users.read`, `offline.access`
**Free tier limit:** 1,500 tweets/month per app (sufficient for 20 customers)

**Post endpoint:** `POST /2/tweets`
**OAuth:** PKCE flow for user authentication

---

## Meta Graph API (Facebook + Instagram)

**App setup:** developers.facebook.com → Create app → Social Login + Instagram
**Facebook scopes:** `pages_manage_posts`, `pages_read_engagement`, `pages_show_list`
**Instagram scopes:** `instagram_basic`, `instagram_content_publish`, `instagram_manage_insights`

**Facebook post:** `POST /{page-id}/feed`
**Instagram post:** First create media container, then publish:
1. `POST /{ig-user-id}/media` → get creation_id
2. `POST /{ig-user-id}/media_publish` with creation_id

**Instagram requirement:** Image required for every post. Brandara validates this in the composer before scheduling.

---

## Paystack

**Docs:** paystack.com/docs
**Supported currencies:** NGN, GHS, KES, ZAR

**Initialise payment:**
```php
$response = Http::withHeaders([
    'Authorization' => 'Bearer ' . config('services.paystack.secret'),
])->post('https://api.paystack.co/transaction/initialize', [
    'email' => $user->email,
    'amount' => $amount * 100, // Paystack uses kobo
    'currency' => 'NGN',
    'plan' => $planCode,
    'callback_url' => route('billing.paystack.callback'),
]);
```

**Webhook:** `POST /api/webhooks/paystack`
Verify signature: `hash_hmac('sha512', $payload, $secret)`

---

## Flutterwave

**Docs:** developer.flutterwave.com
**Supported currencies:** NGN, GHS, KES, ZAR, USD, EUR, GBP + 30 more African currencies

**Payment flow:**
```php
$response = Http::withHeaders([
    'Authorization' => 'Bearer ' . config('services.flutterwave.secret'),
])->post('https://api.flutterwave.com/v3/payments', [
    'tx_ref' => uniqid('brandara_'),
    'amount' => $amount,
    'currency' => $currency,
    'redirect_url' => route('billing.flutterwave.callback'),
    'customer' => ['email' => $user->email, 'name' => $user->name],
    'customizations' => ['title' => 'Brandara ' . $planName],
]);
```

**Webhook:** `POST /api/webhooks/flutterwave`
Verify with `FLW_SECRET_HASH` header check.

**Routing logic:**
```php
// Nigerian users → Paystack
// All other African users → Flutterwave
$gateway = $user->workspace->country === 'NG' ? 'paystack' : 'flutterwave';
```

---

## Africa's Talking (SMS)

**Docs:** developers.africastalking.com
**Free sandbox:** use username `sandbox` for testing

```php
$AT = new AfricasTalking\SDK\AfricasTalking(
    config('services.at.username'),
    config('services.at.api_key')
);

$sms = $AT->sms();
$result = $sms->send([
    'to' => '+234XXXXXXXXXX',
    'message' => 'Your LinkedIn post failed. Open Brandara to fix it.',
    'from' => config('services.at.sender_id'),
]);
```

---

## Web Push Notifications — VAPID setup

```bash
# Generate VAPID keys (one time only)
php artisan webpush:vapid
# Adds VAPID_PUBLIC_KEY and VAPID_PRIVATE_KEY to .env

# Add to User model
use NotificationChannels\WebPush\HasPushSubscriptions;
class User extends Authenticatable {
    use HasPushSubscriptions;
}

# JavaScript to subscribe user (add to app.blade.php)
# Brandara registers service worker on login
```

**Service worker file:** `public/sw.js`
```js
self.addEventListener('push', function(event) {
    const data = event.data.json();
    self.registration.showNotification(data.title, {
        body: data.body,
        icon: '/images/brandara-icon-192.png',
        badge: '/images/brandara-badge.png',
        data: { url: data.action_url }
    });
});
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data.url));
});
```

---

## Canva API (carousel integration)

**Docs:** developers.canva.com
**Free tier:** Available for basic integration

Flow:
1. Generate carousel copy in Brandara
2. User clicks "Design in Canva"
3. Brandara passes carousel text + Brand Kit colours to Canva
4. Canva returns finished image via webhook
5. Image stored in Brandara Media Library

---

## Resend (email)

```php
// Uses Laravel's standard mail system
// Configured via MAIL_MAILER=resend
Mail::to($user)->send(new PostFailedMail($post));
```

Templates live in `resources/views/emails/`.
All subject lines must follow plain English rules from `docs/ui-rules.md`.
