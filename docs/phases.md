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

## Status key

| Symbol | Meaning |
|--------|---------|
| ✅ | Complete — committed, tested, approved |
| 🔄 | In progress |
| ⏳ | Not started |
| ⚠️ | Partial — see notes |

---

| # | Phase | Status | Notes |
|---|---|---|---|
| 01 | Project setup | ✅ | Laravel 13, PHP 8.3, Tailwind + DaisyUI, Livewire 4, Reverb, Horizon, Pulse, SQLite, git |
| 02 | Database migrations | ✅ | All tables created. Single DB, brand_id scoped throughout |
| 03 | Workspace + auth | ✅ | Registration at /get-started, login at /login, path-based routing `/{brand}/dashboard`, ResolveBrand middleware, config-driven sidebar |
| 04 | Platform OAuth | ✅ | LinkedIn, X, Facebook, Instagram, Threads. Encrypted tokens. Disconnect flow. State parameter with PKCE for Twitter |
| 05 | Post composer — write mode | ✅ | Composer at `/{brand}/create`. Platform selector, char counts, tone selector, save as draft. Alpine fixes for race condition on blur |
| 06 | Schedule | ✅ ⚠️ | Queue + calendar UI, schedule modal, cancel, retry failed, PublishPostJob with 5-layer retry, FakePublisher. **Drag-drop reschedule and pillar colour on calendar: pending Module 13** |
| 07 | Plan — Content Strategy | ✅ | Content pillars (up to 5), pillar balance tracker, campaign builder with pagination. Maps to docs/modules/03-plan.md |
| 08 | Notifications (4 channels) | ✅ ⚠️ | In-app bell, email (Resend), web push (service worker), SMS scaffold (Africa's Talking). Custom BrandaraDbChannel. **VAPID keys need regeneration on Linux/Render — placeholder keys used locally** |
| 09 | AI Content Engine | ✅ | ClaudeProvider + OpenAiProvider behind AiProviderFactory. ContentGenerationService with brand-aware prompts. VariationPicker (Authority/Story/Bold). **Admin AI provider switch: ready for Module 23** |
| 10 | Brand Voice | ⏳ | BrandVoiceService, voice sample input, stores profile on brand, used in generation |
| 11 | Brand Kit + Profile | ⏳ | My Brand screen, brand profile form, logo upload |
| 12 | Content pillars (advanced) | ⏳ | Pillar-tagged posts on calendar, balance alerts in AI recommendations |
| 13 | Campaign packs | ⏳ | Built-in pack library (African holidays), one-click generation, custom campaign builder |
| 14 | TikTok toolkit | ⏳ | Caption, video script, hashtag, bio copy — pure text, no API |
| 15 | Media library | ⏳ | Upload, browse, compress, platform compliance check, Canva button |
| 16 | WhatsApp assistant | ⏳ | Broadcast, status copy, follow-up sequences, WhatsApp-native tone |
| 17 | Engagement automation | ⏳ | Auto-like/comment rules, Brand Voice replies, frequency controls |
| 18 | Lead engagement tracker | ⏳ | Post engagers, enrichment, tags, notes, follow-up, CSV export |
| 19 | Analytics dashboard | ⏳ | Results screen, engagement metrics, best posting times, weekly digest |
| 20 | Billing — Paystack + Flutterwave | ⏳ | Pricing page, checkout, webhooks, trial expiry flow |
| 21 | AI Visibility & Trends | ⏳ | Queries ChatGPT/Perplexity/Gemini/Google AI, stores results, dashboard |
| 22 | Admin Panel | ⏳ | /brandara-admin, tier management, DB-driven module access, workspace mgmt |

---

## Tier access — current state (before Phase 22)

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
**Phase 06:** Schedule a post. Check it appears on calendar. Cancel — moves to drafts.
**Phase 07:** Create a pillar. Create a campaign. Check balance tracker shows it.
**Phase 08:** Check notification bell. Trigger a post failure. Verify in-app notification appears.
**Phase 09:** Enter a topic in Create. Click Generate. Verify 3 variation cards appear. Select one. Verify draft created.
**Phase 10:** Paste 10 sample posts in Brand Voice. Train. Generate a post. Verify it reflects style.
**Phase 22:** Run AI visibility query. Verify results stored. Check dashboard.
**Phase 23 (now 22):** Log into admin panel. Change tier module access. Verify sidebar updates instantly.
