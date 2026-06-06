# Module 09 — Admin Panel & Module Tier Management

## Purpose

The Brandara Admin Panel gives Isaac (the platform owner) full control over:
- Subscription tiers and what each tier unlocks
- Module/feature access per tier
- Workspace and user management
- Billing and subscription oversight

**Nothing in the sidebar or feature access is hard-coded.** All of it is
managed from this admin panel and stored in the database.

---

## The problem this solves

Without this module, changing what a "Pro" user can access requires editing
a PHP config file and redeploying. That is not acceptable for a live SaaS.

With this module, Isaac logs into the admin panel, changes which modules
a tier can access, saves — and every workspace on that tier sees the change
immediately.

---

## Database tables

### subscription_tiers

Defines the plans available on the platform.

| Column | Type | Notes |
|---|---|---|
| id | uuid | Primary key |
| key | string unique | starter, pro, agency — matches workspaces.plan |
| name | string | Display name e.g. "Pro Plan" |
| description | text nullable | Short description for pricing page |
| price_ngn | integer | Monthly price in kobo (Paystack) |
| price_usd | integer | Monthly price in cents (Flutterwave USD) |
| is_active | boolean | Whether this tier is available for signup |
| sort_order | integer | Display order on pricing page |
| created_at / updated_at | timestamps | Standard |

### modules

Defines every navigable feature module in the app.

| Column | Type | Notes |
|---|---|---|
| id | uuid | Primary key |
| key | string unique | Matches the route name: create, plan, schedule, grow, results, ai-presence, my-brand, connections, dashboard |
| name | string | Display name e.g. "AI Visibility & Trends" |
| description | text nullable | What this module does |
| icon | text | SVG path string for sidebar icon |
| section | string nullable | Sidebar grouping: Content, Growth, Brand, null |
| sort_order | integer | Order within its section |
| is_active | boolean | Whether module exists at all (for disabling entire modules) |
| created_at / updated_at | timestamps | Standard |

### tier_module_access

The join table. One row = "this tier can access this module".

| Column | Type | Notes |
|---|---|---|
| id | uuid | Primary key |
| tier_id | uuid FK | Links to subscription_tiers |
| module_id | uuid FK | Links to modules |
| created_at / updated_at | timestamps | Standard |

Unique constraint: `[tier_id, module_id]`

---

## How the sidebar reads access (future state)

When the admin module is built, `config/navigation.php` is retired.
The sidebar will read directly from the database:

```php
// In ResolveBrand middleware (or AppServiceProvider boot):
$allowedModules = TierModuleAccess::with('module')
    ->where('tier_id', auth()->user()->workspace->tier_id)
    ->pluck('module.key')
    ->toArray();

app()->instance('allowed.modules', $allowedModules);

// In sidebar Blade:
$allowed = in_array($item['key'], app('allowed.modules'));
```

This means:
- No Blade changes needed to change tier access
- Change takes effect instantly for all users on that tier
- Isaac can manage it from the admin panel without a developer

---

## Migration path from config to database

**Phase 1 (current):** `config/navigation.php` — single file, edit and
deploy to change tier access. Safe for early launch.

**Phase 2 (after Module 09 is built):** Seed the `modules` and
`subscription_tiers` tables from the config file. Retire the config.
All access checks read from DB. Admin panel manages it from UI.

---

## Admin panel screens

### Dashboard
- Total workspaces by tier
- MRR (monthly recurring revenue) estimate
- Trial conversions this week
- Failed payments requiring follow-up

### Workspaces
- List all workspaces with plan, status, trial end, owner
- Search and filter
- View workspace details
- Manually change a workspace's plan (for support/deals)
- Cancel or refund a subscription

### Modules
- List all modules with their section, sort order, active status
- Toggle a module on/off globally (e.g. disable a module in maintenance)
- Edit module name and description

### Tier Management
- List all tiers with pricing
- For each tier: checkbox list of which modules it includes
- Save changes — all workspaces on that tier see the update immediately
- Add new tiers (e.g. "Enterprise")

### Billing Overview
- All Paystack and Flutterwave transactions
- Failed payments list
- Refund management

---

## Access control

The admin panel is only accessible to users with `role = 'owner'` on the
**platform workspace** (the Brandara workspace itself, not customer workspaces).

Routes live at `/brandara-admin/*` — completely separate from the tenant
routes at `/{brand}/*`.

Middleware: `EnsurePlatformAdmin` — checks:
1. User is authenticated
2. User's workspace is the platform workspace (identified by a config key)
3. User role is `owner` or `admin`

---

## Current state (before Module 09 is built)

The sidebar reads from `config/navigation.php`. This file is the temporary
single source of truth for tier access.

**To change tier access before Module 09:**
1. Edit `config/navigation.php`
2. Change the `tiers` array on the relevant item
3. Run `php artisan config:clear`
4. No deployment needed in local dev

**Current tier assignments:**
- Starter: Dashboard, Create, Plan, Schedule, Grow, Results, My Brand, Connections
- Pro: Everything above + AI Visibility & Trends
- Agency: Everything above + unlimited brands

---

## Build order

Build Module 09 AFTER:
- Module 21 (Billing) — so tiers have real prices
- All feature modules are built — so all modules exist to assign

Module 09 is the final admin layer that makes the platform fully
self-manageable without developer intervention.
