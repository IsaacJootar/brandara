# Module 01 — Create (Content Brain)

## Purpose
AI content generation. User gives any input, receives 3 platform-ready
variations, picks one, edits, and publishes.

## Screens
- `/create` — main composer with input and variation picker
- `/create/variations` — 3-card variation selector (Livewire)
- `/create/tiktok` — TikTok-specific text toolkit
- `/create/whatsapp` — WhatsApp marketing copy generator

## Core workflow
1. User selects input type (topic / voice note / PDF / transcript / product)
2. Selects platforms (LinkedIn, X, Facebook, Instagram, Threads, WhatsApp, TikTok)
3. Selects tone mode
4. Clicks "Generate posts"
5. AI generates 3 variations adapted per platform
6. VariationPicker Livewire component shows 3 cards
7. Each card shows opening line + angle label
8. User taps one card — loads into composer
9. User edits, attaches media, previews, schedules

## 3 Variations — always exactly 3

| Card | Label shown | Angle |
|---|---|---|
| 1 | Authority angle | Expert positioning, teach-first |
| 2 | Story angle | Narrative, client result, human |
| 3 | Bold angle | Strong opinion, drives engagement |

All 3 saved as drafts automatically even if user picks one.

## Tone modes
corporate | professional | founder | african | friendly | educational | bold | luxury

"african" tone is trained on African professional communication patterns.

## Per-platform adaptation
Each variation is rewritten (not reformatted) per platform:
- **LinkedIn:** 1,300–3,000 chars, hook line, professional, 3–5 hashtags at end
- **X:** Under 280 chars, punchy, no hashtags unless trending
- **Facebook:** Warm, community tone, emojis OK, longer form works
- **Instagram:** Emotional hook opener, line breaks, 20–30 hashtags
- **Threads:** Casual, discussion-starting, shorter
- **WhatsApp:** Direct, personal, single action, no hashtags, emoji guidance
- **TikTok:** Caption + script + overlay text. Hook in first 3 seconds.

## TikTok toolkit
Generates (no API, pure text):
- Caption (hook-first, trend-aware, with hashtags)
- Video script (opening 3s, content arc, CTA structure)
- Text overlay copy (what to put on screen during video)
- Bio copy (optimised for TikTok discovery)

## WhatsApp toolkit
Generates:
- Broadcast message (for mass sends)
- Status update copy (short, punchy)
- Promo text (flash sale, new arrival, limited offer)
- Customer follow-up (post-enquiry or post-purchase)

## Database tables touched
- `posts` — created as draft with ai_generated=true, variation_selected set on pick
- `brands` — reads brand_voice and brand profile for generation context

## Prompts
See `docs/prompts/content-generation.md` and `docs/prompts/brand-voice.md`

## Livewire components
- `PostComposer` — main composer with real-time preview
- `VariationPicker` — 3-card variation selector
- `PlatformPreview` — live preview per platform (uses Reverb)
