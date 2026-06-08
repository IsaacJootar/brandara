# Brandara — Architecture

## Multi-tenancy approach — single database, brand_id scoped

Brandara uses **single-database multi-tenancy**. All workspaces share one database.
Data isolation is enforced entirely through `brand_id` scoping — every query on
brand-owned data MUST filter by `brand_id`. There are no separate databases per tenant.

### Why this approach

- One database = simpler hosting, cheaper, easier migrations
- How real social media SaaS products work (Buffer, Hootsuite, Sprout Social)
- Agencies managing 10+ client brands log in once and switch brands — no subdomain juggling
- Cross-workspace analytics and platform health checks work without multi-database gymnastics
- Scales to millions of records in one Postgres instance before sharding is needed

### Three levels of data ownership

```
Workspace (subscription/account)
  └── Users (people who can log in — belong to a workspace)
  └── Brands (the actual brand being managed — one workspace, many brands)
        └── Everything else: posts, campaigns, leads, connections, media...
              └── All scoped by brand_id — no exceptions
```

### URL structure

```
brandara.com/login                     ← single login for everyone
brandara.com/get-started              ← workspace registration
brandara.com/{brand-slug}/dashboard   ← brand dashboard
brandara.com/{brand-slug}/create      ← content creation
brandara.com/{brand-slug}/plan        ← campaigns
brandara.com/{brand-slug}/schedule    ← calendar
brandara.com/{brand-slug}/grow        ← engagement
brandara.com/{brand-slug}/results     ← analytics
brandara.com/{brand-slug}/my-brand    ← brand kit + voice DNA
brandara.com/{brand-slug}/connections ← platform OAuth
brandara.com/{brand-slug}/ai-presence ← AI visibility
```

### The non-negotiable scoping rule

**Every query on brand data must be scoped to brand_id. No exceptions.**

```php
// CORRECT
Post::where('brand_id', $brand->id)->where('status', 'published')->get();

// WRONG — leaks data across brands
Post::where('status', 'published')->get();
```

The `ResolveBrand` middleware loads the brand from the URL slug, verifies it belongs
to the authenticated user's workspace, and binds it to `app('current.brand')`.
Controllers receive it via dependency injection or `currentBrand()` helper.

---

## Request lifecycle

```
1. User visits brandara.com/acme-ng/dashboard
2. Laravel Router matches /{brand}/dashboard
3. auth middleware — verifies user is logged in
4. ResolveBrand middleware:
   a. Reads {brand} slug from route
   b. Queries: Brand::where('slug', 'acme-ng')->where('workspace_id', user->workspace_id)
   c. If not found → 403 (user does not own this brand)
   d. Binds brand to app('current.brand')
5. DashboardController@index receives brand via currentBrand() helper
6. All queries: Post::where('brand_id', $brand->id)->...
7. Blade view rendered with brand-scoped data
```

---

## Folder structure

```
brandara/
├── CLAUDE.md
├── docs/
│   ├── architecture.md            ← This file
│   ├── database.md                ← Complete schema
│   ├── stack.md
│   ├── ui-rules.md
│   ├── colors.md
│   ├── phases.md
│   ├── api-integrations.md
│   ├── brand-os-context.md
│   ├── karpathy-guidelines.md
│   ├── modules/
│   └── prompts/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                  ← Login, register, workspace creation
│   │   │   ├── DashboardController.php
│   │   │   ├── PostController.php
│   │   │   ├── CampaignController.php
│   │   │   ├── BrandController.php
│   │   │   ├── PlatformController.php
│   │   │   ├── MediaController.php
│   │   │   ├── LeadController.php
│   │   │   ├── AnalyticsController.php
│   │   │   ├── AiVisibilityController.php
│   │   │   ├── WorkspaceController.php
│   │   │   └── BillingController.php
│   │   ├── Livewire/
│   │   │   ├── PostComposer.php
│   │   │   ├── ContentCalendar.php
│   │   │   ├── PlatformPreview.php
│   │   │   ├── MediaLibrary.php
│   │   │   ├── NotificationBell.php
│   │   │   └── VariationPicker.php
│   │   └── Middleware/
│   │       ├── ResolveBrand.php       ← Loads brand from URL slug, verifies ownership
│   │       └── EnsureWorkspaceActive.php ← Blocks expired subscriptions
│   │
│   ├── Models/
│   │   ├── Workspace.php
│   │   ├── User.php
│   │   ├── Brand.php
│   │   ├── Post.php
│   │   ├── Campaign.php
│   │   ├── ContentPillar.php
│   │   ├── PlatformConnection.php
│   │   ├── MediaFile.php
│   │   ├── Lead.php
│   │   ├── AiVisibilityReport.php
│   │   └── Notification.php
│   │
│   ├── Services/
│   │   ├── AI/
│   │   │   ├── ClaudeService.php
│   │   │   ├── BrandVoiceService.php
│   │   │   ├── ContentGeneratorService.php
│   │   │   ├── CampaignPackService.php
│   │   │   ├── AiVisibilityService.php
│   │   │   └── Prompts/
│   │   ├── Platforms/
│   │   │   ├── LinkedInService.php
│   │   │   ├── TwitterService.php
│   │   │   ├── FacebookService.php
│   │   │   ├── InstagramService.php
│   │   │   ├── ThreadsService.php
│   │   │   └── PlatformServiceFactory.php
│   │   ├── Publishing/
│   │   │   ├── PublisherService.php
│   │   │   └── RetryService.php
│   │   └── Notifications/
│   │       ├── EmailService.php
│   │       ├── SmsService.php
│   │       └── PushService.php
│   │
│   └── Jobs/
│       ├── PublishPostJob.php
│       ├── RetryFailedPostJob.php
│       ├── RefreshPlatformTokenJob.php
│       └── SendWeeklyReportJob.php
│
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php              ← Single dashboard shell with sidebar
│   ├── components/
│   │   └── layouts/
│   │       ├── app.blade.php          ← Blade component alias
│   │       └── auth.blade.php         ← Login/register shell
│   ├── workspace/                     ← Registration
│   ├── auth/                          ← Login
│   ├── dashboard/
│   ├── create/
│   ├── plan/
│   ├── schedule/
│   ├── grow/
│   ├── results/
│   ├── my-brand/
│   ├── connections/
│   ├── ai-presence/
│   └── billing/
│
├── database/migrations/
│   ├── central migrations (workspaces, users, brands, all app tables)
│
├── routes/
│   ├── web.php                        ← All routes (auth + brand-scoped)
│   └── api.php                        ← Webhook endpoints
│
└── storage/app/brands/{brand_id}/media/  ← Per-brand media storage
```

---

## Queue architecture

```
Redis → Horizon dashboard → Queue workers → Jobs
```

Queue names:
- `publish` — high priority, post publishing
- `notifications` — medium priority, email/SMS/push
- `analytics` — low priority, data sync
- `default` — catch-all

---

## Real-time with Laravel Reverb

Reverb is the self-hosted WebSocket server.

```bash
php artisan reverb:start
```

Livewire uses Reverb for:
- Real-time platform preview (updates as user types)
- Live notification counter badge
- Real-time publish status on the calendar
