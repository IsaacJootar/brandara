# Module 06 — Grow (Engagement & Growth Engine)

## Purpose
Engagement automation drives algorithmic reach.
WhatsApp assistant drives African business sales.
Lead tracker turns engagement into a pipeline.

## Screens
- `/grow` — overview with engagement stats and lead count
- `/grow/automation` — auto-like and auto-comment rules
- `/grow/leads` — lead engagement tracker
- `/grow/whatsapp` — WhatsApp Marketing Assistant

## Engagement automation (Pro + Agency)

**Auto-like:**
- Define accounts to auto-like posts from
- Define keywords — auto-like any post containing these words
- Set daily limit per platform (e.g. 50 likes/day on LinkedIn)
- Frequency controls stay within platform guidelines

**Auto-comment:**
- AI reads the target post (not a template — actual post content)
- Generates a genuine contextual comment in the user's Brand Voice
- Comment sounds like them, not a bot
- Rules: comment on posts by [person list], containing [keyword], in [industry]
- Review queue: optional — user reviews AI comments before they post
- Daily limit controls

## WhatsApp Marketing Assistant

Generates WhatsApp-specific copy. Tone adapted for WhatsApp:
shorter, more direct, personal, emoji placement guidance included.

**Types:**
1. **Broadcast message** — promotional copy for mass sends
   Input: offer/announcement, deadline, link
   Output: 2–3 sentence WhatsApp message ready to copy

2. **Status update** — short punchy WhatsApp status
   Input: what to announce
   Output: 1–2 lines with emoji

3. **Promo text** — flash sale, new arrival, limited offer
   Input: product, price, deadline
   Output: WhatsApp message with urgency and CTA

4. **Customer follow-up** — post-enquiry or post-purchase
   Input: context (enquired about X / just bought Y)
   Output: warm follow-up message

Campaign-linked: if a campaign is active, WhatsApp messages tie into it
for consistent cross-channel messaging.

Note: Direct API send (auto-send via WhatsApp Business API) in v2.

## Lead engagement tracker

**Data pulled from platform APIs:**
- Who liked, commented, and reshared every post
- Profile enrichment: job title, company, industry from public data

**Lead actions:**
- Tag: warm_lead / prospect / client / partner / other
- Add notes: "Met at Lagos Tech Summit 2025"
- Set follow-up reminder: date picker, notification sent
- View engagement history: every interaction across all posts

**Dashboard:**
- Total leads this month
- Leads by tag
- Follow-ups due this week (highlighted)
- Top engaged contacts

**Export:** CSV download for CRM import (HubSpot, Notion, Google Sheets)

## Database tables
- `leads` — all lead records with enrichment data
- `posts` — engagement data stored per post
