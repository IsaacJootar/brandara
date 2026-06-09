# Brandara — Claude Code Master Instructions

> Read this file at the start of every session. Every decision traces back here.

## What this product is

Brandara is a multi-tenant SaaS social media management and AI brand intelligence
platform for B2B founders, consultants, and agencies — built with Africa and
emerging markets as the primary market.

**Tagline:** AI-powered personal brand and content intelligence platform.
**Market:** Nigeria, Ghana, Kenya, South Africa — expanding globally.
**Builder:** Solo founder, non-technical. Claude Code is the engineering team.

---

## Stack — do not suggest alternatives without asking

| Layer | Decision |
|---|---|
| Framework | Laravel 13 (PHP 8.3+) |
| Frontend templates | Blade |
| CSS | Tailwind CSS + DaisyUI |
| JS interactivity | Alpine.js |
| Complex UI / real-time | Livewire 3 |
| WebSockets | Laravel Reverb (self-hosted, free) |
| Database (local) | SQLite |
| Database (production) | PostgreSQL via Supabase free tier |
| ORM | Laravel Eloquent |
| Multi-tenancy | stancl/tenancy for Laravel |
| Auth | Laravel Breeze (email + password) |
| Background jobs | Laravel Horizon + Redis |
| Web push notifications | laravel-notification-channels/webpush |
| Email | Resend (free tier) |
| SMS | Africa's Talking |
| Payments | Paystack + Flutterwave (NO Stripe) |
| AI | Anthropic Claude API (claude-sonnet-4-5) |
| File storage | Laravel local storage → Supabase Storage |
| Performance monitoring | Laravel Pulse |
| App monitoring | Laravel Telescope (dev only) |
| AI dev assistant | Laravel Boost (MCP server for Claude Code) |
| Hosting | Render.com — Frankfurt region (closest to Africa) |

---

## Before writing any code — read these files

- `docs/architecture.md` — multi-tenant structure, ALWAYS read before any DB work
- `docs/database.md` — complete schema, ALWAYS read before any model/migration work
- `docs/ui-rules.md` — naming rules, ALWAYS read before any Blade/Livewire work
- `docs/stack.md` — packages and services, read when installing anything
- `docs/colors.md` — complete colour system, read before any CSS/Tailwind work
- The relevant `docs/modules/` file for whatever module you are building
- The relevant `docs/prompts/` file when building any AI feature

---

## Navigation names — FIXED, do not change

| Internal name | UI label shown to user |
|---|---|
| Content Brain | Create |
| Content Strategy | Plan |
| Publishing Engine | Schedule |
| Engagement & Growth | Grow |
| Analytics & Intelligence | Results |
| Brand Kit + Brand Voice + Profile | My Brand |
| Platform Connections | Connections |
| Client Workspace | Clients |
| Approval Workflow | Review |
| Failed Post Queue | Fix & Retry |
| AI Visibility Module | AI Presence |

---

## Output rules

- Respond concisely
- Drop all articles, filler words, pleasantries, and sign-offs
- Do not restate the problem or task
- Provide only the direct answer or requested code
- Do not explain code unless explicitly asked

## Golden rules — non-negotiable

1. **Plain language only** — every button, label, message must be understood
   by a non-technical Nigerian business owner with zero training.

2. **Multi-tenancy is sacred** — every database query MUST be scoped to the
   current tenant. Never write a global query on tenant data. Tenant ID is
   always the first filter. Read `docs/architecture.md` before any DB work.

3. **Mobile responsive** — every screen works at 375px minimum width.
   Use Tailwind responsive prefixes (sm: md: lg:) on every layout element.

4. **Failures are handled gracefully** — every external API call (Claude,
   platforms, SMS, email) wrapped in try-catch. Show plain English to user.
   Never show raw errors or stack traces to users.

5. **Ask before assuming** — if a requirement is unclear, stop and ask.
   Do not silently pick an approach and build 200 lines on a wrong assumption.

