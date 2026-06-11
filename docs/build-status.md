# Brandara — Build Status

Last updated: 2026-06-11
Current phase: **21 — AI Visibility (complete)**

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

### ✅ Module 17 — Engagement Automation
- `EngagementService` — orchestrates auto-like/comment logic per brand rules
- `CommentGeneratorService` — AI-generated comments in Brand Voice tone
- `FakeEngagementPublisher` — logs actions; activates when real OAuth apps approved
- `EngagementAutomation` Livewire component — rules engine with review queue (approve before AI posts)
- Opt-in per brand: master toggle in Settings → Engagement (off by default)
- Scan frequency options: daily, twice daily, weekly, twice weekly
- Growth+ only — Basic users see upgrade prompt
- 10 tests passing

### ✅ Module 18 — Lead Tracker (Engagement & Growth)
- `LeadTracker` Livewire component — search, tag/platform filters, inline edit (tag, notes, follow-up date)
- CSV export, stats bar (total, warm, follow-ups due)
- Cross-brand security via `findLead()` helper
- Engagement & Growth page has tab switcher: Lead Tracker | Automation
- Both tabs wrapped in `<x-tier-gate feature="lead_tracker">` — Growth+ only
- 11 tests passing

### ✅ Module 19 — Analytics Dashboard
- `AnalyticsService` — summary(), dailyChart(), platformBreakdown(), topPosts(), bestPostingTimes(), weekOverWeek()
- `ResultsDashboard` Livewire component — period selector (7/30/90 days), stat cards, Chart.js line chart
- `FakeAnalyticsSeeder` — seeds realistic fake analytics for dev/demo. Command: `php artisan analytics:seed-fake {brand_slug}`
- `WeeklyDigestMail` — queued mailable, Growth+ only. Scheduled every Monday 8AM
- `usage:reset-monthly` Artisan command — resets Basic AI generation counters on 1st of month
- Basic plan: blurred preview with compelling upgrade card listing 5 specific features
- Stat cards use same full-color gradient design as Dashboard metric cards
- 8 tests passing

### ✅ Trends Dashboard (between 19 and 20)
- Full dashboard at `/{brand}/trends` — Growth+ only
- 4 metric stat cards (same gradient style as Dashboard): Industry signals, Format trends, Competitor signals, Hottest platform
- **Tab 1 — Industry Trends:** Top 10 trending topics in brand's niche, strength bar (1–100), platform badges, Hot/Rising/Emerging labels
- **Tab 2 — Content Formats:** Cards per format with description, signal strength meter, platform colour bar — what types of posts are working and why
- **Tab 3 — Competitor Signals:** Keyword/competitor activity with Alert/Watch/Low badges + Tracked Keywords manager (add/remove keywords per platform)
- `TrendsService` — reads `trend_signals` table (source-agnostic: fake | ai | api)
- `FakeTrendsSeeder` — 30 realistic signals (10 per category). Command: `php artisan trends:seed-fake {brand_slug}`
- `TrackedKeyword` model — user-defined keywords/competitors to monitor per brand
- Architecture ready: when real APIs arrive (X API, Google Trends), `LiveTrendsFetcher` writes to same table — zero other changes
- 10 tests passing

---

### ✅ Module 20 — Billing (Flutterwave + Paystack)
- `billing_plans` table — 36 plans seeded: Basic/Growth/Agency × monthly/yearly × USD/NGN/GBP/GHS/KES/ZAR. Admin-editable from Module 22
- `billing_settings` table — key/value store: default_provider (flutterwave), fallback_provider (paystack), yearly_discount_label, default_currency, test_mode
- `subscriptions` table — records every payment with plan, interval, currency, amount, provider, period dates
- `PaymentProviderInterface` — shared contract for both providers. Swap provider by changing one DB setting
- `FlutterwaveProvider` — default. Handles initializePayment, verifyPayment, parseWebhook (verif-hash signature). Accepts cards from any country
- `PaystackProvider` — fallback. Handles same interface. Paystack amounts converted to kobo internally
- `BillingService` — orchestrator: resolves provider from DB, detects currency from workspace country, handles payment success/cancellation, usage summary
- `BillingController` — `/billing` page, `/billing/checkout` (POST → returns popup data as JSON), `/billing/verify` (callback after payment)
- `FlutterwaveWebhookController` + `PaystackWebhookController` — webhook endpoints exempt from CSRF, verify signatures, dispatch events
- `PaymentReceiptMail` — queued, sent on successful payment. Shows plan, interval, amount, next billing date
- Billing page: current plan + usage summary cards, monthly/yearly toggle (Alpine), plan cards with prices from DB, yearly savings calculated dynamically, Flutterwave/Paystack JS popups
- Trial expiry: `EnsureWorkspaceActive` now redirects to `/billing` with plain-English message instead of blank page
- Billing link added to sidebar footer
- Provider toggle ready for Module 22 admin UI — zero code changes needed
- 11 tests passing

---

### ✅ Module 21 — AI Visibility

