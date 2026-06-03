{{-- ===================================================
     PAC-RUN CREW — Global Navigation
     Dark Athletic Magazine aesthetic
     Bebas Neue logo · pac-yellow active states
     ================================================= --}}
<header x-data="{ open: false }"
        class="sticky top-0 z-40 bg-pac-black-900 border-b border-white/[0.06] backdrop-blur-md">

  {{-- 메인 바 --}}
  <div class="h-[60px] flex items-center justify-between px-5 lg:px-8">

    {{-- LOGO --}}
    <a href="{{ route('dashboard') }}"
       class="flex items-center gap-0 group shrink-0">
      <span class="font-display text-2xl font-black text-pac-yellow-500 tracking-tighter leading-none uppercase
                   group-hover:text-pac-yellow-400 transition-colors duration-150">PAC</span>
      <span class="font-display text-2xl font-black text-white tracking-tighter leading-none uppercase
                   group-hover:text-white/80 transition-colors duration-150">-RUN</span>
      <span class="hidden sm:block font-display text-[9px] font-bold text-pac-black-500 uppercase tracking-[0.3em] ml-2.5 mt-0.5 self-end mb-1">
        CREW
      </span>
    </a>

    {{-- 데스크탑 네비 --}}
    <nav class="hidden lg:flex items-center gap-1">
      @php
        $navItems = [
          ['route' => 'dashboard',          'label' => '마이페이지', 'match' => 'dashboard'],
          ['route' => 'running-logs.index', 'label' => '기록',      'match' => 'running-logs*'],
          ['route' => 'events.index',       'label' => '이벤트',    'match' => 'events*'],
          ['route' => 'photos.index',       'label' => '포토',      'match' => 'photos*'],
          ['route' => 'ranking.index',      'label' => '순위',      'match' => 'ranking*'],
          ['route' => 'notices.index',      'label' => '공지',      'match' => 'notices*'],
          ['route' => 'bug-reports.index',  'label' => '제보',      'match' => 'bug-reports*'],
        ];
      @endphp

      @foreach($navItems as $item)
        @php $isActive = request()->routeIs($item['match']); @endphp
        <a href="{{ route($item['route']) }}"
           class="relative px-3.5 py-1.5 font-display text-[11px] font-bold uppercase tracking-[0.12em]
                  transition-colors duration-150
                  {{ $isActive
                      ? 'text-pac-yellow-400'
                      : 'text-pac-black-400 hover:text-white' }}">
          {{ $item['label'] }}
          @if($isActive)
            <span class="absolute bottom-0 left-3.5 right-3.5 h-[2px] bg-pac-yellow-400 rounded-full"></span>
          @endif
        </a>
      @endforeach
    </nav>

    {{-- 우측 영역 --}}
    <div class="flex items-center gap-2">

      {{-- 사용자 정보 (데스크탑) --}}
      <div class="hidden lg:flex items-center gap-3">
        {{-- 아바타 --}}
        @php
          $nick = Auth::user()->nickname ?? Auth::user()->name ?? '?';
          $initials = mb_strtoupper(mb_substr($nick, 0, 1));
          $role = Auth::user()->role;
          $roleColors = [
            'super_admin'  => 'bg-pac-pink-500',
            'region_admin' => 'bg-pac-yellow-500',
            'operator'     => 'bg-pac-green-500',
            'member'       => 'bg-pac-black-600',
          ];
          $avatarBg = $roleColors[$role] ?? 'bg-pac-black-600';
        @endphp
        <div class="w-8 h-8 rounded-full {{ $avatarBg }} flex items-center justify-center shrink-0">
          <span class="font-display text-sm font-black text-white leading-none">{{ $initials }}</span>
        </div>
        <span class="font-body text-sm font-semibold text-pac-black-300 max-w-[100px] truncate">
          {{ $nick }}
        </span>
        <form method="POST" action="{{ route('logout') }}" class="inline">
          @csrf
          <button type="submit"
                  class="p-2 text-pac-black-500 hover:text-pac-pink-400 transition-colors"
                  title="로그아웃">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
          </button>
        </form>
      </div>

      {{-- 기록 추가 버튼 (항상 표시) --}}
      <a href="{{ route('running-logs.create') }}"
         class="hidden md:inline-flex items-center gap-1.5 px-3.5 py-1.5
                bg-pac-yellow-500 hover:bg-pac-yellow-400
                text-pac-black-900 font-display font-black text-[11px] uppercase tracking-[0.1em]
                transition-colors duration-150">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        기록 추가
      </a>

      {{-- 햄버거 (모바일) --}}
      <button @click="open = !open"
              class="lg:hidden p-2 text-pac-black-400 hover:text-white transition-colors rounded">
        <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
  </div>

  {{-- 모바일 드로어 오버레이 --}}
  <div x-show="open"
       x-transition:enter="transition-opacity duration-200"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition-opacity duration-200"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       @click="open = false"
       class="fixed inset-0 bg-black/60 z-40 lg:hidden backdrop-blur-sm"
       style="display:none;"></div>

  {{-- 모바일 슬라이드 드로어 --}}
  <aside x-show="open"
         x-transition:enter="transition-transform duration-250 ease-out"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition-transform duration-200 ease-in"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed top-0 right-0 h-full w-72 bg-pac-black-900 z-50 lg:hidden
                flex flex-col border-l border-white/[0.06]"
         style="display:none;">

    {{-- 드로어 헤더 --}}
    <div class="h-[60px] flex items-center justify-between px-5 border-b border-white/[0.06]">
      <div class="flex items-center gap-2">
        <div class="w-9 h-9 rounded-full {{ $avatarBg ?? 'bg-pac-black-600' }} flex items-center justify-center">
          <span class="font-display text-base font-black text-white">{{ $initials ?? '?' }}</span>
        </div>
        <div>
          <p class="font-body text-sm font-semibold text-white leading-tight">{{ $nick ?? '' }}</p>
          <p class="font-display text-[9px] text-pac-black-500 uppercase tracking-widest">
            {{ match($role ?? '') {
              'super_admin'  => 'SUPER ADMIN',
              'region_admin' => 'REGION ADMIN',
              'operator'     => 'OPERATOR',
              default        => 'MEMBER'
            } }}
          </p>
        </div>
      </div>
      <button @click="open = false" class="p-1 text-pac-black-500 hover:text-white transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    {{-- 드로어 네비 --}}
    <nav class="flex-1 px-2 py-3 overflow-y-auto space-y-0.5">
      @foreach($navItems as $item)
        @php $isActive = request()->routeIs($item['match']); @endphp
        <a href="{{ route($item['route']) }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg font-display text-sm font-bold uppercase tracking-wider
                  transition-colors duration-150
                  {{ $isActive
                      ? 'bg-pac-yellow-500/15 text-pac-yellow-400 border-l-2 border-pac-yellow-400'
                      : 'text-pac-black-400 hover:bg-white/5 hover:text-white border-l-2 border-transparent' }}">
          {{ $item['label'] }}
        </a>
      @endforeach
    </nav>

    {{-- 드로어 하단 --}}
    <div class="px-2 py-3 border-t border-white/[0.06] space-y-0.5">
      <a href="{{ route('running-logs.create') }}"
         class="flex items-center justify-center gap-2 px-4 py-2.5 bg-pac-yellow-500 hover:bg-pac-yellow-400
                text-pac-black-900 font-display font-black text-sm uppercase tracking-wider
                transition-colors duration-150 rounded-lg mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        기록 추가
      </a>
      <a href="{{ route('profile.edit') }}"
         class="flex items-center gap-3 px-4 py-3 rounded-lg font-display text-sm font-bold uppercase tracking-wider
                text-pac-black-400 hover:bg-white/5 hover:text-white transition-colors duration-150">
        내 정보
      </a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-lg font-display text-sm font-bold uppercase tracking-wider
                       text-pac-black-500 hover:bg-pac-pink-500/10 hover:text-pac-pink-400 transition-colors duration-150">
          로그아웃
        </button>
      </form>
    </div>
  </aside>

</header>
