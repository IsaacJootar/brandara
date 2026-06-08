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

    // ── African holidays ───────────────────────────────────────────────────────

    'ng_independence' => [
        'name' => 'Nigeria Independence Day',
        'emoji' => '🇳🇬',
        'category' => 'african_holiday',
        'countries' => ['NG'],
        'typical_month' => 10,
        'typical_day' => 1,
        'duration_days' => 5,
        'default_goal' => 'brand awareness',
        'default_tone' => 'african',
        'description' => 'Build patriotic brand connection around October 1st. 5-post campaign that ties your brand story to Nigerian pride.',
        'key_message_hint' => 'e.g. As Nigerians, we build with pride — here is what our business stands for',
    ],

    'gh_independence' => [
        'name' => 'Ghana Independence Day',
        'emoji' => '🇬🇭',
        'category' => 'african_holiday',
        'countries' => ['GH'],
        'typical_month' => 3,
        'typical_day' => 6,
        'duration_days' => 5,
        'default_goal' => 'brand awareness',
        'default_tone' => 'african',
        'description' => '5-post campaign around March 6th celebrating Ghanaian independence and your brand\'s roots.',
        'key_message_hint' => 'e.g. 67 years of independence — and Ghanaian businesses are just getting started',
    ],

    'africa_day' => [
        'name' => 'Africa Day',
        'emoji' => '🌍',
        'category' => 'african_holiday',
        'countries' => [],
        'typical_month' => 5,
        'typical_day' => 25,
        'duration_days' => 5,
        'default_goal' => 'brand awareness',
        'default_tone' => 'african',
        'description' => 'Pan-African celebration on May 25th. Position your brand as part of Africa\'s growth story.',
        'key_message_hint' => 'e.g. We are building the Africa we want to see — one client at a time',
    ],

    'ramadan' => [
        'name' => 'Ramadan Campaign',
        'emoji' => '🌙',
        'category' => 'african_holiday',
        'countries' => ['NG', 'GH', 'SN', 'CI', 'CM'],
        'typical_month' => null,
        'typical_day' => null,
        'duration_days' => 7,
        'default_goal' => 'brand awareness',
        'default_tone' => 'friendly',
        'description' => 'Week-long Ramadan campaign. Warm, respectful content that connects your brand to community values.',
        'key_message_hint' => 'e.g. Ramadan Mubarak — this season we are giving back with [offer or gesture]',
    ],

    'christmas_newyear' => [
        'name' => 'Christmas & New Year',
        'emoji' => '🎄',
        'category' => 'seasonal',
        'countries' => [],
        'typical_month' => 12,
        'typical_day' => 20,
        'duration_days' => 7,
        'default_goal' => 'brand awareness',
        'default_tone' => 'friendly',
        'description' => '7-day year-end campaign. Build goodwill, celebrate milestones, and tease what\'s coming next year.',
        'key_message_hint' => 'e.g. What a year — here is what we achieved together and what we are building in [year]',
    ],

    'valentine' => [
        'name' => 'Valentine\'s Day',
        'emoji' => '💝',
        'category' => 'seasonal',
        'countries' => [],
        'typical_month' => 2,
        'typical_day' => 10,
        'duration_days' => 5,
        'default_goal' => 'engagement',
        'default_tone' => 'friendly',
        'description' => '5-post campaign around February 14th. Show client love, share success stories, make a special offer.',
        'key_message_hint' => 'e.g. We love the clients who trust us — here is a special gift for you this Valentine\'s',
    ],

    'black_friday' => [
        'name' => 'Black Friday / Cyber Monday',
        'emoji' => '🛍️',
        'category' => 'seasonal',
        'countries' => [],
        'typical_month' => 11,
        'typical_day' => 25,
        'duration_days' => 7,
        'default_goal' => 'sales',
        'default_tone' => 'bold',
        'description' => '7-day urgency campaign. Build anticipation before, drive sales during, celebrate after.',
        'key_message_hint' => 'e.g. 40% off our [service] — only for the last 3 days of November',
    ],

    'school_admissions' => [
        'name' => 'School Admissions Season',
        'emoji' => '📚',
        'category' => 'african_holiday',
        'countries' => ['NG', 'GH', 'KE', 'ZA'],
        'typical_month' => 8,
        'typical_day' => null,
        'duration_days' => 5,
        'default_goal' => 'leads',
        'default_tone' => 'professional',
        'description' => 'August–September admissions push. Position your business as the solution for parents and students.',
        'key_message_hint' => 'e.g. New term, new goals — here is how we help families prepare financially for school fees',
    ],

    // ── Generic business events ────────────────────────────────────────────────

    'product_launch' => [
        'name' => 'Product / Service Launch',
        'emoji' => '🚀',
        'category' => 'business',
        'countries' => [],
        'typical_month' => null,
        'typical_day' => null,
        'duration_days' => 7,
        'default_goal' => 'awareness',
        'default_tone' => 'professional',
        'description' => '7-post launch sequence. Tease, reveal, educate, and close — built to maximise launch day impact.',
        'key_message_hint' => 'e.g. We just launched [product/service] — the fastest way for [audience] to [benefit]',
    ],

    'new_office' => [
        'name' => 'New Branch / Office Opening',
        'emoji' => '🏢',
        'category' => 'business',
        'countries' => [],
        'typical_month' => null,
        'typical_day' => null,
        'duration_days' => 5,
        'default_goal' => 'awareness',
        'default_tone' => 'professional',
        'description' => '5-post opening campaign. Build excitement, show behind the scenes, and invite your audience.',
        'key_message_hint' => 'e.g. We are opening our new [location] branch on [date] — come see us',
    ],

    'flash_sale' => [
        'name' => 'Flash Sale (3 days)',
        'emoji' => '⚡',
        'category' => 'business',
        'countries' => [],
        'typical_month' => null,
        'typical_day' => null,
        'duration_days' => 3,
        'default_goal' => 'sales',
        'default_tone' => 'bold',
        'description' => 'High-urgency 3-day sale campaign. Announce, remind, last chance — maximum conversion.',
        'key_message_hint' => 'e.g. 3 days only — [X]% off [service]. Doors close Friday midnight.',
    ],

    'client_appreciation' => [
        'name' => 'Client Appreciation Week',
        'emoji' => '🙏',
        'category' => 'business',
        'countries' => [],
        'typical_month' => null,
        'typical_day' => null,
        'duration_days' => 5,
        'default_goal' => 'retention',
        'default_tone' => 'friendly',
        'description' => '5-post campaign celebrating your clients. Share wins, give shoutouts, make them feel valued.',
        'key_message_hint' => 'e.g. This week we celebrate the clients who trusted us — their wins are our wins',
    ],

    'thought_leadership' => [
        'name' => 'Thought Leadership Push',
        'emoji' => '🧠',
        'category' => 'business',
        'countries' => [],
        'typical_month' => null,
        'typical_day' => null,
        'duration_days' => 7,
        'default_goal' => 'authority',
        'default_tone' => 'professional',
        'description' => '7-post authority campaign. Share insights, challenge assumptions, build expert positioning.',
        'key_message_hint' => 'e.g. The biggest mistake [audience] make with [topic] — and how to fix it',
    ],

];
