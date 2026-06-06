<!DOCTYPE html>
<html lang="en" data-theme="brandara">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — @yield('title', 'Get started')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="flex items-center justify-center gap-3 mb-8">
            <div class="logo-mark w-10 h-10 flex items-center justify-center">
                <span class="text-white font-bold text-lg">B</span>
            </div>
            <span class="text-2xl font-bold text-neutral">Brandara</span>
        </div>

        {{ $slot }}
    </div>
</body>
</html>
