# Brandara — UI Rules & Language

## The one test before anything ships

> Could a Nigerian business owner with no marketing background understand
> this immediately, without a tooltip or tutorial?
> If no — it is not ready.

---

## Navigation names — fixed forever

| Internal name | UI label | Why |
|---|---|---|
| Content Brain | Create | Short, active, immediately understood |
| Content Strategy | Plan | Users are planning campaigns |
| Publishing Engine | Schedule | The action they're taking |
| Engagement & Growth | Grow | The outcome they want |
| Analytics & Intelligence | Results | They want results, not analytics |
| Brand Kit + Voice DNA + Brand Profile | My Brand | It's all about their brand |
| Platform OAuth Connections | Connections | Simple, non-technical |
| Agency / Client Workspace | Clients | Agencies think in clients |
| Draft & Approval Workflow | Review | What actually happens |
| Failed Post Queue | Fix & Retry | What the user needs to do |
| AI Visibility Module | AI Presence | Their presence in AI answers |
| Horizon Dashboard | Queue Monitor | Plain English |

---

## Button language — always a verb

| Never use | Use instead |
|---|---|
| Generate content | Write a post |
| Submit for approval | Send for review |
| Approve | Looks good — publish it |
| Reject | Needs changes |
| Initiate campaign | Start a campaign |
| Configure integration | Connect LinkedIn |
| Authenticate | Log in to connect |
| Terminate session | Disconnect |
| Initiate generation | Generate posts |
| Select variation | Use this version |

---

## Content variation picker labels

| Never use | Use instead |
|---|---|
| Variation A | Authority angle |
| Variation 1 | Lead with expertise |
| Option B | Story angle |
| Version 2 | Lead with a client result |
| Variation C | Bold angle |
| Option 3 | Lead with a strong opinion |
| Please select | Pick the version that fits |

---

## Status labels

| Never use | Use instead |
|---|---|
| Draft | Not published yet |
| Pending approval | Waiting for review |
| Scheduled | Going out on [date] |
| Published | Live — view post |
| Failed | Did not publish — fix it |
| Token expired | Connection lost — reconnect |
| In queue | Up next |
| Cancelled | Removed from schedule |

---

## Error and system messages

| Raw / technical | Plain English |
|---|---|
| OAuth token refresh failed | Your LinkedIn connection expired. Reconnect in 30 seconds. |
| API rate limit exceeded | LinkedIn needs a short break. We'll retry in 15 minutes. |
| Media format not supported | Instagram rejected this image. Use JPG or PNG under 8MB. |
| Content policy violation | This post was flagged. Review it before publishing. |
| Publish job failed after max retries | This post didn't go live. Tap to retry or reschedule. |
| AI query returned no results | Your brand wasn't mentioned in AI answers this week. Here's what to do. |
| Queue worker not running | Posts aren't being published. Contact support. |
| VAPID key error | Browser notifications need a quick fix. Tap here. |

---

## Notification copy

**Push notifications (short — browser shows limited characters):**
- "LinkedIn post failed — tap to fix"
- "Your trial ends in 3 days — keep your posts going"
- "New approval request from [client name]"
- "Your results are in — [X] posts this week"

**Email subject lines:**
- "Your post didn't publish — here's what happened"
- "3 days left on your free trial"
- "[Client name] is waiting for your approval"
- "Your Brandara results for this week"

---

## Form labels

| Never use | Use instead |
|---|---|
| Input topic | What do you want to post about? |
| Brand vision statement | Where do you want your brand to be in 3 years? |
| Mission statement | Why does your business exist? |
| Negative brief | What does your brand never say? |
| Target audience persona | Who are you speaking to? |
| Content pillar name | What is this content category about? |
| Pillar goal | What do you want this content to achieve? |
| Post scheduled_at | When should this go out? |
| Retry count | Times retried |

---

## Structural rules

**One primary action per screen.**
Every screen has one clear thing to do. Secondary actions are visible but never competing for attention. The user should never wonder which button to press first.

**Progress is always visible.**
- Generating posts? Show the AI generating per variation.
- Publishing? Show per-platform publish status.
- A failed post? Surface it immediately — not buried in a sub-menu.

**Recovery is always one tap away.**
Every error message is also the path to resolution.
"Your LinkedIn connection expired. Reconnect in 30 seconds." — tap that message and the reconnect flow opens.

**Three cards for variations.**
When generation is complete, show exactly 3 cards side by side.
Each card shows the opening line and the angle label.
One tap selects. All 3 saved as drafts automatically.

---

## Platform names in UI

| Internal | User-facing |
|---|---|
| twitter | X |
| linkedin | LinkedIn |
| facebook | Facebook |
| instagram | Instagram |
| threads | Threads |
| whatsapp | WhatsApp |
| tiktok | TikTok |

---

## Colours in UI — no hex codes shown to users

| Meaning | Colour | User sees |
|---|---|---|
| Primary action | #7C3AED violet | Purple button |
| Secondary / CTA | #F59E0B gold | Gold/amber button |
| Success / live | #10B981 | Green indicator |
| Failure / error | #EF4444 | Red indicator |
| Warning | #F59E0B amber | Amber warning |
| Neutral | #6B7A8D | Grey label |
