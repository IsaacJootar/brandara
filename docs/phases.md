# Brandara — 23 Build Phases

Build in this exact order. Every phase must be complete and tested
before moving to the next. Do not skip phases.

Update `CLAUDE.md` → CURRENT PHASE when starting a new session.

---

## Architecture notes before building

- **Multi-tenancy:** single database, brand_id scoped. See `docs/architecture.md`.
- **Nav tier access:** driven by `config/navigation.php` until Phase 23.
  Phase 23 migrates it to database. Never hard-code tier access in Blade files.
- **Admin panel (Phase 23):** gives Isaac full control over tiers, modules,
  and workspaces without touching code. See `docs/modules/09-admin.md`.

---

| # | Phase | What to build | Done when |
|---|---|---|---|
| 01 | Project setup | Laravel 13, PHP 8.3, Tailwind + DaisyUI, Livewire, Reverb, Horizon, Pulse, folder structure, .env, SQLite, git | App loads at localhost, no errors |
| 02 | Database migrations | All migrations: workspaces, users, brands, and all brand-scoped tables in single DB | All tables created, migrate:status clean |
| 03 | Workspace + auth | Registration (workspace + first brand). Single login at /login. Dashboard shell with config-driven sidebar. Tier-based nav. | User can register, log in, see dashboard at /brand/dashboard |
| 04 | Platform OAuth | Connect LinkedIn, X, Facebook, Instagram, Threads. Encrypted token storage. Health status indicators. Disconnect flow. Token expiry detection. | All 5 platforms connect, green indicators show |
| 05 | Post composer — write mode | Blank composer screen. Platform selector. Character count per platform. Manual write and save as draft. Basic media upload. | User can write a post and save as draft |
| 06 | Platform preview panel | Live Livewire preview powered by Reverb. Updates as user types. Character limit warnings. Compliance checks (Instagram requires image). | Preview updates in real time |
| 07 | Scheduling + calendar | Schedule to date and time. Visual monthly calendar colour-coded by pillar. Drag-drop reschedule. Queue mode with posting frequency. | Post appears on calendar at scheduled time |
| 08 | Publish job + retry system | PublishPostJob fires at scheduled time. Calls platform API. 3-layer retry (2min, 5min, 15min). Error classification. Failed post queue (Fix & Retry screen). Publish confirmation with live post URL. | Post auto-publishes. Failures appear in Fix & Retry |
| 09 | Notifications (4 channels) | Laravel Horizon + Redis setup. Resend email. Africa's Talking SMS. Web push notifications (VAPID keys, service worker). In-app notification bell via Livewire. Triggers: post failed, trial expiring, token expired, approval needed. | All 4 channels deliver notifications correctly |
| 10 | AI content engine | Claude API connected. Generation from topic input. Per-platform adaptation (7 platforms). 3 variations (Authority, Story, Bold). VariationPicker Livewire component. Tone modes. Hashtag suggestions. CTA insertion. | 3 variation cards appear after generation. Each adapts per platform. |
| 11 | Voice DNA | Voice sample input (paste 10–20 posts). VoiceDnaService sends to Claude. Stores voice profile JSON on brand. All subsequent generation uses profile. | Generated posts noticeably reflect user's writing style |
| 12 | Brand Kit + Profile | My Brand screen. Brand Kit form (name, colors, fonts, logo). Brand Profile form (vision, mission, values, negative brief, target audience, positioning). All saved to brands table. | Full brand profile saved. Referenced in AI generation. |
| 13 | Content pillars | Create up to 5 pillars with name, goal, colour. Balance tracker. Calendar shows pillar colour. Low-pillar alert in AI recommendations. | Pillars visible on calendar. Balance warning fires. |
| 14 | Campaign packs | Built-in pack library (Black Friday, Ramadan, Easter, Nigeria Independence Day, Ghana Independence Day, Africa Day, school season, product launch, new branch). One-click generation. Custom campaign builder form. | Built-in and custom campaigns generate correctly |
| 15 | TikTok toolkit | TikTok section in Create. Caption generator. Video script generator (opening 3 seconds, arc, CTA). Hashtag suggestions. Bio copy. Text overlay copy. Pure text output — no API integration. | TikTok content generates and feels native to platform |
| 16 | Media library | Upload, browse, search, select media. Per-brand storage at storage/app/brands/{id}/media/. Image compression via Intervention Image. Platform compliance check on upload. Canva integration button. | Media library functional. Canva button opens correctly. |
| 17 | WhatsApp assistant | WhatsApp section in Create module. Broadcast message generator. Status copy generator. Promo text. Customer follow-up sequence generator. WhatsApp-native tone. Campaign-linked messaging. | WhatsApp copy generates correctly and feels native to channel |
| 18 | Engagement automation | Auto-like rules configuration. Auto-comment with AI-generated contextual replies using Voice DNA. Comment rules by person list, keyword, industry. Frequency controls. | Automation rules save and execute correctly |
| 19 | Lead engagement tracker | Pull post engagers via platform APIs. Display in Grow section with job title, company enrichment. Tag (warm_lead, prospect, client, partner). Notes field. Follow-up reminder. CSV export. | Leads appear with correct enriched data. Tags and notes save. |
| 20 | Analytics dashboard | Results screen. Engagement per post, platform, pillar. Top posts by format. Best posting times. Platform comparison. Follower growth trends. Platform health monitor. Weekly Monday 8AM digest. | All metrics display correctly. Weekly report email sends. |
| 21 | Billing — Paystack + Flutterwave | Pricing page with 3 plans. Paystack checkout for Nigerian users. Flutterwave checkout for other African users. Webhook handlers for subscription events. Trial expiry flow with upgrade prompt. | Payment completes. Plan upgrades correctly. Trial-expired users see upgrade screen. |
| 22 | AI Visibility & Trends | AiVisibilityService queries ChatGPT, Perplexity, Gemini, Google AI. Stores results in ai_visibility_reports. AI Presence dashboard. Sentiment tracking. Topic ownership map. Competitor comparison. African market query library. | Brand mentions tracked. Topic map populated. Competitor comparison working. |
| 23 | Admin Panel + Module Tier Management | Platform admin panel at /brandara-admin. Subscription tier management. Database-driven module access (replaces config/navigation.php). Workspace management. Billing overview. Module on/off toggle. Full spec in docs/modules/09-admin.md. | Isaac can change which modules each tier accesses from the admin UI without touching code. |

