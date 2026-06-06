# Brandara — Architecture

## Multi-tenancy — the most important architectural decision

Brandara uses **stancl/tenancy** for full database-per-tenant multi-tenancy.

### Why this matters
Every workspace is completely isolated. No tenant can see, access, or affect
another tenant's data. This is non-negotiable for a B2B SaaS handling brand
content, lead data, and OAuth tokens.

### Two database layers

**Central database** — stores only:
- `workspaces` (tenants) table
- Tenant routing records

**Tenant database** — one per workspace, stores everything else:
- users, brands, posts, campaigns, platform_connections
- leads, media_files, content_pillars, notifications
- All business data

### How it works in practice

1. Request arrives at `acme.brandara.co`
2. Tenancy middleware identifies tenant from subdomain
3. Database connection switches to Acme's tenant database
4. All Eloquent queries automatically hit the right database
5. No manual tenant filtering needed on most queries

### Critical rule

**Never write a raw global query on tenant data.**
Always verify the tenancy middleware is active on every route.
Tenant routes live in `routes/tenant.php` — not `routes/web.php`.

---

## Folder structure

```
brandara/
├── CLAUDE.md                          ← Master instructions (root)
├── docs/                              ← All documentation for Claude Code
│   ├── architecture.md                ← This file
│   ├── database.md                    ← Complete schema
│   ├── stack.md                       ← Tech stack details
│   ├── ui-rules.md                    ← Naming and UX rules
│   ├── colors.md                      ← Complete colour system
│   ├── phases.md                      ← 22 build phases
│   ├── api-integrations.md            ← All external APIs
│   ├── brand-os-context.md            ← Product context
│   ├── karpathy-guidelines.md         ← Behavioral coding rules
│   ├── modules/                       ← One file per feature module
│   │   ├── 01-create.md
│   │   ├── 02-brand-intelligence.md
│   │   ├── 03-plan.md
│   │   ├── 04-visual-media.md
│   │   ├── 05-publishing.md
│   │   ├── 06-grow.md
│   │   ├── 07-results.md
│   │   └── 08-ai-visibility.md
│   └── prompts/                       ← AI prompt templates
│       ├── voice-dna.md
│       ├── content-generation.md
│       ├── campaign-pack.md
│       ├── smart-comment.md
│       ├── whatsapp-copy.md
│       ├── tiktok-toolkit.md
│       ├── weekly-report.md
│       └── ai-visibility-query.md
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                  ← Login, register, workspace creation
│   │   │   ├── PostController.php     ← Create, edit, schedule posts
│   │   │   ├── CampaignController.php ← Campaign packs + custom builder
│   │   │   ├── BrandController.php    ← Brand kit, profile, voice DNA
│   │   │   ├── PlatformController.php ← OAuth connect/disconnect
│   │   │   ├── MediaController.php    ← Media library
│   │   │   ├── LeadController.php     ← Lead engagement tracker
│   │   │   ├── AnalyticsController.php← Results dashboard
│   │   │   ├── AiVisibilityController.php ← Module 08
│   │   │   └── BillingController.php  ← Paystack + Flutterwave webhooks
│   │   ├── Livewire/
│   │   │   ├── PostComposer.php       ← Real-time composer with preview
│   │   │   ├── ContentCalendar.php    ← Drag-drop calendar
│   │   │   ├── PlatformPreview.php    ← Live per-platform preview
│   │   │   ├── MediaLibrary.php       ← Media picker
│   │   │   ├── NotificationBell.php   ← Live notification counter
│   │   │   └── VariationPicker.php    ← 3-variation card selector
│   │   └── Middleware/
│   │       ├── EnsureTenantActive.php ← Block expired workspaces
│   │       └── EnsureTrialValid.php   ← Block post-trial unpaid
│   │
│   ├── Models/                        ← Eloquent models (see database.md)
│   │
│   ├── Services/
│   │   ├── AI/
│   │   │   ├── ClaudeService.php      ← Core Anthropic API client
│   │   │   ├── VoiceDnaService.php    ← Voice profile training
│   │   │   ├── ContentGeneratorService.php ← 3-variation generation
│   │   │   ├── CampaignPackService.php     ← Campaign generation
│   │   │   ├── AiVisibilityService.php     ← Module 08 queries
│   │   │   └── Prompts/               ← PHP prompt template strings
│   │   ├── Platforms/
│   │   │   ├── LinkedInService.php
│   │   │   ├── TwitterService.php
│   │   │   ├── FacebookService.php
│   │   │   ├── InstagramService.php
│   │   │   ├── ThreadsService.php
│   │   │   └── PlatformServiceFactory.php
│   │   ├── Publishing/
│   │   │   ├── PublisherService.php   ← Orchestrates publish
│   │   │   └── RetryService.php       ← 5-layer retry logic
│   │   └── Notifications/
│   │       ├── EmailService.php       ← Resend integration
│   │       ├── SmsService.php         ← Africa's Talking
│   │       └── PushService.php        ← Web push notifications
│   │
│   └── Jobs/
│       ├── PublishPostJob.php          ← Fires at scheduled time
│       ├── RetryFailedPostJob.php      ← Retry handler
│       ├── RefreshPlatformTokenJob.php ← Daily token health
│       └── SendWeeklyReportJob.php     ← Monday digest
│
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php              ← Main dashboard shell
│   │   └── auth.blade.php             ← Login/signup shell
│   ├── create/                        ← Post composer screens
│   ├── plan/                          ← Campaigns + pillars
│   ├── schedule/                      ← Calendar + queue
│   ├── grow/                          ← Engagement + leads
│   ├── results/                       ← Analytics
│   ├── my-brand/                      ← Brand kit + voice DNA
│   ├── connections/                   ← Platform OAuth
│   ├── ai-presence/                   ← Module 08 screens
│   └── billing/                       ← Plans + payments
│
├── database/
│   ├── migrations/central/            ← Central DB migrations
│   └── migrations/tenant/             ← Tenant DB migrations
│
├── routes/
│   ├── web.php                        ← Public + auth routes
│   ├── tenant.php                     ← All tenant-scoped routes
│   └── api.php                        ← Webhook endpoints
│
├── config/
│   ├── tenancy.php                    ← Tenancy config
│   └── services.php                   ← Third-party credentials
│
└── storage/app/tenants/{id}/media/    ← Per-tenant media files
```

