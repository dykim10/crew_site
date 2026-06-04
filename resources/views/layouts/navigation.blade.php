{{-- PAC-RUN Unified GNB — 메인/서브 공용 --}}
@php
  $skinClass   = $skinClass ?? '_skin_v1';
  $authed      = auth()->check();
  $nick        = $authed ? (auth()->user()->nickname ?? auth()->user()->name ?? '?') : '게스트';
  $initial     = mb_strtoupper(mb_substr($nick, 0, 1));
  $role        = $authed ? auth()->user()->role : null;
  $avatarBg    = match($role) {
    'super_admin'  => '#E80043',
    'region_admin' => '#E5AD16',
    'operator'     => '#10b981',
    default        => '#2D2020',
  };
  $avatarTxt   = ($role === 'region_admin') ? '#1A1212' : '#ffffff';
  $roleLabel   = match($role) {
    'super_admin'  => 'SUPER ADMIN',
    'region_admin' => 'REGION ADMIN',
    'operator'     => 'OPERATOR',
    default        => 'MEMBER',
  };
@endphp

<header class="pac-nav" x-data="{ mobileOpen: false, communityOpen: false, userOpen: false }">

  {{-- LOGO --}}
  <a href="{{ route('home') }}" class="pac-nav-logo">
    PAC<span class="logo-suffix">-RUN</span>
  </a>

  {{-- ── Desktop Navigation ── --}}
  <ul class="pac-nav-links hidden lg:flex">

    {{-- PAC 소개 --}}
    <li>
      <a href="{{ route('introduce') }}"
         class="{{ request()->routeIs('introduce') ? 'pac-active' : '' }}">
        PAC
      </a>
    </li>

    {{-- 지부 --}}
    <li>
      <a href="{{ route('branch') }}"
         class="{{ request()->routeIs('branch') ? 'pac-active' : '' }}">
        지부
      </a>
    </li>

    {{-- 이벤트 --}}
    <li>
      <a href="{{ route('events.index') }}"
         class="{{ request()->routeIs('events*') ? 'pac-active' : '' }}">
        이벤트
      </a>
    </li>

    {{-- 커뮤니티 드롭다운 --}}
    <li class="pac-dropdown-wrap"
        @mouseenter="communityOpen = true"
        @mouseleave="communityOpen = false">
      <a href="#"
         onclick="return false"
         class="pac-dropdown-trigger {{ request()->routeIs('notices*', 'boards*') ? 'pac-active' : '' }}">
        커뮤니티
        <svg class="pac-dropdown-caret" width="10" height="6" viewBox="0 0 10 6" fill="none"
             :style="communityOpen ? 'transform:rotate(180deg)' : ''">
          <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </a>
      <div class="pac-dropdown"
           x-show="communityOpen"
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 -translate-y-1"
           x-transition:enter-end="opacity-100 translate-y-0"
           x-transition:leave="transition ease-in duration-100"
           x-transition:leave-start="opacity-100 translate-y-0"
           x-transition:leave-end="opacity-0 -translate-y-1"
           style="display:none;">
        <a href="{{ route('notices.index') }}"
           class="pac-dropdown-item {{ request()->routeIs('notices*') ? 'active' : '' }}">
          <span class="pac-dropdown-label">공지사항</span>
          <span class="pac-dropdown-sub">크루 공지 및 안내</span>
        </a>
        <a href="{{ route('boards.free') }}"
           class="pac-dropdown-item {{ request()->routeIs('boards.free*') ? 'active' : '' }}">
          <span class="pac-dropdown-label">자유게시판</span>
          <span class="pac-dropdown-sub">자유로운 이야기</span>
        </a>
        <a href="{{ route('boards.photo') }}"
           class="pac-dropdown-item {{ request()->routeIs('boards.photo*') ? 'active' : '' }}">
          <span class="pac-dropdown-label">포토 게시판</span>
          <span class="pac-dropdown-sub">러닝 사진 공유</span>
        </a>
        <a href="{{ route('boards.qna') }}"
           class="pac-dropdown-item {{ request()->routeIs('boards.qna*') ? 'active' : '' }}">
          <span class="pac-dropdown-label">문의 게시판</span>
          <span class="pac-dropdown-sub">질문 &amp; 답변</span>
        </a>
      </div>
    </li>

    {{-- 버그제보 --}}
    <li>
      <a href="{{ route('bug-reports.index') }}"
         class="{{ request()->routeIs('bug-reports*') ? 'pac-active' : '' }}">
        버그제보
      </a>
    </li>

  </ul>

  {{-- ── 우측 영역 ── --}}
  <div class="flex items-center gap-2">

    {{-- 스킨 스위처 (로그인 사용자 · 데스크탑) --}}
    @if($authed)
    <div class="skin-switcher hidden lg:flex">
      <button onclick="changeSkin('_skin_v1')"
              data-skin="_skin_v1"
              class="pac-btn-skin {{ $skinClass === '_skin_v1' ? 'active' : '' }}"
              title="V1 Dark Editorial">V1</button>
      <button onclick="changeSkin('_skin_v2')"
              data-skin="_skin_v2"
              class="pac-btn-skin {{ $skinClass === '_skin_v2' ? 'active' : '' }}"
              title="V2 Energy Burst">V2</button>
    </div>
    @endif

    {{-- 사용자 영역 --}}
    @if($authed)

    {{-- 아바타 드롭다운 (데스크탑) --}}
    <div class="relative hidden lg:block"
         @mouseenter="userOpen = true"
         @mouseleave="userOpen = false">
      <button class="pac-user-btn flex items-center gap-2">
        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"
             style="background:{{ $avatarBg }};">
          <span class="font-display text-sm leading-none"
                style="color:{{ $avatarTxt }};">{{ $initial }}</span>
        </div>
        <span class="font-body text-xs text-pac-black-400 max-w-[80px] truncate hidden xl:block">
          {{ $nick }}
        </span>
        <svg class="pac-dropdown-caret" width="10" height="6" viewBox="0 0 10 6" fill="none"
             :style="userOpen ? 'transform:rotate(180deg)' : ''">
          <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>

      <div class="pac-dropdown pac-user-dropdown"
           x-show="userOpen"
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 -translate-y-1"
           x-transition:enter-end="opacity-100 translate-y-0"
           x-transition:leave="transition ease-in duration-100"
           x-transition:leave-start="opacity-100 translate-y-0"
           x-transition:leave-end="opacity-0 -translate-y-1"
           style="display:none;">
        <a href="{{ route('dashboard') }}"
           class="pac-dropdown-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
          <span class="pac-dropdown-label">마이페이지</span>
          <span class="pac-dropdown-sub">기록 요약 · 이벤트</span>
        </a>
        <a href="{{ route('running-logs.index') }}"
           class="pac-dropdown-item {{ request()->routeIs('running-logs*') ? 'active' : '' }}">
          <span class="pac-dropdown-label">기록</span>
          <span class="pac-dropdown-sub">러닝 기록 관리</span>
        </a>
        <a href="{{ route('photos.index') }}"
           class="pac-dropdown-item {{ request()->routeIs('photos*') ? 'active' : '' }}">
          <span class="pac-dropdown-label">포토</span>
          <span class="pac-dropdown-sub">갤러리</span>
        </a>
        <a href="{{ route('ranking.index') }}"
           class="pac-dropdown-item {{ request()->routeIs('ranking*') ? 'active' : '' }}">
          <span class="pac-dropdown-label">순위</span>
          <span class="pac-dropdown-sub">마일리지 랭킹</span>
        </a>
        <div class="pac-dropdown-divider"></div>
        <a href="{{ route('profile.edit') }}"
           class="pac-dropdown-item {{ request()->routeIs('profile*') ? 'active' : '' }}">
          <span class="pac-dropdown-label">내 정보</span>
        </a>
        <div class="pac-dropdown-item pac-dropdown-logout">
          <form method="POST" action="{{ route('logout') }}" style="width:100%;">
            @csrf
            <button type="submit" style="background:none;border:none;cursor:pointer;width:100%;text-align:left;padding:0;">
              <span class="pac-dropdown-label" style="color:rgba(255,255,255,.4);">로그아웃</span>
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- 기록 추가 CTA (데스크탑) --}}
    <a href="{{ route('running-logs.create') }}" class="pac-nav-cta hidden md:inline-flex">
      + 기록
    </a>

    @else

    {{-- 비로그인: 로그인 버튼 --}}
    <a href="{{ route('login') }}" class="pac-nav-cta hidden md:inline-flex">
      로그인
    </a>

    @endif

    {{-- 햄버거 (모바일) --}}
    <button @click="mobileOpen = !mobileOpen"
            class="lg:hidden p-1.5 text-pac-black-400 hover:text-white transition-colors">
      <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
      <svg x-show="mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>

  </div>

  {{-- ── 모바일 오버레이 + 드로어
       x-teleport="body": backdrop-filter stacking context 탈출 → 뷰포트 기준 fixed 정상 작동
       Alpine scope(mobileOpen 등)는 부모 x-data에서 유지됨 ── --}}
  <template x-teleport="body">
  <div>

  {{-- 오버레이 --}}
  <div x-show="mobileOpen"
       @click="mobileOpen = false"
       x-transition:enter="transition-opacity duration-200"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition-opacity duration-200"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       class="fixed inset-0 bg-black/70 lg:hidden"
       style="z-index:9040;display:none;"></div>

  {{-- 드로어 --}}
  <aside x-show="mobileOpen"
         x-transition:enter="transition-transform duration-250 ease-out"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition-transform duration-200 ease-in"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed top-0 right-0 h-full w-72 lg:hidden flex flex-col"
         style="background:#0D0D0D;border-left:1px solid rgba(255,255,255,0.06);z-index:9050;display:none;">

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
          <p class="font-display text-[11px] tracking-widest uppercase"
             style="color:rgba(255,255,255,.3);">{{ $roleLabel }}</p>
        </div>
      </div>
      <button @click="mobileOpen = false"
              class="p-1 text-pac-black-500 hover:text-white transition-colors"
              style="background:none;border:none;cursor:pointer;">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    {{-- 드로어 네비 --}}
    <nav class="flex-1 px-2 py-3 overflow-y-auto">

      {{-- 메인 메뉴 --}}
      @php
        $mobileNav = [
          ['route' => 'introduce',         'label' => 'PAC 소개',  'match' => 'introduce'],
          ['route' => 'branch',            'label' => '지부',      'match' => 'branch'],
          ['route' => 'events.index',      'label' => '이벤트',    'match' => 'events*'],
          ['route' => 'bug-reports.index', 'label' => '버그제보',  'match' => 'bug-reports*'],
        ];
      @endphp
      @foreach($mobileNav as $item)
        @php $ia = request()->routeIs($item['match']); @endphp
        <a href="{{ route($item['route']) }}"
           class="flex items-center px-4 py-3.5 mb-0.5 font-display text-[13px] tracking-widest uppercase"
           style="text-decoration:none;{{ $ia ? 'color:#E5AD16;border-left:2px solid #E5AD16;background:rgba(229,173,22,0.06);' : 'color:rgba(255,255,255,.40);border-left:2px solid transparent;' }}">
          {{ $item['label'] }}
        </a>
      @endforeach

      {{-- 커뮤니티 아코디언 --}}
      <div x-data="{ communityMobile: {{ request()->routeIs('notices*','boards*') ? 'true' : 'false' }} }">
        <button @click="communityMobile = !communityMobile"
                class="w-full flex items-center justify-between px-4 py-3.5 mb-0.5 font-display text-[13px] tracking-widest uppercase"
                style="background:none;border:none;cursor:pointer;border-left:2px solid transparent;
                       {{ request()->routeIs('notices*','boards*') ? 'color:#E5AD16;border-left-color:#E5AD16;background:rgba(229,173,22,0.06);' : 'color:rgba(255,255,255,.40);' }}">
          <span>커뮤니티</span>
          <svg :class="communityMobile ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200"
               fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <div x-show="communityMobile"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             style="display:none;">
          @php
            $communityItems = [
              ['route' => 'notices.index', 'label' => '공지사항',  'match' => 'notices*'],
              ['route' => 'boards.free',   'label' => '자유게시판','match' => 'boards.free*'],
              ['route' => 'boards.photo',  'label' => '포토 게시판','match' => 'boards.photo*'],
              ['route' => 'boards.qna',    'label' => '문의 게시판','match' => 'boards.qna*'],
            ];
          @endphp
          @foreach($communityItems as $ci)
            <a href="{{ route($ci['route']) }}"
               class="flex items-center pl-10 pr-4 py-2.5 font-body text-[12px]"
               style="text-decoration:none;{{ request()->routeIs($ci['match']) ? 'color:#E5AD16;' : 'color:rgba(255,255,255,.35);' }}">
              {{ $ci['label'] }}
            </a>
          @endforeach
        </div>
      </div>

      {{-- 로그인 사용자 — 내 메뉴 --}}
      @if($authed)
      <div class="mt-4 pt-4" style="border-top:1px solid rgba(255,255,255,0.06);">
        <p class="px-4 mb-1 font-display text-[10px] tracking-widest uppercase"
           style="color:rgba(255,255,255,.2);">MY MENU</p>
        @php
          $myNav = [
            ['route' => 'dashboard',          'label' => '마이페이지', 'match' => 'dashboard'],
            ['route' => 'running-logs.index', 'label' => '기록',      'match' => 'running-logs*'],
            ['route' => 'photos.index',       'label' => '포토',      'match' => 'photos*'],
            ['route' => 'ranking.index',      'label' => '순위',      'match' => 'ranking*'],
          ];
        @endphp
        @foreach($myNav as $item)
          @php $ia = request()->routeIs($item['match']); @endphp
          <a href="{{ route($item['route']) }}"
             class="flex items-center px-4 py-3 font-display text-[12px] tracking-widest uppercase"
             style="text-decoration:none;{{ $ia ? 'color:#E5AD16;' : 'color:rgba(255,255,255,.28);' }}">
            {{ $item['label'] }}
          </a>
        @endforeach
      </div>
      @endif

    </nav>

    {{-- 드로어 하단 --}}
    <div class="px-2 py-3" style="border-top:1px solid rgba(255,255,255,0.06);">

      {{-- 스킨 스위처 (로그인 사용자) --}}
      @if($authed)
      <div class="flex items-center gap-2 px-4 py-2 mb-2">
        <span class="font-display text-[10px] tracking-widest uppercase"
              style="color:rgba(255,255,255,.3);">SKIN</span>
        <button onclick="changeSkin('_skin_v1')" data-skin="_skin_v1"
                class="pac-btn-skin {{ $skinClass === '_skin_v1' ? 'active' : '' }}">V1</button>
        <button onclick="changeSkin('_skin_v2')" data-skin="_skin_v2"
                class="pac-btn-skin {{ $skinClass === '_skin_v2' ? 'active' : '' }}">V2</button>
      </div>

      <a href="{{ route('running-logs.create') }}"
         class="pac-nav-cta flex items-center justify-center w-full mb-2"
         style="clip-path:none;text-decoration:none;">
        + 기록 추가
      </a>
      <a href="{{ route('profile.edit') }}"
         class="flex items-center px-4 py-3 font-display text-[13px] tracking-widest uppercase"
         style="text-decoration:none;color:rgba(255,255,255,.3);">
        내 정보
      </a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="w-full flex items-center px-4 py-3 font-display text-[13px] tracking-widest uppercase"
                style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.22);">
          로그아웃
        </button>
      </form>

      @else

      <a href="{{ route('login') }}"
         class="pac-nav-cta flex items-center justify-center w-full"
         style="clip-path:none;text-decoration:none;">
        로그인
      </a>

      @endif
    </div>

  </aside>

  </div>
  </template>

</header>
