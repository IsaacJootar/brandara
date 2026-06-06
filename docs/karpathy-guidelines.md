# Behavioral Guidelines — Karpathy Principles for Brandara

Derived from Andrej Karpathy's observations on LLM coding pitfalls.
Adapted for the Brandara Laravel + Blade + Tailwind + Livewire stack.

**Tradeoff:** These guidelines bias toward caution over speed.
For trivial tasks, use judgment. For anything touching the database,
multi-tenancy, OAuth tokens, or the publish/retry system — follow every rule.

---

## 1. Think Before Coding

**Don't assume. Don't hide confusion. Surface tradeoffs.**

Before implementing anything:

- State your assumptions explicitly. If uncertain, ask the founder.
- If multiple approaches exist for a Laravel problem, present them briefly
  and wait for direction. Do not pick silently.
- If a simpler approach exists, say so and recommend it.
- If something about the Brandara spec is unclear, stop and name what
  is confusing. Ask one focused question. Do not guess and build 200
  lines on top of a wrong assumption.
- If a request could break multi-tenancy or expose one workspace's data
  to another, stop immediately and flag it before writing any code.

**Brandara-specific examples:**

- "Add a posts query" → ask: is this scoped to the current tenant?
  Never assume global scope.
- "Add a settings page" → ask: is this workspace settings or user
  settings? They live in different places in the schema.
- "Cache this data" → ask: is this per-tenant cache or global?
  A global cache on tenant data is a data leak.

---

## 2. Simplicity First

**Minimum code that solves the problem. Nothing speculative.**

- No features beyond what was asked.
- No abstractions for single-use code.
- No "flexibility" or "configurability" that was not requested.
- No error handling for impossible scenarios.
- If you write 200 lines and it could be 50, rewrite it.
- Do not add helper methods, traits, or base classes unless the
  founder specifically asked for reusable architecture.

Ask yourself: "Would a senior Laravel engineer say this is
overcomplicated?" If yes, simplify.

**Brandara-specific examples:**

- Building the post composer? Build the composer. Do not also
  build a draft autosave system, a version history, and a
  collaboration cursor unless those were asked for.
- Adding a Livewire component? Keep it focused on one responsibility.
  Do not turn a simple form into a multi-step wizard unless asked.
- Writing a Claude API call? One service method, one prompt,
  one response. Do not build a prompt management system around it.
- DaisyUI already provides buttons, modals, and cards. Use them.
  Do not write custom CSS for components that DaisyUI covers.

---

## 3. Surgical Changes

**Touch only what you must. Clean up only your own mess.**

When editing existing Brandara code:

- Do not "improve" adjacent Blade templates, Livewire components,
  or migration files that are not directly related to the task.
- Do not refactor things that are not broken.
- Match existing code style even if you would do it differently.
- If you notice unrelated dead code or a potential bug, mention it
  in a comment — do not delete or fix it without being asked.
- Never touch migration files that have already been run.
  Create a new migration instead.

When your changes create orphans:

- Remove imports, routes, or service bindings that YOUR changes
  made unused.
- Do not remove pre-existing unused code unless asked.

**The test:** Every changed line should trace directly to what
the founder asked for in this session.

**Brandara-specific rules:**

- Never modify the tenancy configuration unless the task is
  specifically about tenancy. It is load-bearing.
- Never change the database schema in a Model without a matching
  migration. Schema and model must always be in sync.
- Never edit .env.example to add real credentials. Only add
  placeholder keys with empty values.
- Never touch the OAuth token encryption logic unless the task
  is specifically about token management.

---

## 4. Goal-Driven Execution

**Define success criteria. Loop until verified.**

Transform every Brandara task into a verifiable goal before starting:

- "Add platform preview" → "The composer shows a live preview panel
  that updates as the user types, reflects the correct character limit
  per platform, and shows a warning if Instagram has no image attached.
  Verify by opening the composer and typing."

- "Build the publish job" → "A scheduled post fires at the correct
  time, calls the LinkedIn API, stores the live post URL on success,
  retries 3 times on failure with 2/5/15 minute delays, and marks the
  post as failed with a plain English reason after the 3rd attempt.
  Verify by scheduling a test post and checking the database."

- "Add Voice DNA" → "A user can paste 10 past posts, click Train,
  and subsequent generated posts noticeably reflect the phrasing
  patterns and sentence structure of the samples. Verify by comparing
  generated output before and after training."

For multi-step tasks, state a brief plan before writing code:

```
1. [Step] → verify: [how to check it worked]
2. [Step] → verify: [how to check it worked]
3. [Step] → verify: [how to check it worked]
```

**Brandara-specific execution rules:**

- After every database migration, verify with:
  `php artisan migrate:status`
- After every new Livewire component, verify it mounts without
  errors by loading the page in the browser.
- After any OAuth integration change, verify by disconnecting
  and reconnecting a platform.
- After any publish job change, verify by dispatching the job
  manually with `php artisan tinker` and checking the result.
- After any change touching tenant data, verify that the same
  data is NOT visible from a second test workspace.

---

## When These Guidelines Are Working

You will know the guidelines are working when:

- Diffs are small and focused — no surprise changes to unrelated files
- Clarifying questions come before implementation, not after mistakes
- No multi-tenancy violations appear during testing
- The founder never has to say "I didn't ask for that"
- Each phase is completed and verified before the next begins

---

*Source: Derived from Andrej Karpathy's observations on LLM coding
pitfalls. Original repository: github.com/multica-ai/andrej-karpathy-skills
Adapted for Brandara — a Laravel multi-tenant SaaS for African B2B founders.*
