# Brandara — Database Schema

## Single shared database

All data lives in one database. Workspaces share the database.
Data isolation is enforced by `brand_id` scoping on every query.

**The rule:** every table that holds brand data has a `brand_id` column.
Every query on that table MUST include `WHERE brand_id = ?`.

---

## Schema overview

```
workspaces
  └── users (workspace_id)
  └── brands (workspace_id)
        └── platform_connections (brand_id)
        └── content_pillars (brand_id)
        └── campaigns (brand_id)
        └── posts (brand_id)
        └── media_files (brand_id)
        └── leads (brand_id)
        └── ai_visibility_reports (brand_id)
        └── notifications (user_id — user-level, not brand-level)
```

---

## workspaces

The subscription/account container. One per customer (individual or agency).

| Column | Type | Notes |
|---|---|---|
| id | uuid | Primary key |
| name | string | Workspace/company name |
| slug | string unique | Used in internal references |
| owner_email | string | Primary account email |
| country | string(2) | ISO country code — for payment routing |
| timezone | string | Default: Africa/Lagos |
| plan | enum | starter, pro, agency |
| trial_ends_at | timestamp nullable | 7 days from creation |
| subscription_status | enum | trialing, active, past_due, cancelled |
| paystack_customer_id | string nullable | Set on first Paystack payment |
| flutterwave_customer_id | string nullable | Set on first Flutterwave payment |
| language | enum default en | en, fr |
| created_at / updated_at | timestamps | Standard |

---

## users

People who log in. Belong to a workspace. Can manage all brands under their workspace.

| Column | Type | Notes |
|---|---|---|
| id | uuid | Primary key |
| workspace_id | uuid FK | Links to workspaces |
| name | string | Full name |
| email | string unique | Login email |
| password | string hashed | Bcrypt |
| role | enum | owner, admin, editor, viewer |
| avatar_url | string nullable | Profile picture |
| last_active_at | timestamp nullable | Analytics usage |
| remember_token | string nullable | Standard |
| created_at / updated_at | timestamps | Standard |

> Add `use NotificationChannels\WebPush\HasPushSubscriptions;` to User model

---

## brands

The actual brand being managed. One workspace can have many brands (agency use case).
The `slug` is used in the URL: `brandara.com/{slug}/dashboard`.

| Column | Type | Notes |
|---|---|---|
| id | uuid | Primary key |
| workspace_id | uuid FK | Links to workspaces |
| name | string | Brand or business name |
| slug | string | URL slug — unique per workspace |
| tagline | string nullable | One-line description |
| description | text nullable | What the business does |
| vision | text nullable | Where the brand wants to be in 3 years |
| mission | text nullable | Why the business exists |
| values | json nullable | Array of {title, description} objects |
| target_audience | text nullable | Who they speak to |
| negative_brief | text nullable | What the brand never says |
| positioning | text nullable | How they differ from competitors |
| primary_color | string nullable | Hex code |
| secondary_color | string nullable | Hex code |
| font_preference | string nullable | Preferred font name |
| logo_path | string nullable | Path in storage |
| brand_voice | json nullable | Trained voice profile from Claude |
| voice_samples_count | integer default 0 | Posts used to train voice |
| default_tone | enum | corporate, professional, founder, african, friendly, educational, bold, luxury |
| language | enum default en | en, fr |
| created_at / updated_at | timestamps | Standard |

> Unique constraint: `[workspace_id, slug]` — same slug can exist in different workspaces

---

## platform_connections

OAuth connections for a specific brand. Scoped to brand_id.

| Column | Type | Notes |
|---|---|---|
| id | uuid | Primary key |
| brand_id | uuid FK | Links to brands — ALWAYS scope queries here |
| platform | enum | linkedin, twitter, facebook, instagram, threads |
| platform_user_id | string | User ID on that platform |
| platform_username | string nullable | Display name / handle |
| access_token | text encrypted | OAuth token — encrypted at rest |
| refresh_token | text encrypted nullable | For refresh-capable platforms |
| token_expires_at | timestamp nullable | When access token expires |
| status | enum | connected, expired, disconnected, error |
| last_posted_at | timestamp nullable | Last successful publish |
| follower_count | integer default 0 | Synced periodically |
| created_at / updated_at | timestamps | Standard |

---

## content_pillars

Up to 5 content pillars per brand. Scoped to brand_id.

| Column | Type | Notes |
|---|---|---|
| id | uuid | Primary key |
| brand_id | uuid FK | Links to brands |
| name | string | e.g. Thought Leadership, Client Wins |
| goal | enum | authority, trust, awareness, conversion |
| color | string | Hex — for colour-coded calendar |
| sort_order | integer | Display order |
| is_active | boolean default true | Soft toggle |
| created_at / updated_at | timestamps | Standard |

---

## campaigns

Campaign packs and custom campaigns. Scoped to brand_id.