- 5-section dashboard at `/{brand}/ai-presence` — Livewire component with tab navigation
- **Section 1 — AI Readiness Score:** `WebsiteScannerService` — 20 automated checks across 3 tiers (Tier 1: 15 foundation checks, Tier 2: 5 advanced checks). Fetches HTML, robots.txt, headers, sitemap. Checks: HTTPS, title, meta description, canonical, JSON-LD, FAQ schema, about/contact pages, contact details, city/industry mentions, sameAs links, indexability, AI bot allowance, mobile viewport, local business schema. Score = % passed across all tiers.
- **Section 2 — Entity Clarity:** 5 manual toggle checklist items — Google Business Profile, NAP consistency, reviews, directory listings, social profiles. Toggle saves immediately. Guidance text per item.
- **Section 3 — Content Signals:** Post count cards, content checklist, country-aware directory guide via `CountryDirectoryService` (NG: VConnect/Techpoint/BusinessDay, GH: MyJoyOnline/GhanaWeb, KE: Nation Africa, ZA: Bizcommunity/ITWeb, GB: Yell/Guardian). Effort levels per directory.
- **Section 4 — Quick-Fix Assets:** `AssetGeneratorService` — generates 5 asset types via Claude API with fallbacks: JSON-LD, LocalBusiness schema, FAQ schema, About page copy, Brand markdown. Copy button + "Mark as published" flow.
- **Section 5 — Live AI Presence:** `AiPresenceService` — queries Claude (haiku), ChatGPT (gpt-4o-mini), Gemini (2.0-flash). 6 tailored prompts per brand (discovery, trust, local intent, consideration). Detection: brand mention, position, sentiment (positive/neutral/negative/not_mentioned), competitors. Perplexity labelled "Coming soon".
- Tables: `ai_visibility_checks`, `ai_presence_results`, `ai_generated_assets`, `website_url` on brands
- AI provider toggles ready for Module 22 admin UI — zero code changes needed
- Welcome hero explaining AI visibility journey in plain language
- Colored SVG tabs: purple (Readiness), blue (Entity), green (Content), amber (Quick-Fix), red (Presence)
- Content checklist rewritten as questions with status icons and clear fix steps
- Copy button fixed: copies to clipboard + "Copied ✓" feedback
- Local + global directories shown together; asset generation includes country-aware directory suggestions
- Once-per-month live presence scan limit (server-side enforced + UI disabled state with next date)
- Monitoring advice card: "AI visibility improves over time — not overnight"
- "Don't have a website?" card guiding brands to get one built
- 11 tests passing

---

### ✅ Module 22 — Admin Panel

- Admin panel at `/brandara-admin` — 5 screens, dark sidebar, Livewire-powered
- `EnsurePlatformAdmin` middleware — access controlled by `ADMIN_EMAILS` env (default: jootarisaac@gmail.com)
- `AdminSetting` model — platform-level key-value store with `get()`, `set()`, `getJson()`, `setJson()`, `group()` methods
- `admin_settings` table — seeded with feature gates, limits, AI settings, general settings
- **Dashboard:** total workspaces, active/trialing/expired counts, MRR estimate, by-plan breakdown, recent payments table
- **Workspaces:** search by name/email/slug, filter by plan/status, inline plan change (dropdown), extend trial (+7 days button), brand count
- **Features & Tiers:** checkbox matrix — toggle any feature for any plan (Basic/Growth/Agency). Saves to DB instantly. Generation limits, brand limits, storage limits editable with Save button
- **Billing & Plans:** switch default/fallback payment provider (Flutterwave/Paystack), toggle test mode, edit plan prices inline, toggle plan active/inactive, recent subscriptions table
- **AI Settings:** switch content generation provider (Claude/OpenAI), toggle AI presence providers (Claude/ChatGPT/Gemini/Perplexity), shows API key status
- `PlanFeatureService` upgraded — reads from `admin_settings` DB first, falls back to `config/features.php`. All existing tier gates, limits, and feature checks work unchanged
- `AdminSettingsSeeder` seeds all initial settings from current config values
- 13 tests passing

---

## All 22 modules complete ✅

---

## Known gaps / deferred items

| Item | Deferred to |
|---|---|
| Canva Connect API pre-population | When Canva Connect partner app approved — webhook scaffold in place |
| VAPID key regeneration (EC keys need Linux) | Before go-live on Render |
| Real platform API publishers (LinkedIn/X/Meta live) | When OAuth dev apps approved |
| SMS via Africa's Talking (live) | When `AT_API_KEY` added to production |
| Supabase Storage activation | One-line `.env` change before launch — local disk is ephemeral on Render |

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
- **Tier enforcement** — `PlanFeatureService` reads from `admin_settings` DB first, falls back to `config/features.php`. `<x-tier-gate>` wraps features in views. Admin panel at `/brandara-admin` manages all tier access, limits, and AI settings from UI
- **Generation limits** — Basic: 30/month (counted via `ai_generations_used` on workspace, reset 1st of month). Growth/Agency: unlimited. All 5 AI services use `ChecksGenerationLimit` trait
- **Platform restriction** — Basic: Facebook/LinkedIn/X only. Growth+: all 7 platforms. Enforced in `PostComposer::isPlatformAllowed()` — both UI and server-side
- **Pricing** — Basic $19 / Growth $39 / Agency $89. NGN equivalents shown on website. 7-day free trial. Cancel anytime
- **Trends vs AI Visibility** — separate nav items, routes, and feature gates. Trends = industry content signals (Module 17/18). AI Visibility = brand mentions in ChatGPT/Gemini/Perplexity (Module 21)
