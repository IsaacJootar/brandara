<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    | Supported: "claude" (default), "openai"
    | In Module 23 (Admin Panel), this will be overridden by a DB setting
    | so Brandara admins can switch providers from the UI.
    */
    'default' => env('BRANDARA_AI_PROVIDER', 'claude'),

    'claude' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-5'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o'),
    ],

];
