<!DOCTYPE html>
<html lang="en" data-theme="brandara">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Brandara — {{ $title ?? 'Get started' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="min-height:100vh; background:#F1F5F9; display:flex; align-items:center; justify-content:center; padding:1.5rem; font-family:'Figtree',sans-serif;">
    <div style="width:100%; max-width:440px;">

        {{-- Logo --}}
        <div style="display:flex; align-items:center; justify-content:center; gap:0.625rem; margin-bottom:2rem;">
            <img src="{{ asset('images/brandara-icon.svg') }}" style="width:36px; height:36px;" alt="Brandara">
            <span style="font-size:1.375rem; font-weight:700; color:#0F172A; letter-spacing:-0.02em;">Brandara</span>
        </div>

        {{ $slot }}

    </div>
</body>
</html>
