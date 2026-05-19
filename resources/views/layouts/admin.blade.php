<!DOCTYPE html>
<html lang="ko" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>관리자 · PAC-RUN CREW</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Noto+Sans+KR:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body antialiased bg-pac-black-50 text-pac-black-900 h-full">

<div class="flex h-full min-h-screen" x-data="{ sidebarOpen: false }">

    {{-- 모바일 오버레이 --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/60 z-30 lg:hidden"
         style="display: none;"></div>

    {{-- 사이드바 --}}
    <aside x-show="sidebarOpen || true"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-pac-black-900 flex flex-col
                  transition-transform duration-200 ease-in-out lg:translate-x-0"
           style="display: flex;">

        {{-- 로고 --}}
        <div class="flex items-center gap-2 px-5 h-[60px] border-b border-pac-black-700 shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-baseline gap-1">
                <span class="font-display text-xl font-bold text-pac-yellow-500 tracking-tight uppercase">PAC</span>
                <span class="font-display text-xl font-bold text-white tracking-tight uppercase">RUN</span>
                <span class="font-display text-xs font-bold text-pac-pink-500 tracking-widest uppercase ml-1">ADMIN</span>
            </a>
        </div>

        {{-- 어드민 사용자 정보 --}}
        <div class="px-4 py-3 border-b border-pac-black-700 shrink-0">
            <p class="font-body text-xs text-pac-black-400 uppercase tracking-wide">관리자</p>
            <p class="font-body text-sm font-semibold text-white mt-0.5 truncate">
                {{ auth()->user()->nickname ?? auth()->user()->name }}
            </p>
            <span class="inline-block font-display text-[10px] font-bold uppercase tracking-widest
                         px-2 py-0.5 rounded-full mt-1
                         {{ auth()->user()->isSuperAdmin() ? 'bg-pac-pink-500/20 text-pac-pink-400' : (auth()->user()->isRegionAdmin() ? 'bg-pac-yellow-500/20 text-pac-yellow-400' : 'bg-pac-black-600 text-pac-black-300') }}">
                {{ match(auth()->user()->role) {
                    'super_admin'  => 'Super Admin',
                    'region_admin' => 'Region Admin',
                    'operator'     => 'Operator',
                    default        => auth()->user()->role,
                } }}
            </span>
        </div>

        {{-- 네비게이션 --}}
        <nav class="flex-1 overflow-y-auto py-3 space-y-0.5 px-2">

            {{-- 대시보드 --}}
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.dashboard') ? 'bg-pac-yellow-500/15 text-pac-yellow-400' : 'text-pac-black-300 hover:bg-pac-black-800 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="font-body">대시보드</span>
            </a>

            {{-- 회원 관리 --}}
            <div class="pt-3 pb-1 px-3">
                <p class="font-display text-[10px] font-bold uppercase tracking-widest text-pac-black-500">회원</p>
            </div>

            <a href="{{ route('admin.members.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.members.*') ? 'bg-pac-yellow-500/15 text-pac-yellow-400' : 'text-pac-black-300 hover:bg-pac-black-800 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="font-body">구성원 목록</span>
            </a>

            <a href="{{ route('admin.applications.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.applications.*') ? 'bg-pac-yellow-500/15 text-pac-yellow-400' : 'text-pac-black-300 hover:bg-pac-black-800 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="font-body">모집 신청서</span>
            </a>

            {{-- 기수 관리 (슈퍼 어드민만) --}}
            @if(auth()->user()->isSuperAdmin())
                <div class="pt-3 pb-1 px-3">
                    <p class="font-display text-[10px] font-bold uppercase tracking-widest text-pac-black-500">기수</p>
                </div>

                <a href="{{ route('admin.generations.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors duration-150
                          {{ request()->routeIs('admin.generations.*') ? 'bg-pac-yellow-500/15 text-pac-yellow-400' : 'text-pac-black-300 hover:bg-pac-black-800 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span class="font-body">기수 관리</span>
                </a>
            @endif

            {{-- 이벤트 관리 --}}
            <div class="pt-3 pb-1 px-3">
                <p class="font-display text-[10px] font-bold uppercase tracking-widest text-pac-black-500">이벤트</p>
            </div>

            <a href="{{ route('admin.events.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.events.*') ? 'bg-pac-yellow-500/15 text-pac-yellow-400' : 'text-pac-black-300 hover:bg-pac-black-800 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="font-body">이벤트 목록</span>
            </a>

            {{-- 게시판 관리 --}}
            <div class="pt-3 pb-1 px-3">
                <p class="font-display text-[10px] font-bold uppercase tracking-widest text-pac-black-500">게시판</p>
            </div>

            <a href="{{ route('admin.notices.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.notices.*') ? 'bg-pac-yellow-500/15 text-pac-yellow-400' : 'text-pac-black-300 hover:bg-pac-black-800 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
                <span class="font-body">공지사항</span>
            </a>

            {{-- 소통 (슈퍼 / 지역관리자만) --}}
            @if(auth()->user()->isRegionAdmin())
                <div class="pt-3 pb-1 px-3">
                    <p class="font-display text-[10px] font-bold uppercase tracking-widest text-pac-black-500">소통</p>
                </div>

                <a href="{{ route('admin.sms.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors duration-150
                          {{ request()->routeIs('admin.sms.*') ? 'bg-pac-yellow-500/15 text-pac-yellow-400' : 'text-pac-black-300 hover:bg-pac-black-800 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    <span class="font-body">단체 문자</span>
                </a>
            @endif

            {{-- 통계 (슈퍼 / 지역관리자만) --}}
            @if(auth()->user()->isRegionAdmin())
                <div class="pt-3 pb-1 px-3">
                    <p class="font-display text-[10px] font-bold uppercase tracking-widest text-pac-black-500">통계</p>
                </div>

                <a href="{{ route('admin.stats.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors duration-150
                          {{ request()->routeIs('admin.stats.*') ? 'bg-pac-yellow-500/15 text-pac-yellow-400' : 'text-pac-black-300 hover:bg-pac-black-800 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="font-body">기록 통계</span>
                </a>

                <a href="{{ route('admin.export.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors duration-150
                          {{ request()->routeIs('admin.export.*') ? 'bg-pac-yellow-500/15 text-pac-yellow-400' : 'text-pac-black-300 hover:bg-pac-black-800 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span class="font-body">엑셀 다운로드</span>
                </a>
            @endif

        </nav>

        {{-- 하단: 일반 사이트 이동 + 로그아웃 --}}
        <div class="border-t border-pac-black-700 p-3 space-y-1 shrink-0">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                      text-pac-black-400 hover:bg-pac-black-800 hover:text-white transition-colors duration-150">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                <span class="font-body">일반 사이트</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                               text-pac-black-400 hover:bg-pac-black-800 hover:text-pac-red-400 transition-colors duration-150">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="font-body">로그아웃</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- 메인 영역 --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-auto">

        {{-- 모바일 탑바 --}}
        <header class="lg:hidden flex items-center justify-between px-4 h-[60px] bg-pac-black-900 border-b border-pac-black-700 shrink-0">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="p-2 rounded-lg text-pac-black-300 hover:text-white hover:bg-pac-black-800 transition-colors duration-150">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="font-display text-sm font-bold text-white uppercase tracking-tight">PAC-RUN ADMIN</span>
            <div class="w-9"></div>
        </header>

        {{-- 페이지 콘텐츠 --}}
        <main class="flex-1 p-4 md:p-6 lg:p-8">
            {{ $slot }}
        </main>
    </div>

</div>

</body>
</html>
