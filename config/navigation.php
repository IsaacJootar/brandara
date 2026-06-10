<?php

/*
|--------------------------------------------------------------------------
| Brandara — Navigation & Module Tier Configuration
|--------------------------------------------------------------------------
|
| This file defines which nav modules are available on each subscription
| tier. It is the SINGLE source of truth for sidebar rendering.
|
| When the Admin Module is built (Phase 23), this will be replaced by a
| database-driven system editable from the admin panel. For now, editing
| this file is the only way to change tier access — no Blade files contain
| tier logic.
|
| Structure per item:
|   route  — Laravel named route
|   label  — text shown in sidebar
|   icon   — SVG path string (heroicons outline style)
|   tiers  — which plans can access this module
|             ['starter','pro','agency'] = all tiers
|             ['pro','agency']           = starter cannot access
|             ['agency']                 = agency only
|
| Tiers (in order): starter → pro → agency
|
*/

return [

    'sections' => [

        [
            'label' => null, // no section heading
            'items' => [
                [
                    'route' => 'dashboard',
                    'label' => 'Dashboard',
                    'tiers' => ['starter', 'pro', 'agency'],
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
                ],
            ],
        ],

        [
            'label' => 'Content',
            'items' => [
                [
                    'route' => 'create',
                    'label' => 'Create',
                    'tiers' => ['starter', 'pro', 'agency'],
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
                ],
                [
                    'route' => 'plan',
                    'label' => 'Plan',
                    'tiers' => ['starter', 'pro', 'agency'],
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                ],
                [
                    'route' => 'schedule',
                    'label' => 'Schedule',
                    'tiers' => ['starter', 'pro', 'agency'],
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                ],
                [
                    'route' => 'media',
                    'label' => 'Media Library',
                    'tiers' => ['starter', 'pro', 'agency'],
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                ],
            ],
        ],

        [
            'label' => 'Growth',
            'items' => [
                [
                    'route' => 'grow',
                    'label' => 'Grow',
                    'tiers' => ['starter', 'pro', 'agency'],
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>',
                ],
                [
                    'route' => 'results',
                    'label' => 'Results',
                    'tiers' => ['starter', 'pro', 'agency'], // All tiers get analytics
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
                ],
                [
                    'route' => 'trends',
                    'label' => 'Trends',
                    'tiers' => ['pro', 'agency'],
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>',
                ],
                [
                    'route' => 'ai-presence',
                    'label' => 'AI Visibility',
                    'tiers' => ['pro', 'agency'],
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>',
                ],
            ],
        ],

        [
            'label' => 'Brand',
            'items' => [
                [
                    'route' => 'my-brand',
                    'label' => 'My Brand',
                    'tiers' => ['starter', 'pro', 'agency'],
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
                ],
                [
                    'route' => 'connections',
                    'label' => 'Connections',
                    'tiers' => ['starter', 'pro', 'agency'],
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>',
                ],
                [
                    'route' => 'settings',
                    'label' => 'Settings',
                    'tiers' => ['starter', 'pro', 'agency'],
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
                ],
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Tier Definitions
    |--------------------------------------------------------------------------
    | Used for display labels, upgrade prompts, and billing routing.
    | Do not change the keys — they match the workspaces.plan enum values.
    */
    'tiers' => [
        'starter' => [
            'label'       => 'Basic',
            'color'       => '#64748B',
            'bg'          => '#F1F5F9',
            'description' => 'Content generation, Brand Voice, 3 platforms, 30 generations/month',
            'price_usd'   => 19,
        ],
        'pro' => [
            'label'       => 'Growth',
            'color'       => '#7C3AED',
            'bg'          => '#F5F3FF',
            'description' => 'Everything in Basic + all 7 platforms, AI Visibility, lead tracker, 3 brands',
            'price_usd'   => 39,
        ],
        'agency' => [
            'label'       => 'Agency',
            'color'       => '#F59E0B',
            'bg'          => '#FFFBEB',
            'description' => 'Everything in Growth + unlimited brands + client workspaces + approvals',
            'price_usd'   => 89,
        ],
    ],

];
