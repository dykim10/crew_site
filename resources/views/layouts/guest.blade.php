<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PAC-RUN CREW') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16.png') }}">
        <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('images/favicon-192.webp') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800;900&family=Noto+Sans+KR:wght@400;500;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-body antialiased bg-pac-black-900 text-pac-black-900">

        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">

            <!-- 로고 -->
            <a href="/" class="mb-8 block">
                <img src="{{ asset('images/logo.webp') }}"
                     alt="PAC RUN CREW"
                     class="h-12 w-auto"
                     loading="eager">
            </a>

            <!-- 폼 카드 -->
            <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
                {{ $slot }}
            </div>

        </div>

    </body>
</html>
