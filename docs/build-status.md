# Brandara — Build Status

Last updated: 2026-06-08
Current phase: **10 — Brand Voice**

---

## Completed modules

### ✅ Module 01 — Project Setup
- Laravel 13.14 on PHP 8.3.31
- Tailwind CSS v3 + DaisyUI v5, Alpine.js v3, Livewire 4
- Laravel Reverb (WebSockets), Horizon, Pulse, Telescope (dev)
- SQLite local, PostgreSQL (Supabase) for production
- Git repo: github.com/IsaacJootar/brandara

### ✅ Module 02 — Database Migrations
All tables created and migrated:
- `workspaces`, `users`, `brands`
- `platform_connections`, `content_pillars`, `campaigns`, `posts`
- `media_files`, `leads`, `ai_visibility_reports`
- `notifications` (custom schema — user_id based, not Laravel morph)
- `push_subscriptions` (webpush package)

### ✅ Module 03 — Workspace + Auth
- Registration at `/get-started` (workspace + brand + user in one flow)
- Login at `/login` with loading state on submit
- Path-based routing: `/{brand}/dashboard` (not subdomain)
- `ResolveBrand` middleware — validates brand ownership on every request
- `EnsureWorkspaceActive` middleware — checks trial/subscription
- Config-driven sidebar (`config/navigation.php`) — tier-based access
- `currentBrand()` global helper
- Loading states on login + register forms (btn-label/btn-loading pattern)
- App-ready skeleton CSS (gona-style) on metric cards

### ✅ Module 04 — Platform OAuth
- OAuth 2.0 for: LinkedIn, X (Twitter with PKCE), Facebook, Instagram, Threads
- One Brandara app per provider — users just click Authorize
- Tokens encrypted at rest with `Crypt::encryptString()`
- State parameter encodes brand_id + CSRF + timestamp (10-min expiry)
- Disconnect flow, status indicators (connected/expired/error)
- Fixed callback URLs at `/oauth/callback/{platform}` (providers require static URLs)
- `PlatformConnectionService` handles all OAuth logic

### ✅ Module 05 — Post Composer
- Manual write mode at `/{brand}/create`
- Input type tabs: Write / From topic / Transcript / Product (Alpine-driven, no blur race)
- Tone selector: 8 tones (Alpine-driven instant visual + Livewire persist)
- Platform selector (7 platforms) with character limits and per-platform char breakdown
- Save as draft — validated, scoped to brand_id, updateOrCreate
- Recent drafts list on Create page
- `wire:model.blur` on textarea (lazy sync to avoid Livewire race conditions)
- **Known gap:** media upload deferred to Module 15

### ✅ Module 06 — Schedule
- Queue view: Scheduled / Not published yet / Published / Needs attention tabs
- Calendar view: monthly grid, posts shown as colour chips, month navigation
- Schedule modal: date + time picker, timezone-aware (brand workspace timezone)
- Reschedule, Cancel (→ draft), Delete
- Fix & Retry: failed posts show plain-English reason + Retry now button
- `PublishPostJob` with 5-layer retry logic:
  - Layer 1: silent retry at 2min → 5min → 15min
  - Layer 2: error classification (token_expired/media_rejected = no retry)
  - Layer 3: `NotificationService::postFailed()` fires after exhausting retries
  - Layer 5: stores live post URLs on success
- `FakePublisher` — full pipeline testable without real OAuth apps
- `posts:dispatch-due` Artisan command — runs every minute via scheduler
- **Known gap:** drag-drop reschedule and pillar colour coding on calendar deferred to Module 13

### ✅ Module 07 — Plan (Content Strategy)
- Content pillars: create up to 5 per brand, name/goal/colour
- Pillar balance tracker: % of posts per pillar (last 30 days), stale alert after 14 days
- "Not used yet" badge on new pillars (not "Overdue" — that was a bug, now fixed)
- Campaign builder: name, goal, key message, date range, platform selection
- Campaign cards: pastel colour-coded (8 rotating colours)
- Overview tab: shows pillar balance + top 3 campaigns
- Campaigns tab: 8 per page with Previous/Next pagination
- Tip box explaining pillar vs campaign relationship

### ✅ Module 08 — Notifications (4 channels)
- 4 notification triggers:
  - `PostFailedNotification` — fires from PublishPostJob after retries exhausted
  - `TrialExpiringNotification` — daily at 09:00, fires at 3 days + 1 day before expiry
  - `TokenExpiredNotification` — daily at 07:00, marks expired tokens + notifies
  - `ApprovalNeededNotification` — fires when post submitted for review
- 4 channels per notification:
  - **In-app** — `BrandaraDbChannel` writes to custom `notifications` table (user_id based)
  - **Email** — Resend (queued, production-ready)
  - **Web push** — `WebPushChannel` + service worker at `public/sw.js`
  - **SMS** — Africa's Talking scaffold (fires when `AT_API_KEY` set + user has phone)
