# Module 03 — Plan (Content Strategy)

## Purpose
Strategic content planning. Content pillars map every post to a business goal.
Campaign packs generate complete multi-post campaigns in one click.

## Screens
- `/plan` — overview showing pillar balance and upcoming campaigns
- `/plan/pillars` — manage content pillars
- `/plan/campaigns` — campaign library (built-in + custom)
- `/plan/campaigns/new` — custom campaign builder
- `/plan/campaigns/{id}` — campaign detail and posts

## Content pillars

User creates 3–5 pillars:
- Name (e.g. Thought Leadership, Client Wins, Personal Story)
- Goal (authority / trust / awareness / conversion)
- Colour (shown on calendar and charts)

**Pillar health tracker:**
- Shows posting frequency per pillar (last 30 days)
- Flags when a pillar hasn't been posted in 2+ weeks
- Suggests content for neglected pillars
- AI recommendations: "You haven't posted on Client Wins in 3 weeks. Here are 3 ideas."

## Built-in campaign packs

One-click generation. Each pack produces 5–10 posts with captions,
hashtags, CTAs, and a timeline.

African holiday calendar (proprietary asset):
- Black Friday / Cyber Monday (November)
- Ramadan and Eid (date varies by year)
- Easter (April)
- Nigeria Independence Day — October 1
- Ghana Independence Day — March 6
- Africa Day — May 25
- School admissions season (August/September)
- Christmas / New Year
- Valentine's Day
- Product launch (generic)
- New branch / office opening (generic)

## Custom campaign builder
Fields:
- Campaign name
- Goal (awareness / leads / sales / engagement)
- Key message (1–2 sentences)
- Duration (number of days)
- Target platforms
- Tone override (optional — defaults to brand tone)

AI generates full post sequence. Each post pillar-tagged and platform-adapted.
Custom campaigns saved and reusable next year.

## AI business recommendations
Proactive suggestions surfaced on the Plan overview:
- Pillar imbalance alerts
- Seasonal campaign suggestions ("Ramadan starts in 12 days")
- Performance insights ("Your Tuesday posts get 40% more engagement")
- Platform gap alerts ("You haven't posted on LinkedIn in 8 days")

## Database tables
- `content_pillars` — pillar definitions
- `campaigns` — campaign records (builtin + custom)
- `posts` — campaign_id FK links posts to campaigns

## Constants file
`constants/campaign-packs.php` — all built-in pack data as PHP arrays
`constants/african-holidays.php` — holiday calendar by year