| Column | Type | Notes |
|---|---|---|
| id | uuid | Primary key |
| brand_id | uuid FK | Links to brands |
| name | string | e.g. Black Friday 2025 |
| type | enum | builtin, custom |
| pack_key | string nullable | e.g. black_friday, ramadan |
| goal | text nullable | Campaign objective |
| key_message | text nullable | Core message |
| start_date | date nullable | Campaign start |
| end_date | date nullable | Campaign end |
| platforms | json | Target platforms array |
| status | enum | draft, active, completed, archived |
| created_at / updated_at | timestamps | Standard |

---

## posts

Every post belongs to a brand. Scoped to brand_id.

| Column | Type | Notes |
|---|---|---|
| id | uuid | Primary key |
| brand_id | uuid FK | Links to brands — ALWAYS scope queries here |
| content_pillar_id | uuid FK nullable | Links to content_pillars |
| campaign_id | uuid FK nullable | Links to campaigns |
| created_by | uuid FK | User who created (users table) |
| approved_by | uuid FK nullable | User who approved |
| title | string nullable | Internal reference only |
| input_type | enum | topic, voice_note, pdf, transcript, product, manual |
| raw_input | text nullable | Original input before AI processing |
| ai_generated | boolean default false | Was this AI generated |
| variation_selected | enum nullable | authority, story, bold |
| platform_contents | json nullable | {linkedin:{body,hashtags}, twitter:{body}, facebook:{body}, instagram:{body,hashtags}, threads:{body}, whatsapp:{body}, tiktok:{caption,script,hashtags}} |
| tone | string nullable | Tone mode used |
| media_ids | json nullable | Array of media_file IDs attached |
| status | enum | draft, in_review, scheduled, published, failed, cancelled |
| scheduled_at | timestamp nullable | When to publish |
| published_at | timestamp nullable | When actually published |
| failure_reason | text nullable | Plain English failure description |
| retry_count | integer default 0 | Number of retry attempts |
| live_post_urls | json nullable | {linkedin: url, twitter: url...} |
| is_evergreen | boolean default false | Recycle in queue |
| last_recycled_at | timestamp nullable | Last time recycled |
| created_at / updated_at | timestamps | Standard |

---

## media_files

Brand media library. Stored at `storage/app/brands/{brand_id}/media/`.

| Column | Type | Notes |
|---|---|---|
| id | uuid | Primary key |
| brand_id | uuid FK | Links to brands |
| uploaded_by | uuid FK | User who uploaded |
| filename | string | Original filename |
| storage_path | string | brands/{brand_id}/media/{filename} |
| mime_type | string | image/jpeg, image/png, etc. |
| file_size_kb | integer | Storage quota tracking |
| width | integer nullable | Image width in pixels |
| height | integer nullable | Image height in pixels |
| alt_text | string nullable | Accessibility |
| tags | json nullable | User-added tags for search |
| created_at / updated_at | timestamps | Standard |

---

## leads

People who engaged with brand content. Scoped to brand_id.

| Column | Type | Notes |
|---|---|---|
| id | uuid | Primary key |
| brand_id | uuid FK | Links to brands |
| platform | enum | Platform they engaged on |
| platform_user_id | string | Their platform ID |
| name | string nullable | Display name |
| headline | string nullable | Job title from public profile |
| company | string nullable | Company from public profile |
| profile_url | string nullable | Public profile link |
| tag | enum nullable | warm_lead, prospect, client, partner, other |
| notes | text nullable | Internal notes |
| follow_up_at | date nullable | Reminder date |
| total_engagements | integer default 0 | Running count |
| last_engaged_at | timestamp nullable | Most recent engagement |
| created_at / updated_at | timestamps | Standard |

---

## ai_visibility_reports

AI presence tracking per brand.

| Column | Type | Notes |
|---|---|---|
| id | uuid | Primary key |
| brand_id | uuid FK | Links to brands |
| ai_system | enum | chatgpt, perplexity, gemini, google_ai, claude |
| query | text | The question asked to the AI system |
| response_text | text | Full AI response captured |
| brand_mentioned | boolean | Was the brand in the response |
| mention_position | integer nullable | Position in response (1=first) |
| sentiment | enum nullable | positive, neutral, negative |
| topics | json nullable | Topics the brand appeared for |
| report_date | date | When this was checked |
| created_at / updated_at | timestamps | Standard |

---

## notifications

User-level notifications (not brand-scoped — a user can get notifications about any brand they manage).

| Column | Type | Notes |
|---|---|---|
| id | uuid | Primary key |
| user_id | uuid FK | Who receives this |
| brand_id | uuid FK nullable | Which brand this relates to |
| type | string | post_failed, approval_needed, trial_expiring, token_expired, weekly_report, ai_visibility_alert |
| title | string | Plain English title |
| message | text | Full description + next step |
| action_url | string nullable | Deep link to relevant screen |
| channels | json | [in_app, mail, sms, web_push] |
| read_at | timestamp nullable | Null = unread |
| sent_at | timestamp nullable | When dispatched |
| created_at / updated_at | timestamps | Standard |

---

## push_subscriptions

Web push subscriptions per user (from laravel-notification-channels/webpush).
Standard package table — do not modify.
