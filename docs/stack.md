# Brandara — Complete Tech Stack

## Core framework

**Laravel 13** — PHP 8.3+. Full stack framework. All routing, database,
jobs, storage, email, and API integrations in one codebase.

```bash
composer create-project laravel/laravel brandara
cd brandara
php -v  # Must be 8.3+
```

---

## Frontend

| Package | Purpose | Install |
|---|---|---|
| Tailwind CSS | Utility-first CSS | `npm install -D tailwindcss` |
| DaisyUI | Pre-built Tailwind components | `npm install daisyui` |
| Alpine.js | Small JS interactions | CDN or `npm install alpinejs` |
| Livewire 3 | Complex interactive UI | `composer require livewire/livewire` |
| blade-heroicons | Icon set | `composer require blade-ui-kit/blade-heroicons` |

DaisyUI covers: buttons, modals, cards, tables, dropdowns, alerts, badges,
form inputs, navigation, and more. Use DaisyUI components before writing
any custom CSS.

---

## Multi-tenancy

```bash
composer require stancl/tenancy
php artisan tenancy:install
php artisan migrate
```

**How it works:**
- Central DB: stores workspace records only
- Tenant DB: all business data per workspace
- Routes in `routes/tenant.php` are auto-scoped
- Tenant identified by subdomain: `workspace-slug.brandara.co`

---

## Authentication

```bash
composer require laravel/breeze
php artisan breeze:install blade
```

Email + password at launch. Google OAuth in v2 via Laravel Socialite.

**Onboarding flow:**
1. Create workspace (name, email, country, timezone)
2. Set password
3. 7-day trial begins automatically
4. Connect first platform

---

## Background jobs — Laravel Horizon + Redis

```bash
composer require laravel/horizon
php artisan horizon:install
```

**Requires Redis.** Use Redis free tier on Railway or Render.

Horizon provides:
- Beautiful dashboard at `/horizon`
- Queue monitoring and throughput metrics
- Failed job management and retries
- Job metrics and wait times

Queue driver in production: `QUEUE_CONNECTION=redis`
Queue driver in local dev: `QUEUE_CONNECTION=database` (no Redis needed locally)

**Brandara jobs:**
- `PublishPostJob` — fires at scheduled time, calls platform API
- `RetryFailedPostJob` — 3-layer retry with backoff
- `RefreshPlatformTokenJob` — daily token health check
- `SendWeeklyReportJob` — Monday 8AM analytics digest

---

## Real-time — Laravel Reverb

```bash
composer require laravel/reverb
php artisan reverb:install
```

Self-hosted WebSocket server. Free. No third-party service needed.

**Powers:**
- Livewire real-time platform preview (updates as user types)
- Live notification counter badge
- Real-time publish status updates on the calendar
- Collaborative agency review notifications

Start server: `php artisan reverb:start`

---

## Web push notifications

```bash
composer require laravel-notification-channels/webpush
php artisan vendor:publish --provider="NotificationChannels\WebPush\WebPushServiceProvider"
php artisan migrate
php artisan webpush:vapid
```

**Integrates with Laravel's notification system:**
```php
public function via($notifiable) {
    return ['mail', 'vonage', WebPushChannel::class];
}
```

**Brandara push triggers:**
- Post failed to publish (immediate)
- Trial expiring in 3 days (scheduled)
- Platform token expired (immediate)
- Approval request received (immediate)
- Weekly results summary (Monday 8AM)

Works in Chrome, Firefox, Edge, Safari. No Firebase billing. Zero cost.

**Required .env:**
```
VAPID_PUBLIC_KEY=
VAPID_PRIVATE_KEY=
VAPID_SUBJECT=https://brandara.co
```

---

## Performance monitoring — Laravel Pulse

```bash
composer require laravel/pulse
php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider"
php artisan migrate
```

Dashboard at `/pulse`. Shows:
- Slow queries and request times
- Failed jobs and queue health
- Cache hit rates
- Exception tracking

Add to admin routes only — not publicly accessible.

---

## Development monitoring — Laravel Telescope

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