6. **Surgical changes** — only touch what the task requires. Do not refactor
   or improve adjacent code unless explicitly asked.

7. **One phase at a time** — complete and test each phase before starting
   the next. See `docs/phases.md` for the full build sequence.

8. **Do not invent a new architecture** — use the architecture defined in
   `docs/architecture.md` exactly as written. No creative alternatives.

9. **Implement only according to the docs** — every feature, screen, model,
   and service must trace back to a decision in the docs/ folder. If it is
   not documented, stop and ask before building it.

10. **Keep the system simple and modular** — one responsibility per class,
    one purpose per file, one action per screen. If a solution feels complex,
    there is a simpler way. Find it.

11. **Commit after completing each module** — when a module is fully built
    and tested, run git add, git commit, and git push before doing anything else.

12. **Stop and ask Isaac to approve before continuing** — after every completed
    module, stop completely. Report what was built, confirm it works, and wait
    for Isaac's explicit approval before starting the next module.

13. **Do not proceed to the next module without Isaac's approval** — silence
    is not approval. A thumbs up is not approval. Wait for Isaac to say
    "approved" or "go ahead" before writing a single line of the next module.

14. **Keep commits clear and module-based** — every commit message must
    name the module and what was completed. Example: "Module 01 complete —
    Content Brain: AI generation, 3 variations, Brand Voice". No vague messages.

15. **Do not stop until a full module is complete** — do not pause halfway
    through a module because something is difficult. Work through it. A module
    is only done when every feature in its docs/modules/ file is built, tested,
    committed, and pushed. Then stop and wait for approval.

---

## Laravel Boost — MCP server setup

Laravel Boost gives Claude Code real-time access to your codebase.
Install it in the project before starting any coding session:

```bash
composer require laravel/boost --dev
php artisan boost:install
```

Then in Claude Code settings, enable the laravel-boost MCP server.
This lets Claude Code read your routes, models, schema, and logs live.

---

## Architecture in one paragraph

Brandara uses stancl/tenancy for multi-tenancy. Each workspace (tenant) gets its
own isolated database. The central database stores only workspace records and
routing. All business data — posts, brands, platforms, campaigns, leads — lives
in the tenant database. The tenancy package switches database connections
automatically on each request based on the subdomain.

---

## Payments — Paystack and Flutterwave ONLY

**No Stripe.** African users pay via Paystack (NGN, GHS, KES, ZAR) or
Flutterwave (pan-African, 30+ currencies). Both have webhook handlers.
See `docs/api-integrations.md` for full integration details.

---

## Content generation — 3 variations always

Every AI content generation produces exactly 3 variations:
- Variation 1: Authority angle (expert positioning)
- Variation 2: Story angle (narrative / client result)
- Variation 3: Bold/Opinion angle (strong take, drives engagement)

Each variation is generated across ALL selected platforms simultaneously,
adapted for each platform's format, tone, and algorithm. See `docs/prompts/`.

---

## Git identity — all commits must use

```bash
git config user.name "Isaac Jootar"
git config user.email "jootarisaac@gmail.com"
```

Run this at the start of every session to confirm identity is set.

---

## Current build phase

Update this line when starting a new phase:
**CURRENT PHASE: 10 — Brand Voice** (next to build)

Modules 01–09, 13, 14, 15 complete and committed.
**Modules 10, 11, 12 were skipped — must be built before continuing to 16.**
See `docs/build-status.md` for full status.

See `docs/phases.md` for all 22 phases.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3
- laravel/breeze (BREEZE) - v2
- laravel/framework (LARAVEL) - v13
- laravel/horizon (HORIZON) - v5
- laravel/prompts (PROMPTS) - v0
- laravel/pulse (PULSE) - v1
- laravel/reverb (REVERB) - v1
- livewire/livewire (LIVEWIRE) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- phpunit/phpunit (PHPUNIT) - v12
- alpinejs (ALPINEJS) - v3
- tailwindcss (TAILWINDCSS) - v3

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== livewire/core rules ===

# Livewire

- Livewire allow to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>