---

## Tier access — current state (before Phase 23)

Managed in `config/navigation.php`. Edit that file to change what each
tier can access. Run `php artisan config:clear` after changes.

| Module | Starter | Pro | Agency |
|---|---|---|---|
| Dashboard | ✓ | ✓ | ✓ |
| Create | ✓ | ✓ | ✓ |
| Plan | ✓ | ✓ | ✓ |
| Schedule | ✓ | ✓ | ✓ |
| Grow | ✓ | ✓ | ✓ |
| Results | ✓ | ✓ | ✓ |
| My Brand | ✓ | ✓ | ✓ |
| Connections | ✓ | ✓ | ✓ |
| AI Visibility & Trends | 🔒 | ✓ | ✓ |

---

## Phase testing checklist

After every phase, manually test before moving on:

**Phase 03:** Register workspace. Log in. Log out. Log in again. Check /brand/dashboard loads.
**Phase 04:** Connect LinkedIn. Disconnect. Reconnect. Check token stored encrypted.
**Phase 05:** Write a post. Save as draft. Retrieve draft.
**Phase 06:** Open composer. Type 10 characters. Preview updates. Switch platform. Preview updates again.
**Phase 07:** Schedule a post for 2 minutes from now. Wait. Check calendar.
**Phase 08:** Force a publish failure by revoking a token. Verify retry fires. Verify Fix & Retry shows post.
**Phase 09:** Trigger a post failure notification. Check in-app bell, email inbox, SMS, browser push.
**Phase 10:** Enter a topic. Generate. Verify 3 cards appear. Verify each card shows different angle.
**Phase 22:** Run an AI visibility query for a test brand. Verify results stored in database.
**Phase 23:** Log into admin panel. Change a tier's module access. Log into a workspace on that tier. Verify sidebar reflects the change instantly.