Dev-only. Shows all requests, queries, jobs, notifications, and logs.
Dashboard at `/telescope`. Never deploy to production.

---

## Laravel Boost — MCP server for Claude Code

```bash
composer require laravel/boost --dev
php artisan boost:install
```

Gives Claude Code real-time access to:
- Codebase structure and files
- Database schema and queries
- Application routes
- Artisan commands
- Error logs and exceptions
- Configuration values

**Enable in Claude Code:** Settings → MCP Servers → enable laravel-boost
This must be done at the start of every development session.

---

## AI — Anthropic Claude API

```bash
composer require anthropic-php/laravel
```

**Model:** `claude-sonnet-4-5` (cost-efficient, high quality)
**Max tokens:** 4096 per generation
**Usage:** Content generation, Brand Voice, campaign packs, smart comments, WhatsApp copy, AI Visibility queries

```
ANTHROPIC_API_KEY=sk-ant-...
ANTHROPIC_MODEL=claude-sonnet-4-5
```

Cost: ~$0.001–$0.003 per post generation. Under $3/month at 20 customers.

---

## Email — Resend

```bash
composer require resend/resend-laravel
```

**Free tier:** 3,000 emails/month. Set as default mail driver.

```
MAIL_MAILER=resend
RESEND_API_KEY=re_...
MAIL_FROM_ADDRESS=hello@brandara.co
MAIL_FROM_NAME=Brandara
```

---

## SMS — Africa's Talking

```bash
composer require africastalking/africastalking
```

Best SMS coverage across Nigeria, Ghana, Kenya, South Africa.
~$0.004 per SMS. Free sandbox for testing.

```
AT_USERNAME=brandara
AT_API_KEY=...
AT_SENDER_ID=Brandara
```

---

## Payments — Paystack + Flutterwave

**NO STRIPE. African users only.**

### Paystack
```bash
composer require unicodeveloper/laravel-paystack
```
Covers: NGN, GHS, KES, ZAR. Best for Nigerian market.
```
PAYSTACK_PUBLIC_KEY=pk_live_...
PAYSTACK_SECRET_KEY=sk_live_...
PAYSTACK_PAYMENT_URL=https://api.paystack.co
```

### Flutterwave
```bash
composer require kingflamez/laravelrave
```
Covers: 30+ African currencies. Pan-African coverage.
```
FLW_PUBLIC_KEY=FLWPUBK-...
FLW_SECRET_KEY=FLWSECK-...
FLW_SECRET_HASH=...
```

Both have webhook handlers for subscription events.
Payment routing: Nigerian users → Paystack first. All other African users → Flutterwave.

---

## File storage

**Local (development + early production):**
```
FILESYSTEM_DISK=local
```
Files stored at: `storage/app/tenants/{tenant_id}/media/`

**Production (when storage grows):**
Switch to Supabase Storage — one line config change. No code rewrite.

Image processing: `composer require intervention/image`
Compress all uploads. Max dimensions enforced per platform.

---

## Platform APIs

All use OAuth 2.0. No passwords stored. Tokens encrypted at rest.

| Platform | API | OAuth scopes needed |
|---|---|---|
| LinkedIn | LinkedIn API v2 | w_member_social, r_basicprofile |
| X (Twitter) | Twitter API v2 | tweet.read, tweet.write, users.read |
| Facebook | Meta Graph API | pages_manage_posts, pages_read_engagement |
| Instagram | Meta Graph API | instagram_basic, instagram_content_publish |
| Threads | Threads API | threads_basic, threads_content_publish |

TikTok: text toolkit only in v1. No API integration needed.
WhatsApp: copy generation only in v1. No API integration needed.

---

## Hosting — Render.com

- Free tier for Laravel web service
- **Region: Frankfurt, Europe** (closest to West Africa — critical for speed)
- PostgreSQL add-on: use Supabase instead (better free tier)
- Redis add-on: free tier available for Horizon

Deployment: git push to main → auto-deploy.

---

## Environment variables — complete list

See `docs/api-integrations.md` for the full .env template.