- In-app notification bell (Livewire): unread red badge, dropdown with last 15, mark read
- `NotificationService` — single entry point for all notification dispatch
- **Known gap:** VAPID keys are placeholder dev keys. Run `php artisan webpush:vapid` on Linux/Render to regenerate proper P-256 EC keys before going live.

### ✅ Module 09 — AI Content Engine
- Provider abstraction: `AiProvider` interface — both providers implement identical contract
- `ClaudeProvider` — default. Uses `anthropic-ai/sdk`. Model: `claude-sonnet-4-5`
- `OpenAiProvider` — alternative. Uses Guzzle HTTP directly. Model: `gpt-4o`
- `AiProviderFactory` — reads `BRANDARA_AI_PROVIDER` env (default: `claude`)
  - **Admin note for Module 22:** factory will read from DB setting so admin UI toggle requires zero code changes. Current hook: `config('ai.default')`
- `ContentGenerationService` — brand-aware system prompt using Brand Voice, target audience, negative brief, positioning. Generates 3 variations in one API call.
- Platform adaptation: LinkedIn (paragraphs), X (280 chars), Instagram (emoji hook), TikTok (hook first), Threads (casual), WhatsApp (broadcast), Facebook (conversational)
- JSON parser with markdown fence stripping + fallback for malformed responses
- `VariationPicker` Livewire component:
  - Shows when input type ≠ manual
  - Generating state with spinner
  - 3 colour-coded cards: Authority (purple) / Story (blue) / Bold (red)
  - Platform preview switcher
  - "Use this variation" → saves draft + dispatches `variation-selected` event → composer loads content
- **To activate:** add `ANTHROPIC_API_KEY=sk-ant-...` to `.env`
- **To switch to OpenAI:** `BRANDARA_AI_PROVIDER=openai` + `OPENAI_API_KEY=sk-...`

### ✅ Module 10 — Brand Voice
- `BrandVoiceService` — sends writing samples to Claude, stores voice profile JSON on brand
- `BrandVoice` Livewire component — paste samples or answer voice interview questions, train, retrain
- Voice profile captures: sentence length, vocabulary, tone, opening/closing patterns, emoji habits, signature phrases
- Integrated into all AI generation prompts via `brand_voice` JSON on the brand model

### ✅ Module 11 — Brand Kit + Profile
- `BrandKit` Livewire component — brand name, tagline, description, colours, fonts, logo upload, target audience
- `BrandProfile` Livewire component — vision, mission, values (up to 5), negative brief, positioning
- `CompletionScore` Livewire component — % score shown on dashboard, drives users to fill in missing fields
- All fields feed into AI content generation system prompts

### ✅ Module 12 — Content Pillars (Advanced)
- Pillar tagging on posts (content_pillar_id on post composer)
- Calendar colour-coding by pillar
- Pillar balance tracker with stale alert after 14 days
- AI balance alerts when a pillar has not been used

### ✅ Module 13 — Campaign Packs
- African holiday/event pack library with built-in packs
- One-click campaign generation from a pack
- Custom campaign builder with date range, platform selection, goal setting
- Pack library scrollable section on Plan page

### ✅ Module 14 — TikTok Toolkit
- `TikTokService` — generates caption, video script (hook/body/CTA), text overlays, hashtags, bio copy
- `TikTokToolkit` Livewire component — 6 tones, generating state, copy buttons per section
- Route: `/{brand}/create/tiktok` → `create.tiktok`
- Link card on Create page
- Tone selector fixed (Alpine `:style` binding — static `style` conflict resolved)
- **Note:** Text-only tool. TikTok has no publishing API in v1 — copy is pasted manually into TikTok app
- 9 tests passing

### ✅ Module 15 — Media Library + Carousel + Cards
- `MediaLibraryService` — upload, compress (Intervention Image v4), platform compliance check, delete, storage quota
- `MediaLibrary` Livewire component — drag-drop upload, search, grid, delete, picker mode
- `MediaPicker` Livewire component — modal picker in post composer, dispatches `media-selected` event
- `PostComposer` updated — `attachedMedia` state, `onMediaSelected` listener, thumbnail strip, remove button
- Route: `/{brand}/media` → `media`, added to sidebar nav
- `CarouselService` — carousel slide deck generation + quote/testimonial/motivational card copy
- `CarouselGenerator` Livewire component — carousel mode + quote card mode, two modes in one component
- Route: `/{brand}/create/carousel` → `create.carousel`, link card on Create page
- Canva "Design in Canva" button (deep link, no API key needed in v1)
- `CanvaController` — Canva webhook scaffold ready for when Canva Connect app is approved
- 21 tests passing (12 media + 9 carousel)

### ✅ Module 16 — WhatsApp Assistant
- `WhatsAppService` — 4 copy types (broadcast, status, promo, follow-up), 2 variations each, do/don't tips
- `WhatsAppAssistant` Livewire component — type selector (Blade-driven, no Alpine state), brief input, results
- WhatsApp Share API button — "Send on WhatsApp" opens WA with message pre-filled (`wa.me/?text=`)
- Route: `/{brand}/create/whatsapp` → `create.whatsapp`, link card on Create page
- Brand Voice integrated — matches user's natural writing tone
- 12 tests passing

