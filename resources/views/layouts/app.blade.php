<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $activeTheme ?? 'v1' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PAC-RUN CREW') }}</title>

        <!-- Fonts: 기본 -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800;900&family=Noto+Sans+KR:wght@400;500;700&display=swap" rel="stylesheet">

        @if(($activeTheme ?? 'v1') === 'v1')
        <!-- V1: Bebas Neue -->
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
        @endif

        @if(($activeTheme ?? 'v1') === 'v2')
        <!-- V2: Anton -->
        <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
        @endif

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- ===== THEME CSS ===== -->
        @if(($activeTheme ?? 'v1') === 'v1')
        <style>
        /* ─────────────────────────────────────
           V1 THEME — Dark Editorial
           검정 배경 / pac-yellow 강조 / Bebas Neue
        ───────────────────────────────────── */
        [data-theme="v1"] { background-color:#111111; color:#FFFFFF; }

        /* 페이지 배경 */
        [data-theme="v1"] body,
        [data-theme="v1"].bg-pac-black-50 { background-color:#111111 !important; }

        /* ─ 카드/서피스 ─ */
        [data-theme="v1"] .bg-white                { background-color:#1C1C1C !important; }
        [data-theme="v1"] .bg-gray-50              { background-color:#161616 !important; }
        [data-theme="v1"] .bg-pac-black-50         { background-color:#111111 !important; }
        [data-theme="v1"] .bg-pac-black-900        { background-color:#242424 !important; }
        [data-theme="v1"] .bg-pac-black-800,
        [data-theme="v1"] .hover\:bg-pac-black-800:hover { background-color:#2C2C2C !important; }
        [data-theme="v1"] .bg-pac-black-700        { background-color:#333333 !important; }

        /* 컬러 배경 대체 (알림/상태) */
        [data-theme="v1"] .bg-green-50             { background-color:#0a2015 !important; }
        [data-theme="v1"] .bg-red-50               { background-color:#220010 !important; }
        [data-theme="v1"] .bg-pac-yellow-50        { background-color:#1a1200 !important; }
        [data-theme="v1"] .bg-pac-yellow-100       { background-color:#2a1e00 !important; }
        [data-theme="v1"] .bg-blue-50              { background-color:#001020 !important; }

        /* ─ 텍스트 ─ */
        [data-theme="v1"] .text-pac-black-900      { color:#FFFFFF !important; }
        [data-theme="v1"] .text-pac-black-800      { color:rgba(255,255,255,.9) !important; }
        [data-theme="v1"] .text-pac-black-700      { color:rgba(255,255,255,.8) !important; }
        [data-theme="v1"] .text-pac-black-600      { color:rgba(255,255,255,.65) !important; }
        [data-theme="v1"] .text-pac-black-500      { color:rgba(255,255,255,.5) !important; }
        [data-theme="v1"] .text-pac-black-400      { color:rgba(255,255,255,.38) !important; }
        [data-theme="v1"] .text-pac-black-300      { color:rgba(255,255,255,.28) !important; }
        [data-theme="v1"] .text-gray-900           { color:#FFFFFF !important; }
        [data-theme="v1"] .text-gray-700           { color:rgba(255,255,255,.8) !important; }
        [data-theme="v1"] .text-gray-600           { color:rgba(255,255,255,.6) !important; }
        [data-theme="v1"] .text-gray-500           { color:rgba(255,255,255,.45) !important; }
        [data-theme="v1"] .text-green-800          { color:#4ade80 !important; }
        [data-theme="v1"] .text-pac-yellow-700     { color:#E5AD16 !important; }

        /* ─ 테두리 ─ */
        [data-theme="v1"] .border-pac-black-100    { border-color:rgba(255,255,255,.07) !important; }
        [data-theme="v1"] .border-pac-black-200    { border-color:rgba(255,255,255,.11) !important; }
        [data-theme="v1"] .border-pac-black-300    { border-color:rgba(255,255,255,.18) !important; }
        [data-theme="v1"] .border-green-200        { border-color:rgba(74,222,128,.25) !important; }
        [data-theme="v1"] .border-red-200          { border-color:rgba(252,165,165,.2) !important; }
        [data-theme="v1"] .border-pac-yellow-200   { border-color:rgba(229,173,22,.3) !important; }
        [data-theme="v1"] .divide-pac-black-100 > * + * { border-color:rgba(255,255,255,.07) !important; }

        /* ─ 폼 인풋 ─ */
        [data-theme="v1"] input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]),
        [data-theme="v1"] select,
        [data-theme="v1"] textarea {
            background-color:#2C2C2C !important;
            border-color:rgba(255,255,255,.15) !important;
            color:#FFFFFF !important;
        }
        [data-theme="v1"] input::placeholder,
        [data-theme="v1"] textarea::placeholder { color:rgba(255,255,255,.3) !important; }

        /* ─ 링크/호버 ─ */
        [data-theme="v1"] .hover\:bg-white\/5:hover { background-color:rgba(255,255,255,.05) !important; }
        [data-theme="v1"] .hover\:bg-pac-black-800:hover { background-color:#2C2C2C !important; }

        /* ─ 디스플레이 폰트 → Bebas Neue ─ */
        [data-theme="v1"] .font-display,
        [data-theme="v1"] h1, [data-theme="v1"] h2, [data-theme="v1"] h3 {
            font-family:'Bebas Neue', 'Barlow Condensed', sans-serif !important;
            letter-spacing:0.05em;
        }

        /* ─ 어드민 스위처 바 (V1 스타일) ─ */
        .theme-admin-bar {
            background:#1A1212;
            border-bottom:1px solid rgba(229,173,22,.4);
        }
        .theme-btn-v1 {
            background:#E5AD16; color:#1A1212;
        }
        .theme-btn-v2 {
            background:transparent; color:rgba(255,255,255,.5);
            border:1px solid rgba(255,255,255,.2);
        }
        .theme-btn-v2:hover { color:rgba(255,255,255,.8); border-color:rgba(255,255,255,.4); }
        </style>
        @endif

        @if(($activeTheme ?? 'v1') === 'v2')
        <style>
        /* ─────────────────────────────────────
           V2 THEME — Energy Burst
           크림 배경 / Anton 폰트 / 역동적
        ───────────────────────────────────── */
        [data-theme="v2"] { background-color:#F5F3EE; color:#1A1212; }
        [data-theme="v2"] .bg-pac-black-50 { background-color:#F5F3EE !important; }
        [data-theme="v2"] .bg-white { background-color:#FFFFFF !important; }

        /* 디스플레이 폰트 → Anton */
        [data-theme="v2"] .font-display,
        [data-theme="v2"] h1, [data-theme="v2"] h2, [data-theme="v2"] h3 {
            font-family:'Anton', 'Barlow Condensed', sans-serif !important;
            letter-spacing:0.02em;
        }

        /* 카드 세퍼레이터 살짝 따뜻하게 */
        [data-theme="v2"] .border-pac-black-100 { border-color:rgba(26,18,18,.1) !important; }
        [data-theme="v2"] .divide-pac-black-100 > * + * { border-color:rgba(26,18,18,.08) !important; }

        /* ─ 어드민 스위처 바 (V2 스타일) ─ */
        .theme-admin-bar {
            background:#E5AD16;
            border-bottom:2px solid #1A1212;
        }
        .theme-btn-v1 {
            background:transparent; color:rgba(26,18,18,.6);
            border:2px solid rgba(26,18,18,.3);
        }
        .theme-btn-v1:hover { background:rgba(26,18,18,.1); color:#1A1212; }
        .theme-btn-v2 {
            background:#1A1212; color:#E5AD16;
            border:2px solid #1A1212;
        }
        </style>
        @endif
    </head>
    <body class="font-body antialiased bg-pac-black-50 text-pac-black-900">

        {{-- ===== 어드민 전용 테마 스위처 바 ===== --}}
        @auth
        @if(in_array(auth()->user()->role, ['super_admin', 'region_admin']))
        <div class="theme-admin-bar" style="position:sticky;top:0;z-index:9999;display:flex;align-items:center;justify-content:space-between;padding:0 24px;height:36px;">
            <span style="font-size:10px;font-weight:700;letter-spacing:3px;text-transform:uppercase;opacity:.7;">
                🎨 THEME SWITCHER
            </span>
            <div style="display:flex;align-items:center;gap:8px;">
                <form method="POST" action="{{ route('theme.switch') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="theme" value="v1">
                    <button type="submit" class="theme-btn-v1" style="padding:3px 14px;font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;cursor:pointer;">
                        V1 Dark
                    </button>
                </form>
                <form method="POST" action="{{ route('theme.switch') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="theme" value="v2">
                    <button type="submit" class="theme-btn-v2" style="padding:3px 14px;font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;cursor:pointer;">
                        V2 Energy
                    </button>
                </form>
                <a href="/admin/theme-settings" style="font-size:9px;font-weight:700;letter-spacing:1px;opacity:.6;text-decoration:none;margin-left:8px;">
                    설정 →
                </a>
            </div>
        </div>
        @endif
        @endauth

        @include('layouts.navigation')

        @isset($header)
            <div class="bg-white border-b border-pac-black-100">
                <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-4">
                    {{ $header }}
                </div>
            </div>
        @endisset

        @if (session('error'))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 4000)"
                class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-full max-w-md px-4"
            >
                <div class="flex items-center gap-3 bg-red-600 text-white text-sm font-medium px-5 py-3 rounded-lg shadow-lg">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="ml-auto text-white/70 hover:text-white">✕</button>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 3000)"
                class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-full max-w-md px-4"
            >
                <div class="flex items-center gap-3 bg-green-600 text-white text-sm font-medium px-5 py-3 rounded-lg shadow-lg">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="ml-auto text-white/70 hover:text-white">✕</button>
                </div>
            </div>
        @endif

        <main>
            {{ $slot }}
        </main>

    </body>
</html>
