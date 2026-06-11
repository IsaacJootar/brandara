<?php

return [

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    // ── OpenAI (ChatGPT) ─────────────────────────────────────────────────────
    'openai' => [
        'key' => env('OPENAI_API_KEY'),
    ],

    // ── Google Gemini ─────────────────────────────────────────────────────────
    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
    ],

    // ── Anthropic Claude ──────────────────────────────────────────────────────
    'anthropic' => [
        'key' => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-5'),
    ],

    // ── Africa's Talking (SMS) ────────────────────────────────────────────────
    'africastalking' => [
        'username' => env('AT_USERNAME', 'sandbox'),
        'api_key' => env('AT_API_KEY'),
        'sender_id' => env('AT_SENDER_ID', 'Brandara'),
    ],

    // ── LinkedIn OAuth 2.0 ────────────────────────────────────────────────────
    // Get from: developer.linkedin.com → Your App → Auth
    // Redirect URI to register: APP_URL/oauth/callback/linkedin
    'linkedin' => [
        'client_id' => env('LINKEDIN_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
        'redirect_uri' => env('APP_URL').'/oauth/callback/linkedin',
        'scopes' => ['openid', 'profile', 'email', 'w_member_social'],
    ],

    // ── X (Twitter) OAuth 2.0 PKCE ───────────────────────────────────────────
    // Get from: developer.twitter.com → Your App → Keys and Tokens
    // Redirect URI to register: APP_URL/oauth/callback/twitter
    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect_uri' => env('APP_URL').'/oauth/callback/twitter',
        'scopes' => ['tweet.read', 'tweet.write', 'users.read', 'offline.access'],
    ],

    // ── Meta (Facebook + Instagram + Threads) ────────────────────────────────
    // Get from: developers.facebook.com → Your App → Settings → Basic
    // One app covers Facebook, Instagram, and Threads
    // Redirect URI to register: APP_URL/oauth/callback/facebook
    //                           APP_URL/oauth/callback/instagram
    //                           APP_URL/oauth/callback/threads
    'meta' => [
        'app_id' => env('META_APP_ID'),
        'app_secret' => env('META_APP_SECRET'),
        'redirect_uri' => env('APP_URL').'/oauth/callback/{platform}', // replaced per request
        'fb_scopes' => ['pages_manage_posts', 'pages_read_engagement', 'pages_show_list'],
        'ig_scopes' => ['instagram_basic', 'instagram_content_publish', 'instagram_manage_insights'],
        'threads_scopes' => ['threads_basic', 'threads_content_publish'],
    ],

    // ── Paystack ─────────────────────────────────────────────────────────────
    // Get from: dashboard.paystack.com → Settings → API Keys
    'paystack' => [
        'public_key' => env('PAYSTACK_PUBLIC_KEY'),
        'secret_key' => env('PAYSTACK_SECRET_KEY'),  // Also used as webhook HMAC key
        'url' => env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co'),
    ],

    // ── Flutterwave ───────────────────────────────────────────────────────────
    // Get from: app.flutterwave.com → Settings → API
    'flutterwave' => [
        'public_key' => env('FLW_PUBLIC_KEY'),
        'secret_key' => env('FLW_SECRET_KEY'),
        'webhook_secret' => env('FLW_WEBHOOK_SECRET', env('FLW_SECRET_HASH')), // verif-hash header
    ],

    // ── Canva ─────────────────────────────────────────────────────────────────
    'canva' => [
        'client_id' => env('CANVA_CLIENT_ID'),
        'client_secret' => env('CANVA_CLIENT_SECRET'),
    ],

    'publishing' => [
        // When true, PublisherFactory resolves real platform publishers.
        // When false (default for dev), all platforms use FakePublisher.
        'live' => env('PUBLISHING_LIVE', false),
    ],

];
