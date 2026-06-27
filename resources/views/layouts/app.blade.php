<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="v1">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'PAC-RUN CREW') }}</title>

        {{-- Favicon --}}
        <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16.png') }}">
        <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('images/favicon-192.webp') }}">

        {{-- PAC-RUN 공용 폰트 --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow+Condensed:wght@400;600;700;800;900&family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- V1 Dark Editorial CSS 변수 --}}
        <link rel="stylesheet" href="{{ asset('css/skin/_skin_v1.css') }}">

        {{-- V1 Dark Editorial 테마 (서브페이지 고정) --}}
        <style>
        :root {
            --v1-bg:      #0D0D0D;
            --v1-surface: #141414;
            --v1-card:    #1C1C1C;
            --v1-border:  rgba(255,255,255,0.07);
            --v1-border2: rgba(255,255,255,0.12);
            --v1-yellow:  #E5AD16;
            --v1-pink:    #E80043;
            --v1-text:    #FFFFFF;
            --v1-muted:   rgba(255,255,255,0.45);
        }

        html, body { background-color: var(--v1-bg) !important; color: var(--v1-text) !important; }

        .rounded-2xl, .rounded-xl, .rounded-lg, .rounded-md { border-radius: 2px !important; }
        .rounded     { border-radius: 2px !important; }
        .rounded-sm  { border-radius: 1px !important; }
        .rounded-full { border-radius: 9999px !important; }

        .shadow-sm, .shadow, .shadow-md, .shadow-lg { box-shadow: none !important; }

        .bg-pac-black-50   { background-color: var(--v1-bg)      !important; }
        .bg-white          { background-color: var(--v1-card)    !important; }
        .bg-gray-50        { background-color: var(--v1-surface) !important; }
        .bg-pac-black-900  { background-color: var(--v1-card)    !important; }
        .bg-pac-black-800  { background-color: #222222           !important; }
        .bg-pac-black-700  { background-color: #2A2A2A           !important; }
        .bg-pac-black-600  { background-color: #333333           !important; }
        .bg-pac-black-100  { background-color: #252525           !important; }
        .hover\:bg-pac-black-800:hover { background-color: #252525 !important; }
        .hover\:bg-pac-black-50:hover  { background-color: rgba(255,255,255,0.03) !important; }

        .bg-green-50        { background-color: rgba(16,185,129,0.08)  !important; }
        .bg-red-50          { background-color: rgba(232,0,67,0.08)    !important; }
        .bg-pac-yellow-50   { background-color: rgba(229,173,22,0.06)  !important; }
        .bg-pac-yellow-100  { background-color: rgba(229,173,22,0.10)  !important; }
        .bg-pac-yellow-200  { background-color: rgba(229,173,22,0.15)  !important; }
        .bg-pac-black-800\/30 { background-color: rgba(34,34,34,0.3)  !important; }

        .text-pac-black-900 { color: var(--v1-text)              !important; }
        .text-pac-black-800 { color: rgba(255,255,255,0.92)      !important; }
        .text-pac-black-700 { color: rgba(255,255,255,0.78)      !important; }
        .text-pac-black-600 { color: rgba(255,255,255,0.60)      !important; }
        .text-pac-black-500 { color: rgba(255,255,255,0.45)      !important; }
        .text-pac-black-400 { color: rgba(255,255,255,0.32)      !important; }
        .text-pac-black-300 { color: rgba(255,255,255,0.22)      !important; }
        .text-pac-black-200 { color: rgba(255,255,255,0.88)      !important; }
        .text-gray-900      { color: var(--v1-text)              !important; }
        .text-gray-700      { color: rgba(255,255,255,0.78)      !important; }
        .text-gray-600      { color: rgba(255,255,255,0.60)      !important; }
        .text-gray-500      { color: rgba(255,255,255,0.45)      !important; }
        .text-green-800     { color: #4ade80                     !important; }
        .text-green-700     { color: #34d399                     !important; }
        .text-pac-yellow-700 { color: var(--v1-yellow)          !important; }
        .text-pac-yellow-600 { color: var(--v1-yellow)          !important; }
        .text-pac-black-50   { color: rgba(255,255,255,0.08)    !important; }
        .hover\:text-pac-yellow-600:hover { color: var(--v1-yellow) !important; }
        .hover\:text-pac-yellow-300:hover { color: #f0c84a      !important; }
        .group:hover .group-hover\:text-white { color: #fff     !important; }

        .border-pac-black-100   { border-color: var(--v1-border)           !important; }
        .border-pac-black-200   { border-color: var(--v1-border2)          !important; }
        .border-pac-black-50    { border-color: var(--v1-border)           !important; }
        .border-green-200       { border-color: rgba(74,222,128,0.25)      !important; }
        .border-red-200         { border-color: rgba(232,0,67,0.25)        !important; }
        .border-pac-yellow-200  { border-color: rgba(229,173,22,0.30)      !important; }
        .border-pac-yellow-300  { border-color: rgba(229,173,22,0.45)      !important; }
        .divide-pac-black-100 > * + * { border-color: var(--v1-border)     !important; }
        .divide-pac-black-50  > * + * { border-color: var(--v1-border)     !important; }

        input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):not([type="file"]),
        select, textarea {
            background-color: #1E1E1E  !important;
            border-color: var(--v1-border2) !important;
            color: var(--v1-text)          !important;
        }
        input::placeholder, textarea::placeholder { color: rgba(255,255,255,0.25) !important; }

        .hover\:bg-white\/5:hover         { background-color: rgba(255,255,255,0.04)  !important; }
        .hover\:bg-white\/\[0\.02\]:hover { background-color: rgba(255,255,255,0.025) !important; }
        .hover\:shadow-md:hover { box-shadow: none !important; }
        .hover\:-translate-y-1:hover { transform: translateY(-4px) !important; }

        .prose         { color: rgba(255,255,255,0.8)  !important; }
        .prose h1, .prose h2, .prose h3 { color: var(--v1-text) !important; }
        .prose a       { color: var(--v1-yellow)       !important; }
        .prose strong  { color: var(--v1-text)         !important; }
        .prose hr      { border-color: var(--v1-border) !important; }
        </style>
    </head>

    <body class="_skin_v1 font-body antialiased bg-pac-black-50 text-pac-black-900 min-h-screen overflow-x-hidden">

        @include('layouts.navigation')

        @isset($header)
            <div class="bg-white border-b border-pac-black-100">
                <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-4">
                    {{ $header }}
                </div>
            </div>
        @endisset

        {{-- 플래시 메시지 (전역) --}}
        @if(session('error'))
            <div x-data="{ show: true }"
                 x-show="show"
                 x-init="setTimeout(() => show = false, 4000)"
                 x-transition:leave="transition-opacity duration-300"
                 x-transition:leave-end="opacity-0"
                 class="fixed top-20 left-1/2 -translate-x-1/2 z-50 w-full max-w-sm px-4">
                <div class="flex items-center gap-3 bg-pac-pink-500 text-white text-sm font-medium px-5 py-3 shadow-lg">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <span class="flex-1 font-body">{{ session('error') }}</span>
                    <button @click="show = false" class="text-white/70 hover:text-white ml-auto">✕</button>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div x-data="{ show: true }"
                 x-show="show"
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:leave="transition-opacity duration-300"
                 x-transition:leave-end="opacity-0"
                 class="fixed top-20 left-1/2 -translate-x-1/2 z-50 w-full max-w-sm px-4">
                <div class="flex items-center gap-3 bg-pac-green-500 text-white text-sm font-medium px-5 py-3 shadow-lg">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="flex-1 font-body">{{ session('success') }}</span>
                    <button @click="show = false" class="text-white/70 hover:text-white ml-auto">✕</button>
                </div>
            </div>
        @endif

        <main>
            {{ $slot }}
        </main>

    </body>
</html>
