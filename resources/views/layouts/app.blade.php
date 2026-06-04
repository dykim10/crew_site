@php
    // 스킨값 결정: 1순위 로그인 사용자 DB, 2순위 기본값 _skin_v1
    $skinClass = '_skin_v1';
    if (auth()->check()) {
        $userDetail = auth()->user()->detail;
        if ($userDetail && $userDetail->skin_select) {
            $skinClass = $userDetail->skin_select;
        }
    }
    $activeTheme = ($skinClass === '_skin_v2') ? 'v2' : 'v1';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $activeTheme }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'PAC-RUN CREW') }}</title>

        {{-- PAC-RUN 공용 폰트 (항상 로드) --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow+Condensed:wght@400;600;700;800;900&family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- 스킨별 CSS 변수 (id 필수 — Ajax 교체용) --}}
        <link id="skin-css" rel="stylesheet"
              href="{{ asset('css/skin/' . $skinClass . '.css') }}">

        {{-- ═══════════════════════════════════════════════
             V1 THEME — DARK EDITORIAL
             #0D0D0D 배경 / Bebas Neue / pac-yellow
        ═══════════════════════════════════════════════ --}}
        {{-- 항상 두 테마 모두 렌더링 — JS 스킨 전환 시 data-theme 변경으로 즉시 적용 --}}
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

        html[data-theme="v1"],
        html[data-theme="v1"] body {
            background-color: var(--v1-bg) !important;
            color: var(--v1-text) !important;
        }

        /* rounded → 샤프 엣지 */
        html[data-theme="v1"] .rounded-2xl,
        html[data-theme="v1"] .rounded-xl,
        html[data-theme="v1"] .rounded-lg,
        html[data-theme="v1"] .rounded-md { border-radius: 2px !important; }
        html[data-theme="v1"] .rounded     { border-radius: 2px !important; }
        html[data-theme="v1"] .rounded-sm  { border-radius: 1px !important; }
        html[data-theme="v1"] .rounded-full { border-radius: 9999px !important; }

        /* 섀도 제거 */
        html[data-theme="v1"] .shadow-sm,
        html[data-theme="v1"] .shadow,
        html[data-theme="v1"] .shadow-md,
        html[data-theme="v1"] .shadow-lg { box-shadow: none !important; }

        /* 배경 */
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

        html[data-theme="v1"] .bg-green-50        { background-color: rgba(16,185,129,0.08)  !important; }
        html[data-theme="v1"] .bg-red-50          { background-color: rgba(232,0,67,0.08)    !important; }
        html[data-theme="v1"] .bg-pac-yellow-50   { background-color: rgba(229,173,22,0.06)  !important; }
        html[data-theme="v1"] .bg-pac-yellow-100  { background-color: rgba(229,173,22,0.10)  !important; }
        html[data-theme="v1"] .bg-pac-yellow-200  { background-color: rgba(229,173,22,0.15)  !important; }
        html[data-theme="v1"] .bg-pac-black-800\/30 { background-color: rgba(34,34,34,0.3)  !important; }

        /* 텍스트 */
        html[data-theme="v1"] .text-pac-black-900 { color: var(--v1-text)              !important; }
        html[data-theme="v1"] .text-pac-black-800 { color: rgba(255,255,255,0.92)      !important; }
        html[data-theme="v1"] .text-pac-black-700 { color: rgba(255,255,255,0.78)      !important; }
        html[data-theme="v1"] .text-pac-black-600 { color: rgba(255,255,255,0.60)      !important; }
        html[data-theme="v1"] .text-pac-black-500 { color: rgba(255,255,255,0.45)      !important; }
        html[data-theme="v1"] .text-pac-black-400 { color: rgba(255,255,255,0.32)      !important; }
        html[data-theme="v1"] .text-pac-black-300 { color: rgba(255,255,255,0.22)      !important; }
        html[data-theme="v1"] .text-pac-black-200 { color: rgba(255,255,255,0.88)      !important; }
        html[data-theme="v1"] .text-gray-900      { color: var(--v1-text)              !important; }
        html[data-theme="v1"] .text-gray-700      { color: rgba(255,255,255,0.78)      !important; }
        html[data-theme="v1"] .text-gray-600      { color: rgba(255,255,255,0.60)      !important; }
        html[data-theme="v1"] .text-gray-500      { color: rgba(255,255,255,0.45)      !important; }
        html[data-theme="v1"] .text-green-800     { color: #4ade80                     !important; }
        html[data-theme="v1"] .text-green-700     { color: #34d399                     !important; }
        html[data-theme="v1"] .text-pac-yellow-700 { color: var(--v1-yellow)           !important; }
        html[data-theme="v1"] .text-pac-yellow-600 { color: var(--v1-yellow)           !important; }
        html[data-theme="v1"] .text-pac-black-50   { color: rgba(255,255,255,0.08)     !important; }
        html[data-theme="v1"] .hover\:text-pac-yellow-600:hover { color: var(--v1-yellow) !important; }
        html[data-theme="v1"] .hover\:text-pac-yellow-300:hover { color: #f0c84a       !important; }
        html[data-theme="v1"] .group:hover .group-hover\:text-white { color: #fff      !important; }

        /* 테두리 */
        html[data-theme="v1"] .border-pac-black-100   { border-color: var(--v1-border)           !important; }
        html[data-theme="v1"] .border-pac-black-200   { border-color: var(--v1-border2)          !important; }
        html[data-theme="v1"] .border-pac-black-50    { border-color: var(--v1-border)           !important; }
        html[data-theme="v1"] .border-green-200       { border-color: rgba(74,222,128,0.25)      !important; }
        html[data-theme="v1"] .border-red-200         { border-color: rgba(232,0,67,0.25)        !important; }
        html[data-theme="v1"] .border-pac-yellow-200  { border-color: rgba(229,173,22,0.30)      !important; }
        html[data-theme="v1"] .border-pac-yellow-300  { border-color: rgba(229,173,22,0.45)      !important; }
        html[data-theme="v1"] .divide-pac-black-100 > * + * { border-color: var(--v1-border)     !important; }
        html[data-theme="v1"] .divide-pac-black-50  > * + * { border-color: var(--v1-border)     !important; }

        /* 폼 인풋 */
        html[data-theme="v1"] input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):not([type="file"]),
        html[data-theme="v1"] select,
        html[data-theme="v1"] textarea {
            background-color: #1E1E1E  !important;
            border-color: var(--v1-border2) !important;
            color: var(--v1-text)          !important;
        }
        html[data-theme="v1"] input::placeholder,
        html[data-theme="v1"] textarea::placeholder { color: rgba(255,255,255,0.25) !important; }

        /* 호버 */
        html[data-theme="v1"] .hover\:bg-white\/5:hover        { background-color: rgba(255,255,255,0.04)  !important; }
        html[data-theme="v1"] .hover\:bg-white\/\[0\.02\]:hover { background-color: rgba(255,255,255,0.025) !important; }
        html[data-theme="v1"] .hover\:shadow-md:hover { box-shadow: none !important; }
        html[data-theme="v1"] .hover\:-translate-y-1:hover { transform: translateY(-4px) !important; }

        /* prose */
        html[data-theme="v1"] .prose         { color: rgba(255,255,255,0.8)  !important; }
        html[data-theme="v1"] .prose h1,
        html[data-theme="v1"] .prose h2,
        html[data-theme="v1"] .prose h3      { color: var(--v1-text)         !important; }
        html[data-theme="v1"] .prose a       { color: var(--v1-yellow)       !important; }
        html[data-theme="v1"] .prose strong  { color: var(--v1-text)         !important; }
        html[data-theme="v1"] .prose hr      { border-color: var(--v1-border) !important; }
        </style>

        {{-- ═══════════════════════════════════════════════
             V2 THEME — ENERGY BURST
             크림 배경 / pac-yellow 히어로
        ═══════════════════════════════════════════════ --}}
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
            color: var(--v2-text)          !important;
        }

        html[data-theme="v2"] .rounded-2xl,
        html[data-theme="v2"] .rounded-xl  { border-radius: 4px !important; }
        html[data-theme="v2"] .rounded-lg,
        html[data-theme="v2"] .rounded-md  { border-radius: 3px !important; }
        html[data-theme="v2"] .rounded-full { border-radius: 9999px !important; }

        html[data-theme="v2"] .bg-pac-black-50  { background-color: var(--v2-bg)   !important; }
        html[data-theme="v2"] .bg-white         { background-color: var(--v2-card) !important; }
        html[data-theme="v2"] .bg-pac-black-900 { background-color: var(--v2-nav)  !important; }

        html[data-theme="v2"] .text-pac-black-200,
        html[data-theme="v2"] .text-pac-black-300 { color: rgba(26,18,18,0.65) !important; }

        html[data-theme="v2"] .border-pac-black-100 { border-color: var(--v2-border) !important; }
        html[data-theme="v2"] .border-pac-black-50  { border-color: var(--v2-border) !important; }
        html[data-theme="v2"] .divide-pac-black-100 > * + * { border-color: var(--v2-border) !important; }
        </style>
    </head>

    <body class="{{ $skinClass }} font-body antialiased bg-pac-black-50 text-pac-black-900 min-h-screen">


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

    {{-- 스킨 전환 Ajax 스크립트 --}}
    <script>
    function changeSkin(skin) {
        fetch('{{ route("skin.change") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ skin: skin }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // body 클래스 교체
                document.body.className = document.body.className
                    .replace(/_skin_v1|_skin_v2/g, '').trim();
                document.body.classList.add(data.skin);

                // data-theme 속성 교체 (기존 테마 스타일 호환)
                document.documentElement.setAttribute('data-theme',
                    data.skin === '_skin_v2' ? 'v2' : 'v1');

                // 스킨 CSS link 교체
                document.getElementById('skin-css').href
                    = '/css/skin/' + data.skin + '.css';

                // nav 스킨 버튼 active 상태 갱신
                document.querySelectorAll('.pac-btn-skin').forEach(btn => {
                    btn.classList.remove('active');
                    if (btn.dataset.skin === data.skin) btn.classList.add('active');
                });
            }
        })
        .catch(error => console.error('스킨 변경 실패:', error));
    }
    </script>
</html>
