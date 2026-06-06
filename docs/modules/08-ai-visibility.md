# Module 08 — AI Presence (AI Visibility)

## Purpose
Track how the brand appears in AI-generated answers across ChatGPT,
Perplexity, Gemini, Google AI Overviews, and Claude.
Help brands understand and improve their footprint in AI search.

**Built last in v1.** All 7 core modules must be complete before Phase 22.

## Why this matters
As AI assistants replace traditional search for discovery and recommendations,
appearing in AI-generated answers becomes as important as appearing on page 1
of Google. Most brands have no idea what AI systems say about them.
No competitor at Brandara's price point monitors this.

## Screens
- `/ai-presence` — main dashboard with presence score
- `/ai-presence/mentions` — all AI mentions log
- `/ai-presence/topics` — topic ownership map
- `/ai-presence/competitors` — competitor comparison
- `/ai-presence/recommendations` — content gaps → content briefs

## Core features

### Brand mention monitoring
AiVisibilityService queries AI systems with questions relevant to the brand.

African-market-specific query library:
- "Best [industry] consultants in Lagos"
- "Top [industry] agencies in Nigeria"
- "Recommended [service] providers in Accra"
- "Leading [industry] companies in Nairobi"
- "Best [profession] in South Africa"

Also queries in general professional context:
- "Who should I hire for [service]?"
- "What is the best [product category] for [use case]?"
- "Recommend a [profession] who specialises in [topic]"

Query library is per-brand — populated from brand profile industry and services.
Runs weekly by default (more frequent on Pro/Agency).

### AI systems queried
- OpenAI ChatGPT (via API)
- Perplexity AI (via API)
- Google Gemini (via API)
- Google AI Overviews (via web scraping — carefully)
- Anthropic Claude (via API)

Results stored in `ai_visibility_reports` table.

### AI Presence score
Calculated from:
- Mention frequency (how often brand appears)
- Position (first mention vs later)
- Sentiment (positive/neutral/negative)
- Topic coverage (how many relevant topics)

Score displayed as 0–100. Trend over last 30 days shown.

### Sentiment tracking
When brand is mentioned:
- Positive: AI recommends or praises
- Neutral: mentioned without strong framing
- Negative: mentioned with caveats

Sentiment trend chart. Alert when sentiment drops.

### Topic ownership map
Visual map showing:
- Topics where brand appears in AI answers (owned)
- Topics brand should appear for but doesn't (gaps)
- Competitors who appear for gap topics

Example insight:
"You appear for 'LinkedIn strategy in Lagos' but not for
'personal branding for consultants in Nigeria' — this is a gap."

### Competitor visibility comparison
User adds 2–3 competitor names.
System tracks their AI visibility alongside the brand.
Side-by-side: who appears more, who is described better, who owns which topics.

### Content recommendations (connects to Create)
Gap topics → content briefs → fed directly into Plan module.
"You don't appear for [topic]. Here are 3 post ideas to close this gap."
User taps → opens Create module with brief pre-populated.

This is the Brandara flywheel: content → publish → AI indexes → track visibility
→ find gaps → create content → publish → repeat.

### Citation tracking
Monitors whether content published through Brandara is referenced in AI answers.
Shows which posts have generated AI citations.
"Your LinkedIn post about X is being referenced by Perplexity."

## Database tables
- `ai_visibility_reports` — all query results stored
- `brands` — reads industry and services for query generation
- `posts` — citation tracking cross-references published posts

## Prompts
See `docs/prompts/ai-visibility-query.md`

## Plan gating
- AI Presence: Pro + Agency only (not Starter)
- Competitor comparison: Agency only
- Weekly automated queries: Pro (manual trigger on Starter)