---

## Request lifecycle

```
1. Request → {workspace}.brandara.co/schedule
2. DNS → Render server
3. Laravel Router → InitializeTenancyByDomain middleware
4. Tenancy switches DB connection to workspace's tenant database
5. EnsureTenantActive middleware checks subscription status
6. Route hits ScheduleController@index
7. All Eloquent queries auto-scoped to tenant database
8. Blade view rendered with tenant data
9. Livewire components mount with tenant context preserved
10. Response returned
```

---

## Real-time with Laravel Reverb

Reverb is the self-hosted WebSocket server. It replishes Pusher at zero cost.

```bash
# Start in development
php artisan reverb:start

# Laravel Echo in Blade (connects to Reverb)
# resources/js/echo.js already configured by `php artisan reverb:install`
```

Livewire uses Reverb automatically for real-time features.
No additional configuration needed after `php artisan reverb:install`.

---

## Queue architecture with Laravel Horizon

```
Redis → Horizon dashboard → Queue workers → Jobs
```

**Queue names in Brandara:**
- `publish` — high priority, post publishing
- `notifications` — medium priority, email/SMS/push
- `analytics` — low priority, data sync jobs
- `default` — catch-all

```php
// PublishPostJob dispatched to high-priority queue
PublishPostJob::dispatch($post)->onQueue('publish');

// Weekly report to low-priority
SendWeeklyReportJob::dispatch()->onQueue('analytics');
```

Horizon dashboard at `/horizon` — only accessible to workspace owners.
