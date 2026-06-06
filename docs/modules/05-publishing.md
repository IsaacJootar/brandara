# Module 05 — Schedule (Publishing Engine)

## Purpose
Post composition, platform preview, scheduling, OAuth management,
and the 5-layer failure recovery system.

## Screens
- `/schedule` — visual content calendar (main view)
- `/schedule/queue` — queue management
- `/schedule/failed` — Fix & Retry screen
- `/connections` — platform OAuth management

## Post composer
Livewire component: `PostComposer`

Features:
- Import generated variations from Create module
- Manual write mode (blank canvas)
- Platform selector (checkboxes for each platform)
- Per-platform content editor (separate tab per platform)
- Media attachment (upload / pick from library / open Canva)
- Live platform preview via PlatformPreview Livewire component
- Character count per platform (warns at 80%, errors at 100%)
- Compliance validation before scheduling:
  - Instagram: "This post requires an image to publish to Instagram"
  - LinkedIn: "Caption exceeds 3,000 characters"

## Platform preview
- Powered by Laravel Reverb (WebSocket)
- Updates in real time as user types
- Shows realistic rendering per platform
- Correct fonts, spacing, hashtag formatting, link preview

## OAuth connections
Each platform uses OAuth 2.0. No passwords stored ever.
Tokens encrypted using Laravel's encryption (`Crypt::encrypt()`).

**Connection flow:**
1. User clicks "Connect LinkedIn"
2. Redirect to LinkedIn OAuth
3. LinkedIn returns `code`
4. Exchange for `access_token` + `refresh_token`
5. Store encrypted in `platform_connections`
6. Show green status indicator

**Token monitoring:**
- Daily job checks all tokens expiring in 7 days
- Amber indicator appears 7 days before expiry
- Red indicator + web push + email when expired
- "Reconnect" button triggers OAuth flow again

## Scheduling
- Schedule to specific date and time
- Queue mode: set frequency (e.g. 3x per week), Brandara fills slots
- Best-time suggestions from platform analytics
- Drag-and-drop reschedule on calendar

## Evergreen recycling
- User marks post as evergreen
- System re-queues automatically every 60–90 days
- Tracks last recycled date to prevent close duplicates

## 5-layer failure recovery

**Layer 1 — Silent retry:**
Fails → retry at 2 min → retry at 5 min → retry at 15 min.
Most transient API failures resolve here. User sees nothing.

**Layer 2 — Error classification:**
- `token_expired` → do not retry, trigger reconnect prompt
- `rate_limited` → wait longer (30 min), then retry
- `media_rejected` → flag to user immediately, do not retry
- `network_timeout` → Layer 1 retry as normal

**Layer 3 — User notification:**
After 3 failed retries:
- In-app notification (NotificationBell updates live via Reverb)
- Email via Resend (plain English subject + reason)
- SMS via Africa's Talking (brief)
- Web push notification (browser alert even if tab closed)
Message format: "Your LinkedIn post scheduled for 9AM didn't go live. [Reason]. Tap to fix."

**Layer 4 — Fix & Retry screen:**
`/schedule/failed` — shows all failed posts with:
- Original scheduled time
- Plain English failure reason
- "Retry now" button
- "Reschedule" button
- Post content (editable before retry)
Users never lose post content due to a failure.

**Layer 5 — Publish confirmation:**
On success: pull live post URL from platform API, store in `posts.live_post_urls`.
Every published post on the calendar shows "View live post" link.

## Approval workflow (Agency/Pro)
Draft → Send for Review → Client reviews → Approved / Needs Changes
- Client receives email + web push notification
- Client review portal (no full platform login needed)
- Approval moves post to scheduled queue automatically
- Audit trail: who approved what and when

## Database tables
- `posts` — all post data including status and failure info
- `platform_connections` — OAuth tokens
