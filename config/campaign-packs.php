<?php

/**
 * Brandara Campaign Pack Library
 *
 * Each pack defines a reusable campaign template.
 * Users activate a pack, fill in their offer details,
 * and AI generates a full post sequence.
 *
 * Keys map to Campaign.pack_key in the database.
 */

return [

    // ── National & cultural occasions ─────────────────────────────────────────

    'independence_day' => [
        'name' => 'Independence Day',
        'category' => 'cultural',
        'duration_days' => 5,
        'default_goal' => 'brand awareness',
        'default_tone' => 'african',
        'description' => '5-post campaign around your country\'s Independence Day. Tie your brand story to national pride and connect with your audience on a deeper level.',
        'key_message_hint' => 'e.g. We are proud to build locally — here is what our business stands for in [your country]',
    ],

    'africa_day' => [
        'name' => 'Africa Day',
        'category' => 'cultural',
        'duration_days' => 5,
        'default_goal' => 'brand awareness',
        'default_tone' => 'african',
        'description' => 'Pan-African celebration on May 25th. Position your brand as part of Africa\'s growth story and connect with the continent.',
        'key_message_hint' => 'e.g. We are building the Africa we want to see — one client at a time',
    ],

    'ramadan' => [
        'name' => 'Ramadan',
        'category' => 'cultural',
        'duration_days' => 7,
        'default_goal' => 'brand awareness',
        'default_tone' => 'friendly',
        'description' => 'Week-long Ramadan campaign. Warm, respectful content that connects your brand to community values and gives back.',
        'key_message_hint' => 'e.g. Ramadan Mubarak — this season we are giving back with [offer or gesture]',
    ],

    'school_admissions' => [
        'name' => 'School Admissions Season',
        'category' => 'cultural',
        'duration_days' => 5,
        'default_goal' => 'leads',
        'default_tone' => 'professional',
        'description' => 'August–September admissions push. Position your business as the solution for parents and students preparing for a new academic term.',
        'key_message_hint' => 'e.g. New term, new goals — here is how we help families prepare for [service relevant to back-to-school]',
    ],

    // ── Seasonal ──────────────────────────────────────────────────────────────

    'christmas_newyear' => [
        'name' => 'Christmas & New Year',
        'category' => 'seasonal',
        'duration_days' => 7,
        'default_goal' => 'brand awareness',
        'default_tone' => 'friendly',
        'description' => '7-day year-end campaign. Build goodwill, celebrate milestones with your audience, and tease what is coming next year.',
        'key_message_hint' => 'e.g. What a year — here is what we achieved together and what we are building in [next year]',
    ],

    'valentine' => [
        'name' => 'Valentine\'s Day',
        'category' => 'seasonal',
        'duration_days' => 5,
        'default_goal' => 'engagement',
        'default_tone' => 'friendly',
        'description' => '5-post campaign around February 14th. Show client love, share success stories, and make a special offer.',
        'key_message_hint' => 'e.g. We love the clients who trust us — here is a special gift for you this Valentine\'s',
    ],

    'black_friday' => [
        'name' => 'Black Friday',
        'category' => 'seasonal',
        'duration_days' => 7,
        'default_goal' => 'sales',
        'default_tone' => 'bold',
        'description' => '7-day urgency campaign. Build anticipation before, drive sales during, celebrate results after.',
        'key_message_hint' => 'e.g. 40% off our [service] — only available for 3 days this November',
    ],

    // ── Business events ───────────────────────────────────────────────────────

    'product_launch' => [
        'name' => 'Product / Service Launch',
        'category' => 'business',
        'duration_days' => 7,
        'default_goal' => 'awareness',
        'default_tone' => 'professional',
        'description' => '7-post launch sequence. Tease, reveal, educate, and close — built to maximise launch day impact.',
        'key_message_hint' => 'e.g. We just launched [product/service] — the fastest way for [audience] to [benefit]',
    ],

    'new_office' => [
        'name' => 'New Branch / Office Opening',
        'category' => 'business',
        'duration_days' => 5,
        'default_goal' => 'awareness',
        'default_tone' => 'professional',
        'description' => '5-post opening campaign. Build excitement, show behind the scenes, and invite your community.',
        'key_message_hint' => 'e.g. We are opening our new [location] branch on [date] — come see us',
    ],

    'flash_sale' => [
        'name' => 'Flash Sale (3 days)',
        'category' => 'business',
        'duration_days' => 3,
        'default_goal' => 'sales',
        'default_tone' => 'bold',
        'description' => 'High-urgency 3-day sale campaign. Announce, remind, last chance — maximum conversion.',
        'key_message_hint' => 'e.g. 3 days only — [X]% off [service]. Doors close Friday midnight.',
    ],

    'client_appreciation' => [
        'name' => 'Client Appreciation Week',
        'category' => 'business',
        'duration_days' => 5,
        'default_goal' => 'retention',
        'default_tone' => 'friendly',
        'description' => '5-post campaign celebrating your clients. Share wins, give shoutouts, make them feel valued.',
        'key_message_hint' => 'e.g. This week we celebrate the clients who trusted us — their wins are our wins',
    ],

    'thought_leadership' => [
        'name' => 'Thought Leadership Push',
        'category' => 'business',
        'duration_days' => 7,
        'default_goal' => 'authority',
        'default_tone' => 'professional',
        'description' => '7-post authority campaign. Share insights, challenge assumptions, build expert positioning.',
        'key_message_hint' => 'e.g. The biggest mistake [audience] make with [topic] — and how to fix it',
    ],

];
