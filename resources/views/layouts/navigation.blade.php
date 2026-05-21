<header class="bg-pac-black-900 sticky top-0 z-50 h-[60px]"
        x-data="{ drawerOpen: false }">
    <div class="h-full flex items-center justify-between px-4 lg:px-8">

        <!-- 로고 -->
        <a href="{{ route('dashboard') }}" class="flex items-center gap-1">
            <span class="font-display text-2xl font-bold text-pac-yellow-500 uppercase tracking-tight">PAC</span>
            <span class="font-display text-2xl font-bold text-white uppercase tracking-tight">RUN</span>
            <span class="font-display text-xs text-pac-black-400 ml-2 hidden sm:block uppercase tracking-widest">CREW</span>
        </a>

        <!-- 데스크탑 네비 -->
        <nav class="hidden lg:flex items-center gap-6">
            <a href="{{ route('dashboard') }}"
               class="font-body text-sm transition-colors duration-200
                      {{ request()->routeIs('dashboard') ? 'text-pac-yellow-400 font-semibold' : 'text-pac-black-300 hover:text-pac-yellow-400' }}">
                대시보드
            </a>
            <a href="{{ route('running-logs.index') }}"
               class="font-body text-sm transition-colors duration-200
                      {{ request()->routeIs('running-logs*') ? 'text-pac-yellow-400 font-semibold' : 'text-pac-black-300 hover:text-pac-yellow-400' }}">
                기록
            </a>
            <a href="#"
               class="font-body text-sm text-pac-black-300 hover:text-pac-yellow-400 transition-colors duration-200">
                이벤트
            </a>
            <a href="{{ route('photos.index') }}"
               class="font-body text-sm transition-colors duration-200
                      {{ request()->routeIs('photos*') ? 'text-pac-yellow-400 font-semibold' : 'text-pac-black-300 hover:text-pac-yellow-400' }}">
                포토
            </a>
            <a href="#"
               class="font-body text-sm text-pac-black-300 hover:text-pac-yellow-400 transition-colors duration-200">
                순위
            </a>
            <a href="{{ route('bug-reports.index') }}"
               class="font-body text-sm transition-colors duration-200
                      {{ request()->routeIs('bug-reports*') ? 'text-pac-yellow-400 font-semibold' : 'text-pac-black-300 hover:text-pac-yellow-400' }}">
                버그 제보
            </a>
        </nav>

        <!-- 우측 영역 -->
        <div class="flex items-center gap-1">
            <!-- 알림 버튼 -->
            <button class="relative p-2 text-pac-black-400 hover:text-pac-pink-400 transition-colors duration-200
                           min-h-[44px] min-w-[44px] flex items-center justify-center
                           focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:ring-offset-2 focus:ring-offset-pac-black-900 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </button>

            <!-- 유저명 + 로그아웃: 데스크탑 -->
            <div class="hidden lg:flex items-center gap-2">
                <span class="font-body text-sm text-pac-black-300 px-1">
                    {{ Auth::user()->nickname ?? Auth::user()->name }}
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="p-2 text-pac-black-400 hover:text-pac-pink-400 transition-colors duration-200
                                   min-h-[44px] min-w-[44px] flex items-center justify-center
                                   focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:ring-offset-2 focus:ring-offset-pac-black-900 rounded-lg"
                            title="로그아웃">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- 유저명: 태블릿(md)만 표시 -->
            <span class="hidden md:block lg:hidden font-body text-sm text-pac-black-300 px-1">
                {{ Auth::user()->nickname ?? Auth::user()->name }}
            </span>

            <!-- 햄버거: 데스크탑에서 숨김 -->
            <button @click="drawerOpen = true"
                    class="lg:hidden p-2 text-pac-black-300 hover:text-white transition-colors duration-200
                           min-h-[44px] min-w-[44px] flex items-center justify-center
                           focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:ring-offset-2 focus:ring-offset-pac-black-900 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- 드로어 오버레이 -->
    <div x-show="drawerOpen"
         @click="drawerOpen = false"
         x-transition:enter="transition-opacity duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden"
         style="display:none;"></div>

    <!-- 슬라이드 드로어 -->
    <aside x-show="drawerOpen"
           x-transition:enter="transition-transform duration-300"
           x-transition:enter-start="translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition-transform duration-300"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="translate-x-full"
           class="fixed top-0 right-0 h-full w-64 bg-pac-black-900 z-50 lg:hidden flex flex-col border-l border-white/10"
           style="display:none;">

        <!-- 드로어 상단 -->
        <div class="h-[60px] flex items-center justify-between px-5 border-b border-white/10">
            <div class="flex items-center gap-1">
                <span class="font-display text-xl font-bold text-pac-yellow-500">PAC</span>
                <span class="font-display text-xl font-bold text-white">RUN</span>
            </div>
            <button @click="drawerOpen = false"
                    class="text-pac-black-300 hover:text-white p-2 transition-colors duration-200 focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- 드로어 네비 -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

            <!-- 사용자 정보 -->
            <div class="px-3 py-3 mb-3 border-b border-white/10">
                <p class="font-body text-sm font-semibold text-white">{{ Auth::user()->nickname ?? Auth::user()->name }}</p>
                <p class="font-body text-xs text-pac-black-400 mt-0.5">{{ Auth::user()->email }}</p>
            </div>

            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-3 rounded-xl font-body text-sm transition-colors duration-200
                      {{ request()->routeIs('dashboard') ? 'bg-pac-yellow-500/15 text-pac-yellow-400 font-semibold' : 'text-pac-black-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                대시보드
            </a>

            <a href="{{ route('running-logs.index') }}"
               class="flex items-center gap-3 px-3 py-3 rounded-xl font-body text-sm transition-colors duration-200
                      {{ request()->routeIs('running-logs*') ? 'bg-pac-yellow-500/15 text-pac-yellow-400 font-semibold' : 'text-pac-black-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                기록
            </a>

            <a href="#"
               class="flex items-center gap-3 px-3 py-3 rounded-xl font-body text-sm text-pac-black-300 hover:bg-white/5 hover:text-white transition-colors duration-200">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                이벤트
            </a>

            <a href="{{ route('photos.index') }}"
               class="flex items-center gap-3 px-3 py-3 rounded-xl font-body text-sm transition-colors duration-200
                      {{ request()->routeIs('photos*') ? 'bg-pac-yellow-500/15 text-pac-yellow-400 font-semibold' : 'text-pac-black-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                포토
            </a>

            <a href="#"
               class="flex items-center gap-3 px-3 py-3 rounded-xl font-body text-sm text-pac-black-300 hover:bg-white/5 hover:text-white transition-colors duration-200">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                순위
            </a>

            <a href="{{ route('bug-reports.index') }}"
               class="flex items-center gap-3 px-3 py-3 rounded-xl font-body text-sm transition-colors duration-200
                      {{ request()->routeIs('bug-reports*') ? 'bg-pac-yellow-500/15 text-pac-yellow-400 font-semibold' : 'text-pac-black-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                버그 제보
            </a>

            <!-- 하단 구분선 -->
            <div class="pt-3 mt-3 border-t border-white/10 space-y-1">
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl font-body text-sm text-pac-black-300 hover:bg-white/5 hover:text-white transition-colors duration-200">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    내 정보
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-3 py-3 rounded-xl font-body text-sm
                                   text-pac-black-400 hover:bg-white/5 hover:text-pac-red-500
                                   transition-colors duration-200 text-left focus:outline-none">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        로그아웃
                    </button>
                </form>
            </div>
        </nav>
    </aside>
</header>