### ✅ Tier & Multi-Brand Architecture (between 16 and 17)
- `config/features.php` — single source of truth for feature gates, brand limits, generation limits, storage limits
- `PlanFeatureService` — all tier checks through one service; Phase 1: config-driven, Phase 2 (Module 22): DB-driven with zero other changes
- `<x-tier-gate feature="...">` — wrap any feature; shows upgrade card if locked, content if allowed
- `ChecksGenerationLimit` trait — applied to all 5 AI services (ContentGenerationService, TikTokService, WhatsAppService, CarouselService, CampaignPackService)
- Generation counter: `ai_generations_used` + `usage_reset_date` columns on workspaces
- `usage:reset-monthly` Artisan command — resets Basic counters on 1st of month, scheduled
- `BrandController` — create/store brand with tier limit enforcement
- Multi-brand sidebar: "Add brand" button (if under limit), "Upgrade to add brands" (if at limit), brand count shown
- Platform restriction in PostComposer: Basic = Facebook/LinkedIn/X only; locked platforms shown with "Growth" badge; server-side enforcement in `togglePlatform()`
- Plan labels: `starter` → "Basic", `pro` → "Growth", `agency` → "Agency" everywhere
- Trends split from AI Visibility: separate nav item, route, view, feature gate
- Pricing updated on website: Basic $19 · Growth $39 · Agency $89 (NGN equivalents shown)
- Brand limits: Basic 1, Growth 3, Agency unlimited
- Storage limits: Basic 500MB, Growth 2GB, Agency 10GB

---

## Pending modules (17–22)

| # | Module | Key dependencies |
|---|---|---|
| 17 | Engagement automation | Auto-like/comment rules, Brand Voice contextual replies — Pro+ only |
| 18 | Lead tracker | Post engagers, enrichment, tags, notes, CSV export — Pro+ only |
| 19 | Analytics dashboard | Results screen, engagement metrics, weekly digest email — Pro+ only |
| 20 | Billing | Paystack (NGN) + Flutterwave (pan-Africa), webhooks, trial expiry — Basic $19 / Growth $39 / Agency $89 |
| 21 | AI Visibility | Queries ChatGPT/Perplexity/Gemini/Claude, stores reports, dashboard with sentiment — Pro+ only |
| 22 | Admin Panel | /brandara-admin, DB-driven tier/module access, workspace mgmt — replaces config/features.php |

---

## Known gaps / deferred items

| Item | Deferred to |
|---|---|
| Canva Connect API pre-population | When Canva Connect partner app approved — webhook scaffold in place |
| VAPID key regeneration (EC keys need Linux) | Before go-live on Render |
| Real platform API publishers (LinkedIn/X/Meta live) | When OAuth dev apps approved |
| AI provider switch in admin UI | Module 22 — `AiProviderFactory` already has hook |
| SMS via Africa's Talking (live) | When `AT_API_KEY` added to production |
| Supabase Storage activation | One-line `.env` change before launch — local disk is ephemeral on Render |
| Tier gates via DB (Admin Panel) | Module 22 — `PlanFeatureService` already abstracts config vs DB |
| Billing integration (Paystack/Flutterwave) | Module 20 — plan field currently set manually |

---

## Architecture decisions recorded here

- **Single DB tenancy** — brand_id on every table, not separate databases per tenant
- **Path-based URLs** — `/{brand-slug}/...` not subdomain-based
- **No Stripe** — Paystack (NGN) + Flutterwave (pan-Africa) only
- **AI provider abstraction** — swap Claude ↔ OpenAI by changing one env var
- **Notification table** — custom schema (user_id direct), not Laravel's morph pattern
- **FakePublisher** — real platform API calls are behind `services.publishing.live` flag; safe to test without live OAuth apps
- **Alpine for instant UI + Livewire for persistence** — used on tone/tab selectors to prevent blur race condition
- **Multi-brand architecture** — one account → one workspace → many brands (isolated by brand_id). Limits: Basic 1, Growth 3, Agency unlimited
- **Tier enforcement** — `PlanFeatureService` + `config/features.php` is the single source of truth. `<x-tier-gate>` wraps features in views. Module 22 switches to DB-driven without touching any view
- **Generation limits** — Basic: 30/month (counted via `ai_generations_used` on workspace, reset 1st of month). Growth/Agency: unlimited. All 5 AI services use `ChecksGenerationLimit` trait
- **Platform restriction** — Basic: Facebook/LinkedIn/X only. Growth+: all 7 platforms. Enforced in `PostComposer::isPlatformAllowed()` — both UI and server-side
- **Pricing** — Basic $19 / Growth $39 / Agency $89. NGN equivalents shown on website. 7-day free trial. Cancel anytime
- **Trends vs AI Visibility** — separate nav items, routes, and feature gates. Trends = industry content signals (Module 17/18). AI Visibility = brand mentions in ChatGPT/Gemini/Perplexity (Module 21)
