{{-- PAC-RUN Global Navigation — dark editorial, v1 unified --}}
<header class="pac-nav" x-data="{ open: false }">

  {{-- LOGO --}}
  <a href="{{ route('home') }}" class="pac-nav-logo">
    PAC<span class="logo-suffix">-RUN</span>
  </a>

  {{-- Desktop Nav Links --}}
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

  <ul class="pac-nav-links hidden lg:flex">
    @foreach($navItems as $item)
      @php $isActive = request()->routeIs($item['match']); @endphp
      <li>
        <a href="{{ route($item['route']) }}"
           class="{{ $isActive ? 'pac-active' : '' }}">
          {{ $item['label'] }}
        </a>
      </li>
    @endforeach
  </ul>

  {{-- 우측 영역 --}}
  @php
    $nick      = Auth::user()->nickname ?? Auth::user()->name ?? '?';
    $initial   = mb_strtoupper(mb_substr($nick, 0, 1));
    $role      = Auth::user()->role;
    $avatarBg  = match($role) {
      'super_admin'  => '#E80043',
      'region_admin' => '#E5AD16',
      'operator'     => '#10b981',
      default        => '#2D2020',
    };
    $avatarTxt = ($role === 'region_admin') ? '#1A1212' : '#ffffff';
  @endphp

  <div class="flex items-center gap-3">

    {{-- 사용자 아바타 + 이름 (데스크탑) --}}
    <div class="hidden lg:flex items-center gap-2.5">
      <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"
           style="background:{{ $avatarBg }};">
        <span class="font-display text-sm leading-none"
              style="color:{{ $avatarTxt }};">{{ $initial }}</span>
      </div>
      <span class="font-body text-xs text-pac-black-400 max-w-[88px] truncate">
        {{ $nick }}
      </span>
      <form method="POST" action="{{ route('logout') }}" class="inline">
        @csrf
        <button type="submit"
                class="font-display text-[10px] tracking-widest uppercase
                       text-pac-black-500 hover:text-pac-pink-400 transition-colors duration-150">
          OUT
        </button>
      </form>
    </div>

    {{-- 기록 추가 CTA --}}
    <a href="{{ route('running-logs.create') }}"
       class="pac-nav-cta hidden md:inline-flex">
      + 기록
    </a>

    {{-- 햄버거 (모바일) --}}
    <button @click="open = !open"
            class="lg:hidden p-1.5 text-pac-black-400 hover:text-white transition-colors">
      <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
      <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>

  </div>

  {{-- 모바일 오버레이 --}}
  <div x-show="open"
       @click="open = false"
       x-transition:enter="transition-opacity duration-200"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition-opacity duration-200"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       class="fixed inset-0 bg-black/70 z-40 lg:hidden"
       style="display:none;"></div>

  {{-- 모바일 슬라이드 드로어 --}}
  <aside x-show="open"
         x-transition:enter="transition-transform duration-250 ease-out"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition-transform duration-200 ease-in"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed top-0 right-0 h-full w-72 z-50 lg:hidden flex flex-col"
         style="background:#0D0D0D;border-left:1px solid rgba(255,255,255,0.06);display:none;">

    {{-- 드로어 헤더 --}}
    <div class="h-[68px] flex items-center justify-between px-5"
         style="border-bottom:1px solid rgba(255,255,255,0.06);">
      <div class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0"
             style="background:{{ $avatarBg }};">
          <span class="font-display text-base leading-none"
                style="color:{{ $avatarTxt }};">{{ $initial }}</span>
        </div>
        <div>
          <p class="font-body text-sm font-semibold text-white leading-tight">{{ $nick }}</p>
          <p class="font-display text-[9px] tracking-widest uppercase"
             style="color:rgba(255,255,255,.3);">
            {{ match($role) {
              'super_admin'  => 'SUPER ADMIN',
              'region_admin' => 'REGION ADMIN',
              'operator'     => 'OPERATOR',
              default        => 'MEMBER'
            } }}
          </p>
        </div>
      </div>
      <button @click="open = false"
              class="p-1 transition-colors"
              style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.4);">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    {{-- 드로어 네비 --}}
    <nav class="flex-1 px-2 py-3 overflow-y-auto">
      @foreach($navItems as $item)
        @php $isActive = request()->routeIs($item['match']); @endphp
        <a href="{{ route($item['route']) }}"
           class="flex items-center px-4 py-3.5 mb-0.5 font-display text-[11px] tracking-widest uppercase
                  text-decoration-none transition-colors duration-150"
           style="text-decoration:none;
                  {{ $isActive
                    ? 'color:#E5AD16;border-left:2px solid #E5AD16;background:rgba(229,173,22,0.06);'
                    : 'color:rgba(255,255,255,.40);border-left:2px solid transparent;' }}">
          {{ $item['label'] }}
        </a>
      @endforeach
    </nav>

    {{-- 드로어 하단 --}}
    <div class="px-2 py-3" style="border-top:1px solid rgba(255,255,255,0.06);">
      <a href="{{ route('running-logs.create') }}"
         class="pac-nav-cta flex items-center justify-center w-full mb-2"
         style="clip-path:none;text-decoration:none;">
        + 기록 추가
      </a>
      <a href="{{ route('profile.edit') }}"
         class="flex items-center px-4 py-3 font-display text-[11px] tracking-widest uppercase
                transition-colors duration-150"
         style="text-decoration:none;color:rgba(255,255,255,.3);">
        내 정보
      </a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="w-full flex items-center px-4 py-3 font-display text-[11px] tracking-widest uppercase
                       transition-colors duration-150"
                style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.25);">
          로그아웃
        </button>
      </form>
    </div>

  </aside>

</header>
