<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $activeTheme ?? 'v1' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'PAC-RUN CREW') }}</title>

        <!-- Base Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800;900&family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet">

        @if(($activeTheme ?? 'v1') === 'v1')
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
        @else
        <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
        @endif

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- ═══════════════════════════════════════════════════════════
             V1 THEME — DARK EDITORIAL
             메인 페이지와 완전 동일한 시각 언어
             #0D0D0D 배경 / Bebas Neue / pac-yellow / 샤프 엣지
        ═══════════════════════════════════════════════════════════ --}}
        @if(($activeTheme ?? 'v1') === 'v1')
        <style>
        /* ── 전역 리셋 ── */
        :root {
            --v1-bg:      #0D0D0D;
            --v1-surface: #141414;
            --v1-card:    #1C1C1C;
            --v1-nav:     #121212;
            --v1-border:  rgba(255,255,255,0.07);
            --v1-border2: rgba(255,255,255,0.12);
            --v1-yellow:  #E5AD16;
            --v1-pink:    #E80043;
            --v1-text:    #FFFFFF;
            --v1-muted:   rgba(255,255,255,0.45);
            --v1-faint:   rgba(255,255,255,0.18);
        }

        html[data-theme="v1"],
        html[data-theme="v1"] body {
            background-color: var(--v1-bg) !important;
            color: var(--v1-text) !important;
        }

        /* ── 모든 rounded 제거 → 메인 페이지와 동일한 샤프 엣지 ── */
        html[data-theme="v1"] .rounded-2xl,
        html[data-theme="v1"] .rounded-xl,
        html[data-theme="v1"] .rounded-lg,
        html[data-theme="v1"] .rounded-md { border-radius: 2px !important; }
        html[data-theme="v1"] .rounded { border-radius: 2px !important; }
        html[data-theme="v1"] .rounded-sm { border-radius: 1px !important; }
        /* 원형 유지 (아바타, 도트 등) */
        html[data-theme="v1"] .rounded-full { border-radius: 9999px !important; }

        /* ── 섀도 제거 ── */
        html[data-theme="v1"] .shadow-sm,
        html[data-theme="v1"] .shadow,
        html[data-theme="v1"] .shadow-md,
        html[data-theme="v1"] .shadow-lg { box-shadow: none !important; }

        /* ── 배경 ── */
        html[data-theme="v1"] .bg-pac-black-50   { background-color: var(--v1-bg)      !important; }
        html[data-theme="v1"] .bg-white          { background-color: var(--v1-card)    !important; }
        html[data-theme="v1"] .bg-gray-50        { background-color: var(--v1-surface) !important; }
        html[data-theme="v1"] .bg-pac-black-900  { background-color: var(--v1-card)    !important; }
        html[data-theme="v1"] .bg-pac-black-800  { background-color: #222222           !important; }
        html[data-theme="v1"] .bg-pac-black-700  { background-color: #2A2A2A           !important; }
        html[data-theme="v1"] .bg-pac-black-600  { background-color: #333333           !important; }
        html[data-theme="v1"] .bg-pac-black-100  { background-color: #252525           !important; }
        html[data-theme="v1"] .hover\:bg-pac-black-800:hover { background-color: #252525 !important; }
        html[data-theme="v1"] .hover\:bg-pac-black-50:hover  { background-color: rgba(255,255,255,0.03) !important; }

        /* 상태별 컬러 배경 */
        html[data-theme="v1"] .bg-green-50        { background-color: rgba(16,185,129,0.08) !important; }
        html[data-theme="v1"] .bg-red-50          { background-color: rgba(232,0,67,0.08)   !important; }
        html[data-theme="v1"] .bg-pac-yellow-50   { background-color: rgba(229,173,22,0.06) !important; }
        html[data-theme="v1"] .bg-pac-yellow-100  { background-color: rgba(229,173,22,0.10) !important; }
        html[data-theme="v1"] .bg-pac-yellow-200  { background-color: rgba(229,173,22,0.15) !important; }
        html[data-theme="v1"] .bg-pac-black-800\/30 { background-color: rgba(34,34,34,0.3) !important; }

        /* ── 텍스트 ── */
        html[data-theme="v1"] .text-pac-black-900 { color: var(--v1-text)  !important; }
        html[data-theme="v1"] .text-pac-black-800 { color: rgba(255,255,255,0.92) !important; }
        html[data-theme="v1"] .text-pac-black-700 { color: rgba(255,255,255,0.78) !important; }
        html[data-theme="v1"] .text-pac-black-600 { color: rgba(255,255,255,0.60) !important; }
        html[data-theme="v1"] .text-pac-black-500 { color: rgba(255,255,255,0.45) !important; }
        html[data-theme="v1"] .text-pac-black-400 { color: rgba(255,255,255,0.32) !important; }
        html[data-theme="v1"] .text-pac-black-300 { color: rgba(255,255,255,0.22) !important; }
        html[data-theme="v1"] .text-pac-black-200 { color: rgba(255,255,255,0.88) !important; }
        html[data-theme="v1"] .text-gray-900      { color: var(--v1-text)  !important; }
        html[data-theme="v1"] .text-gray-700      { color: rgba(255,255,255,0.78) !important; }
        html[data-theme="v1"] .text-gray-600      { color: rgba(255,255,255,0.60) !important; }
        html[data-theme="v1"] .text-gray-500      { color: rgba(255,255,255,0.45) !important; }
        html[data-theme="v1"] .text-green-800     { color: #4ade80 !important; }
        html[data-theme="v1"] .text-green-700     { color: #34d399 !important; }
        html[data-theme="v1"] .text-pac-yellow-700 { color: var(--v1-yellow) !important; }
        html[data-theme="v1"] .text-pac-yellow-600 { color: var(--v1-yellow) !important; }
        html[data-theme="v1"] .text-pac-black-50  { color: rgba(255,255,255,0.08) !important; }
        html[data-theme="v1"] .hover\:text-pac-yellow-600:hover { color: var(--v1-yellow) !important; }
        html[data-theme="v1"] .hover\:text-pac-yellow-300:hover { color: #f0c84a !important; }
        html[data-theme="v1"] .group:hover .group-hover\:text-white { color: #fff !important; }

        /* ── 테두리 ── */
        html[data-theme="v1"] .border-pac-black-100   { border-color: var(--v1-border)  !important; }
        html[data-theme="v1"] .border-pac-black-200   { border-color: var(--v1-border2) !important; }
        html[data-theme="v1"] .border-pac-black-50    { border-color: var(--v1-border)  !important; }
        html[data-theme="v1"] .border-green-200       { border-color: rgba(74,222,128,0.25) !important; }
        html[data-theme="v1"] .border-red-200         { border-color: rgba(232,0,67,0.25)   !important; }
        html[data-theme="v1"] .border-pac-yellow-200  { border-color: rgba(229,173,22,0.30) !important; }
        html[data-theme="v1"] .border-pac-yellow-300  { border-color: rgba(229,173,22,0.45) !important; }
        html[data-theme="v1"] .divide-pac-black-100 > * + * { border-color: var(--v1-border) !important; }
        html[data-theme="v1"] .divide-pac-black-50  > * + * { border-color: var(--v1-border) !important; }

        /* ── 폼 인풋 ── */
        html[data-theme="v1"] input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):not([type="file"]),
        html[data-theme="v1"] select,
        html[data-theme="v1"] textarea {
            background-color: #1E1E1E !important;
            border-color: var(--v1-border2) !important;
            color: var(--v1-text) !important;
        }
        html[data-theme="v1"] input::placeholder,
        html[data-theme="v1"] textarea::placeholder { color: rgba(255,255,255,0.25) !important; }

        /* ── 호버 인터랙션 ── */
        html[data-theme="v1"] .hover\:bg-white\/5:hover       { background-color: rgba(255,255,255,0.04) !important; }
        html[data-theme="v1"] .hover\:bg-white\/\[0\.02\]:hover { background-color: rgba(255,255,255,0.025) !important; }
        html[data-theme="v1"] .hover\:shadow-md:hover { box-shadow: none !important; }
        html[data-theme="v1"] .hover\:-translate-y-1:hover { transform: translateY(-4px) !important; }

        /* ── 디스플레이 폰트 → Bebas Neue (메인 페이지와 동일) ── */
        html[data-theme="v1"] .font-display,
        html[data-theme="v1"] h1.font-display,
        html[data-theme="v1"] h2.font-display,
        html[data-theme="v1"] h3.font-display,
        html[data-theme="v1"] p.font-display,
        html[data-theme="v1"] span.font-display,
        html[data-theme="v1"] a.font-display,
        html[data-theme="v1"] button.font-display,
        html[data-theme="v1"] div.font-display {
            font-family: 'Bebas Neue', 'Barlow Condensed', sans-serif !important;
        }

        /* ── prose (RichEditor 출력) ── */
        html[data-theme="v1"] .prose { color: rgba(255,255,255,0.8) !important; }
        html[data-theme="v1"] .prose h1, html[data-theme="v1"] .prose h2,
        html[data-theme="v1"] .prose h3 { color: var(--v1-text) !important; }
        html[data-theme="v1"] .prose a  { color: var(--v1-yellow) !important; }
        html[data-theme="v1"] .prose strong { color: var(--v1-text) !important; }
        html[data-theme="v1"] .prose hr { border-color: var(--v1-border) !important; }

        /* ── 어드민 스위처 바 스타일 ── */
        .theme-admin-bar { background:#121212; border-bottom:1px solid rgba(229,173,22,0.35); }
        .theme-btn-v1 { background:#E5AD16; color:#1A1212; border:none; }
        .theme-btn-v2 { background:transparent; color:rgba(255,255,255,0.45); border:1px solid rgba(255,255,255,0.15); }
        .theme-btn-v2:hover { color:rgba(255,255,255,0.75); border-color:rgba(255,255,255,0.35); }
        </style>
        @endif

        {{-- ═══════════════════════════════════════════════════════════
             V2 THEME — ENERGY BURST
             크림 배경 / Anton / pac-yellow 히어로 / 날카로운 앵귤러
        ═══════════════════════════════════════════════════════════ --}}
        @if(($activeTheme ?? 'v1') === 'v2')
        <style>
        :root {
            --v2-bg:     #F5F3EE;
            --v2-card:   #FFFFFF;
            --v2-nav:    #1A1212;
            --v2-text:   #1A1212;
            --v2-muted:  rgba(26,18,18,0.55);
            --v2-border: rgba(26,18,18,0.10);
            --v2-yellow: #E5AD16;
            --v2-pink:   #E80043;
        }

        html[data-theme="v2"],
        html[data-theme="v2"] body {
            background-color: var(--v2-bg) !important;
            color: var(--v2-text) !important;
        }

        /* rounded 를 더 angular하게 */
        html[data-theme="v2"] .rounded-2xl,
        html[data-theme="v2"] .rounded-xl { border-radius: 4px !important; }
        html[data-theme="v2"] .rounded-lg,
        html[data-theme="v2"] .rounded-md { border-radius: 3px !important; }
        html[data-theme="v2"] .rounded-full { border-radius: 9999px !important; }

        /* 배경 */
        html[data-theme="v2"] .bg-pac-black-50  { background-color: var(--v2-bg)   !important; }
        html[data-theme="v2"] .bg-white         { background-color: var(--v2-card) !important; }
        html[data-theme="v2"] .bg-pac-black-900 { background-color: var(--v2-nav)  !important; }

        /* 텍스트: 기존 다크 텍스트 그대로 유지 (이미 어두움) */
        html[data-theme="v2"] .text-pac-black-200,
        html[data-theme="v2"] .text-pac-black-300 { color: rgba(26,18,18,0.65) !important; }

        /* 테두리 */
        html[data-theme="v2"] .border-pac-black-100 { border-color: var(--v2-border) !important; }
        html[data-theme="v2"] .border-pac-black-50  { border-color: var(--v2-border) !important; }
        html[data-theme="v2"] .divide-pac-black-100 > * + * { border-color: var(--v2-border) !important; }

        /* 디스플레이 폰트 → Anton */
        html[data-theme="v2"] .font-display,
        html[data-theme="v2"] h1.font-display,
        html[data-theme="v2"] h2.font-display,
        html[data-theme="v2"] h3.font-display,
        html[data-theme="v2"] p.font-display,
        html[data-theme="v2"] span.font-display,
        html[data-theme="v2"] a.font-display,
        html[data-theme="v2"] button.font-display,
        html[data-theme="v2"] div.font-display {
            font-family: 'Anton', 'Barlow Condensed', sans-serif !important;
        }

        /* 어드민 스위처 바 */
        .theme-admin-bar { background:#E5AD16; border-bottom:2px solid #1A1212; }
        .theme-btn-v1 { background:transparent; color:rgba(26,18,18,0.55); border:2px solid rgba(26,18,18,0.25); }
        .theme-btn-v1:hover { background:rgba(26,18,18,0.10); color:#1A1212; }
        .theme-btn-v2 { background:#1A1212; color:#E5AD16; border:2px solid #1A1212; }
        </style>
        @endif
    </head>

    <body class="font-body antialiased bg-pac-black-50 text-pac-black-900 min-h-screen">

        {{-- 어드민 전용 테마 스위처 바 --}}
        @auth
        @if(in_array(auth()->user()->role, ['super_admin', 'region_admin']))
        <div class="theme-admin-bar"
             style="position:fixed;top:0;left:0;right:0;z-index:9999;
                    display:flex;align-items:center;justify-content:space-between;
                    padding:0 20px;height:34px;">
            <span style="font-size:9px;font-weight:700;letter-spacing:4px;text-transform:uppercase;
                         opacity:.6;font-family:'Barlow Condensed',sans-serif;">
                🎨 THEME
            </span>
            <div style="display:flex;align-items:center;gap:6px;">
                <form method="POST" action="{{ route('theme.switch') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="theme" value="v1">
                    <button type="submit" class="theme-btn-v1"
                            style="padding:2px 12px;font-size:9px;font-weight:800;letter-spacing:2px;
                                   text-transform:uppercase;cursor:pointer;font-family:'Barlow Condensed',sans-serif;">
                        V1 DARK
                    </button>
                </form>
                <form method="POST" action="{{ route('theme.switch') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="theme" value="v2">
                    <button type="submit" class="theme-btn-v2"
                            style="padding:2px 12px;font-size:9px;font-weight:800;letter-spacing:2px;
                                   text-transform:uppercase;cursor:pointer;font-family:'Barlow Condensed',sans-serif;">
                        V2 ENERGY
                    </button>
                </form>
                <a href="/admin/theme-settings"
                   style="font-size:9px;font-weight:700;letter-spacing:1px;text-decoration:none;
                          margin-left:6px;opacity:.5;font-family:'Barlow Condensed',sans-serif;">
                    설정 →
                </a>
            </div>
        </div>
        {{-- 스위처 바 높이만큼 여백 --}}
        <div style="height:34px;"></div>
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
