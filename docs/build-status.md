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

---

## Pending modules (10–22)

| # | Module | Key dependencies |
|---|---|---|
| 10 | Brand Voice | Paste samples → Claude trains voice profile → stored as JSON on brand |
| 11 | Brand Kit + Profile | My Brand screen, logo upload, all fields feed into AI prompts |
| 12 | Content pillars (advanced) | Pillar tags on posts, calendar colouring, AI balance alerts |
| 13 | Campaign packs | African holiday library, built-in packs, one-click generation |
| 14 | TikTok toolkit | Caption + video script + hashtag generator, pure text |
| 15 | Media library | Upload/browse/compress, Canva button, platform compliance check |
| 16 | WhatsApp assistant | Broadcast/status/follow-up copy, WhatsApp-native tone |
| 17 | Engagement automation | Auto-like/comment rules, Brand Voice contextual replies |
| 18 | Lead tracker | Post engagers, enrichment, tags, notes, CSV export |
| 19 | Analytics dashboard | Results screen, engagement metrics, weekly digest email |
| 20 | Billing | Paystack (NGN) + Flutterwave (pan-Africa), webhooks, trial expiry |
| 21 | AI Visibility & Trends | Queries 4 AI systems, stores reports, dashboard with sentiment |
| 22 | Admin Panel | /brandara-admin, DB-driven tier/module access, workspace mgmt |

---

## Known gaps / deferred items

| Item | Deferred to |
|---|---|
| Media upload in composer | Module 15 |
| Drag-drop reschedule on calendar | Module 12 |
| Pillar colour on calendar | Module 12 |
| VAPID key regeneration (EC keys need Linux) | Before go-live on Render |
| Real platform API publishers (LinkedIn/X/Meta live) | When OAuth dev apps approved |
| AI provider switch in admin UI | Module 22 — `AiProviderFactory` already has hook |
| Campaign → post tagging from composer | Module 10 AI generation |
| SMS via Africa's Talking (live) | When `AT_API_KEY` added to production |

---

## Architecture decisions recorded here

- **Single DB tenancy** — brand_id on every table, not separate databases per tenant
- **Path-based URLs** — `/{brand-slug}/...` not subdomain-based
- **No Stripe** — Paystack (NGN) + Flutterwave (pan-Africa) only
- **AI provider abstraction** — swap Claude ↔ OpenAI by changing one env var
- **Notification table** — custom schema (user_id direct), not Laravel's morph pattern
- **FakePublisher** — real platform API calls are behind `services.publishing.live` flag; safe to test without live OAuth apps
- **Alpine for instant UI + Livewire for persistence** — used on tone/tab selectors to prevent blur race condition
