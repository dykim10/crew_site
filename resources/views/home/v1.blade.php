@php $skinClass = $skinClass ?? '_skin_v1'; @endphp
<!DOCTYPE html>
<html lang="ko" data-theme="v1">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>PAC-RUN CREW</title>
  {{-- 스킨별 CSS 변수 (id 필수 — Ajax 교체용) --}}
  <link id="skin-css" rel="stylesheet" href="{{ asset('css/skin/' . $skinClass . '.css') }}">

  {{-- Swiper (홈 전용 슬라이더) --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

  {{-- PAC-RUN 공용 CSS/JS (Bebas Neue 포함) --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    /* ── 홈 전용 리셋 ── */
    * { margin:0; padding:0; box-sizing:border-box; }
    html { scroll-behavior:smooth; }
    body { background:var(--pac-bg); color:var(--pac-white); font-family:'Barlow','Noto Sans KR',sans-serif; overflow-x:hidden; }

    /* ── HERO ── */
    .hero { min-height:calc(100vh - 72px); display:flex; flex-direction:column; justify-content:center; padding:80px 56px 80px; position:relative; overflow:hidden; }
    .hero-glow { position:absolute; top:20%; right:10%; width:600px; height:600px; background:radial-gradient(circle, rgba(229,173,22,0.07) 0%, transparent 70%); pointer-events:none; }
    .hero-grid-lines { position:absolute; inset:0; background-image:linear-gradient(rgba(255,255,255,0.02) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.02) 1px,transparent 1px); background-size:80px 80px; }
    .hero-eyebrow { font-size:11px; font-weight:700; letter-spacing:5px; text-transform:uppercase; color:var(--pac-yellow); margin-bottom:20px; }
    .hero-title { font-family:'Bebas Neue',sans-serif; font-size:clamp(90px,15vw,220px); line-height:.85; letter-spacing:-3px; position:relative; z-index:1; }
    .hero-title .line2 { -webkit-text-stroke:2px rgba(255,255,255,0.15); color:transparent; }
    .hero-title .accent { color:var(--pac-yellow); }
    .hero-rule { width:80px; height:3px; background:var(--pac-yellow); margin:36px 0; }
    .hero-sub { font-size:16px; font-weight:300; color:rgba(255,255,255,.55); max-width:560px; line-height:1.8; margin-bottom:52px; }
    .hero-cta { display:flex; gap:16px; align-items:center; }
    .hero-stats { display:flex; gap:0; margin-top:80px; border-top:1px solid var(--pac-border); padding-top:40px; }
    .hero-stat { flex:1; padding-right:40px; border-right:1px solid var(--pac-border); margin-right:40px; }
    .hero-stat:last-child { border-right:none; margin-right:0; }
    .hero-stat-num { font-family:'Bebas Neue',sans-serif; font-size:52px; color:var(--pac-yellow); line-height:1; }
    .hero-stat-label { font-size:11px; font-weight:600; letter-spacing:2px; color:rgba(255,255,255,.35); text-transform:uppercase; margin-top:4px; }

    /* ── SECTION COMMON ── */
    section { padding:100px 56px; }

    /* ── PAC 소개 ── */
    .pac-intro-section { background:var(--pac-bg2); }
    .pac-banner { display:grid; grid-template-columns:1fr 1fr; border:1px solid var(--pac-border); overflow:hidden; }
    .pac-image { height:420px; background:linear-gradient(135deg, #1a1212 0%, #3d2800 60%, #1a1212 100%); position:relative; display:flex; align-items:center; justify-content:center; overflow:hidden; }
    .pac-image::before { content:''; position:absolute; inset:0; background:repeating-linear-gradient(45deg, transparent, transparent 30px, rgba(229,173,22,0.03) 30px, rgba(229,173,22,0.03) 31px); }
    .pac-image-big-text { font-family:'Bebas Neue',sans-serif; font-size:100px; letter-spacing:10px; color:rgba(229,173,22,0.12); position:absolute; }
    .pac-image-tag { position:absolute; bottom:24px; left:24px; background:var(--pac-yellow); color:var(--pac-black); font-size:10px; font-weight:700; letter-spacing:3px; text-transform:uppercase; padding:6px 14px; }
    .pac-content { padding:64px; display:flex; flex-direction:column; justify-content:center; }
    .pac-content h2 { font-family:'Bebas Neue',sans-serif; font-size:52px; line-height:1.05; margin-bottom:20px; }
    .pac-content h2 em { color:var(--pac-yellow); font-style:normal; }
    .pac-content p { font-size:15px; line-height:1.9; color:rgba(255,255,255,.55); margin-bottom:16px; font-weight:300; }
    .pac-slogan-list { display:flex; flex-direction:column; gap:10px; margin-top:28px; padding-top:24px; border-top:1px solid var(--pac-border); }
    .pac-slogan-item { font-family:'Bebas Neue',sans-serif; font-size:clamp(16px,2.5vw,22px); letter-spacing:2px; color:var(--pac-yellow); line-height:1.3; }
    .hero-slogan { font-family:'Bebas Neue',sans-serif; font-size:clamp(18px,3vw,26px); letter-spacing:2px; color:var(--pac-yellow); margin-bottom:12px; line-height:1.3; }

    /* ── 지부 ── */
    .branch-section { background:var(--pac-bg); }
    .swiper-branches { overflow:hidden; }
    .branch-card { height:300px; position:relative; overflow:hidden; cursor:pointer; border:1px solid var(--pac-border); transition:border-color .3s; }
    .branch-card:hover { border-color:var(--pac-yellow); }
    .branch-bg { position:absolute; inset:0; transition:transform .6s ease; }
    .branch-bg img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; object-position:center; }
    .branch-card:hover .branch-bg { transform:scale(1.06); }
    .branch-banpo   { background:linear-gradient(160deg, #2d1a00, #0d0d0d); }
    .branch-yonsei  { background:linear-gradient(160deg, #001a2d, #0d0d0d); }
    .branch-gunpo   { background:linear-gradient(160deg, #0d2200, #0d0d0d); }
    .branch-incheon { background:linear-gradient(160deg, #001a2d, #1a001a); }
    .branch-overlay { position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,.9) 0%, transparent 55%); }
    .branch-number { position:absolute; top:20px; right:20px; font-family:'Bebas Neue',sans-serif; font-size:64px; color:rgba(255,255,255,0.05); line-height:1; }
    .branch-content { position:absolute; bottom:0; left:0; right:0; padding:24px; }
    .branch-region-tag { font-size:10px; font-weight:700; letter-spacing:3px; text-transform:uppercase; color:var(--pac-yellow); margin-bottom:8px; }
    .branch-name { font-family:'Bebas Neue',sans-serif; font-size:34px; letter-spacing:2px; margin-bottom:8px; }
    .branch-slogan { font-size:12px; color:rgba(255,255,255,.5); line-height:1.5; }
    .branch-arrow { position:absolute; top:20px; left:20px; width:38px; height:38px; border:1px solid rgba(229,173,22,.4); display:flex; align-items:center; justify-content:center; opacity:0; transition:opacity .3s; font-size:14px; }
    .branch-card:hover .branch-arrow { opacity:1; }

    /* ── 이벤트 ── */
    .event-section { background:var(--pac-bg2); }
    .event-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; }
    .event-card { border:1px solid var(--pac-border); overflow:hidden; cursor:pointer; transition:border-color .3s, transform .3s; }
    .event-card:hover { border-color:var(--pac-yellow); transform:translateY(-6px); }
    .event-thumb { height:200px; position:relative; display:flex; align-items:center; justify-content:center; overflow:hidden; }
    .et-1 { background:linear-gradient(135deg, #2d1a00, #0d0d0d); }
    .et-2 { background:linear-gradient(135deg, #00122d, #0d0d0d); }
    .et-3 { background:linear-gradient(135deg, #2d001a, #0d0d0d); }
    .et-4 { background:linear-gradient(135deg, #001a15, #0d0d0d); }
    .event-thumb::after { content:'EVENT'; font-family:'Bebas Neue',sans-serif; font-size:52px; letter-spacing:8px; color:rgba(255,255,255,0.05); position:absolute; }
    .event-date-tag { position:absolute; top:14px; left:14px; background:var(--pac-yellow); color:var(--pac-black); font-size:10px; font-weight:700; letter-spacing:1px; padding:4px 10px; z-index:1; }
    .event-info { padding:18px 20px 22px; }
    .event-type-tag { font-size:10px; font-weight:700; letter-spacing:3px; text-transform:uppercase; color:var(--pac-yellow); margin-bottom:8px; }
    .event-title { font-size:15px; font-weight:700; margin-bottom:12px; line-height:1.4; }
    .event-meta { font-size:12px; color:rgba(255,255,255,.4); display:flex; flex-direction:column; gap:4px; }
    .event-meta span { display:flex; align-items:center; gap:6px; }

    /* ── 커뮤니티 ── */
    .community-section { background:var(--pac-bg); }
    .community-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .community-card { border:1px solid var(--pac-border); padding:28px 32px; transition:border-color .3s; }
    .community-card:hover { border-color:rgba(229,173,22,.3); }
    .community-card-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; padding-bottom:16px; border-bottom:1px solid var(--pac-border); }
    .community-card-name { font-family:'Bebas Neue',sans-serif; font-size:22px; letter-spacing:2px; }
    .community-card-link { font-size:10px; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:var(--pac-yellow); text-decoration:none; }
    .notice-tabs { display:flex; gap:0; margin-bottom:14px; }
    .ntab { padding:5px 12px; font-size:10px; font-weight:700; letter-spacing:1.5px; cursor:pointer; border:1px solid var(--pac-border); border-right:none; color:rgba(255,255,255,.4); transition:all .2s; }
    .ntab:last-child { border-right:1px solid var(--pac-border); }
    .ntab.on { background:var(--pac-yellow); border-color:var(--pac-yellow); color:var(--pac-black); }
    .post-list { list-style:none; }
    .post-item { display:flex; justify-content:space-between; align-items:center; padding:9px 0; border-bottom:1px solid rgba(255,255,255,.04); cursor:pointer; }
    a.post-item { text-decoration:none; color:inherit; }
    .post-item:last-child { border-bottom:none; }
    .post-item:hover .post-name { color:var(--pac-yellow); }
    .post-name { font-size:13px; color:rgba(255,255,255,.75); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:220px; transition:color .2s; }
    .post-date { font-size:11px; color:rgba(255,255,255,.25); flex-shrink:0; margin-left:10px; }

    /* ── Instagram ── */
    .insta-section { background:var(--pac-bg2); }
    .swiper-insta { overflow:hidden; }
    .insta-card { aspect-ratio:1; position:relative; overflow:hidden; cursor:pointer; }
    .ic-1 { background:linear-gradient(135deg,#3d2800,#E5AD16 250%); }
    .ic-2 { background:linear-gradient(135deg,#1a0028,#E80043 250%); }
    .ic-3 { background:linear-gradient(135deg,#001a2d,#0066ff 250%); }
    .ic-4 { background:linear-gradient(135deg,#001a10,#00cc66 250%); }
    .ic-5 { background:linear-gradient(135deg,#2d0028,#E5AD16 250%); }
    .ic-6 { background:linear-gradient(135deg,#001a2d,#E80043 250%); }
    .insta-hover { position:absolute; inset:0; background:rgba(0,0,0,.55); display:flex; align-items:center; justify-content:center; opacity:0; transition:opacity .3s; flex-direction:column; gap:6px; }
    .insta-card:hover .insta-hover { opacity:1; }
    .insta-hover-icon { font-size:26px; }
    .insta-hover-text { font-size:10px; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:rgba(255,255,255,.8); }
    .insta-watermark { position:absolute; bottom:10px; left:12px; font-size:10px; font-weight:700; letter-spacing:2px; color:rgba(255,255,255,.35); }

    /* ── Footer ── */
    footer { background:#090909; border-top:1px solid var(--pac-border); padding:64px 56px 32px; }
    .footer-main { display:grid; grid-template-columns:2fr 1fr 1fr 1fr; gap:60px; margin-bottom:48px; }
    .footer-brand-name { font-family:'Bebas Neue',sans-serif; font-size:34px; letter-spacing:5px; color:var(--pac-yellow); margin-bottom:12px; }
    .footer-brand-name span { color:white; }
    .footer-desc { font-size:13px; line-height:1.9; color:rgba(255,255,255,.3); font-weight:300; }
    .footer-col-title { font-size:10px; font-weight:700; letter-spacing:3px; text-transform:uppercase; color:var(--pac-yellow); margin-bottom:18px; }
    .footer-links { list-style:none; }
    .footer-links li { margin-bottom:10px; }
    .footer-links a { font-size:13px; color:rgba(255,255,255,.4); text-decoration:none; transition:color .2s; }
    .footer-links a:hover { color:var(--pac-white); }
    .footer-bottom { border-top:1px solid var(--pac-border); padding-top:24px; display:flex; justify-content:space-between; align-items:center; font-size:12px; color:rgba(255,255,255,.2); }
    .footer-bottom a { color:var(--pac-yellow); text-decoration:none; }

    @media (max-width: 767px) {
      section { padding:60px 20px; }

      .hero { min-height:auto; padding:48px 20px 60px; }
      .hero-glow { width:300px; height:300px; right:-50px; }
      .hero-eyebrow { font-size:10px; letter-spacing:3px; line-height:1.6; word-break:keep-all; }
      .hero-title { font-size:clamp(52px,16vw,90px); word-break:keep-all; }
      .hero-sub { max-width:none; margin-bottom:32px; word-break:keep-all; }
      .hero-slogan { word-break:keep-all; margin-bottom:8px; }
      .hero-cta { flex-direction:column; align-items:stretch; width:100%; }
      .hero-cta .pac-btn, .hero-cta .pac-btn-ghost { justify-content:center; text-align:center; }
      .hero-stats { flex-wrap:wrap; margin-top:48px; padding-top:32px; }
      .hero-stat { flex:1 1 50%; min-width:50%; padding:16px 0; margin-right:0; border-right:none; border-bottom:1px solid var(--pac-border); }
      .hero-stat:nth-child(odd) { padding-right:16px; border-right:1px solid var(--pac-border); }
      .hero-stat:nth-child(3), .hero-stat:nth-child(4) { border-bottom:none; }
      .hero-stat-num { font-size:40px; }

      .pac-banner { grid-template-columns:1fr; }
      .pac-image { height:280px; }
      .pac-image-big-text { font-size:60px; letter-spacing:4px; }
      .pac-content { padding:32px 24px; }
      .pac-content h2 { font-size:36px; word-break:keep-all; }
      .pac-content p { word-break:keep-all; }

      .event-grid { grid-template-columns:1fr; }
      .event-title { word-break:keep-all; }

      .community-grid { grid-template-columns:1fr; }
      .community-card-name { word-break:keep-all; }
      .notice-tabs { flex-wrap:wrap; }
      .post-name { max-width:none; flex:1; min-width:0; }

      footer { padding:48px 20px 24px; }
      .footer-main { grid-template-columns:1fr; gap:32px; }
      .footer-desc, .footer-links a { word-break:keep-all; }
      .footer-bottom { flex-direction:column; align-items:flex-start; gap:12px; }
    }
  </style>
</head>
<body class="{{ $skinClass }}">

{{-- GNB (공용 navigation 컴포넌트) --}}
@include('layouts.navigation')

<!-- HERO -->
<section class="hero">
  <div class="hero-glow"></div>
  <div class="hero-grid-lines"></div>
  <div class="hero-eyebrow">High Intensity Interval Training · Partnership Activation Crew · Since 2024</div>
  <div class="hero-title">
    <div>PAC<span class="accent">-</span></div>
    <div>RUN</div>
    <div class="line2">CREW</div>
  </div>
  <div class="hero-rule"></div>
  <p class="hero-slogan">Birds fly. Fish swim. Pac run.</p>
  <p class="hero-sub">TEAM PAC(팀팩)은 풀코스 마라톤 완주를 목표로 트랙에서 인터벌 훈련을 이어가는 러닝 팀입니다.<br><br>에밀 자토팩의 명언을 모티브로 한 "Bird fly, Fish swim, PAC run"처럼, 우리는 언제 어디서나 달리는 '팩'입니다. 함께 달리며 얻은 유대감과 성장이 TEAM PAC의 가장 큰 자부심입니다.</p>
  <div class="hero-cta">
    <a href="{{ route('apply') }}" class="pac-btn">크루 참여하기 →</a>
    <a href="#about" class="pac-btn-ghost">크루 소개</a>
  </div>
  <div class="hero-stats">
    <div class="hero-stat"><div class="hero-stat-num">240+</div><div class="hero-stat-label">Active Runners</div></div>
    <div class="hero-stat"><div class="hero-stat-num">4</div><div class="hero-stat-label">지부</div></div>
    <div class="hero-stat"><div class="hero-stat-num">12</div><div class="hero-stat-label">이벤트</div></div>
    <div class="hero-stat"><div class="hero-stat-num">1,840</div><div class="hero-stat-label">Total KM</div></div>
  </div>
</section>

<!-- PAC 소개 -->
<section class="pac-intro-section" id="about">
  <div class="pac-section-label">About TEAM PAC</div>
  <div class="pac-section-heading">Eat miles, pac run</div>
  <div class="pac-banner">
    <div class="pac-image">
      <div class="pac-image-big-text">PAC</div>
      <div class="pac-image-tag">Birds fly · Fish swim · Pac run</div>
    </div>
    <div class="pac-content">
      <h2>풀코스를 향한<br><em>러닝 팀 TEAM PAC</em></h2>
      <div class="pac-slogan-list">
        <div class="pac-slogan-item">Birds fly. Fish swim. Pac run</div>
        <div class="pac-slogan-item">You want something go get it Period</div>
        <div class="pac-slogan-item">Eat miles, pac run</div>
      </div>
      <a href="{{ route('introduce') }}" class="pac-btn-outline" style="margin-top:20px;">자세히 보기 →</a>
    </div>
  </div>
</section>

{{-- 지부 섹션 v1 --}}
@include('sections.branch-v1')

{{-- 이벤트 섹션 v1 --}}
@include('sections.event-v1')

<!-- 커뮤니티 -->
<section class="community-section">
  <div class="pac-section-label">Community</div>
  <div class="pac-section-heading">커뮤니티</div>
  <div class="community-grid">
    <div class="community-card">
      <div class="community-card-top"><div class="community-card-name">공지사항</div><a href="{{ route('notices.index') }}" class="community-card-link">전체보기</a></div>
      <div class="notice-tabs">
        <div class="ntab on" onclick="selectTab(this)">전체</div>
        <div class="ntab" onclick="selectTab(this)">반포</div>
        <div class="ntab" onclick="selectTab(this)">연대</div>
        <div class="ntab" onclick="selectTab(this)">군포</div>
        <div class="ntab" onclick="selectTab(this)">인천</div>
      </div>
      <ul class="post-list">
        @forelse($notices as $notice)
        <a href="{{ route('notices.show', $notice) }}" class="post-item">
          <span class="post-name">{{ Str::limit($notice->title, 40) }}</span>
          <span class="post-date">{{ $notice->created_at->format('m.d') }}</span>
        </a>
        @empty
        <li class="post-item" style="cursor:default;"><span class="post-name" style="opacity:.5;">등록된 공지가 없습니다</span></li>
        @endforelse
      </ul>
    </div>
    <div class="community-card">
      <div class="community-card-top"><div class="community-card-name">자유게시판</div><a href="{{ route('boards.free') }}" class="community-card-link">전체보기</a></div>
      <ul class="post-list">
        @forelse($freePosts as $post)
        <a href="{{ route('boards.show', ['free', $post]) }}" class="post-item">
          <span class="post-name">{{ Str::limit($post->title, 40) }}</span>
          <span class="post-date">{{ $post->created_at->format('m.d') }}</span>
        </a>
        @empty
        <li class="post-item" style="cursor:default;"><span class="post-name" style="opacity:.5;">등록된 글이 없습니다</span></li>
        @endforelse
      </ul>
    </div>
    <div class="community-card">
      <div class="community-card-top"><div class="community-card-name">포토 갤러리</div><a href="{{ route('photos.index') }}" class="community-card-link">전체보기</a></div>
      <ul class="post-list">
        @forelse($photoGalleries as $gallery)
        <a href="{{ route('photos.show', $gallery) }}" class="post-item">
          <span class="post-name">{{ Str::limit($gallery->title, 40) }}</span>
          <span class="post-date">{{ optional($gallery->taken_at)->format('m.d') ?? $gallery->created_at->format('m.d') }}</span>
        </a>
        @empty
        <li class="post-item" style="cursor:default;"><span class="post-name" style="opacity:.5;">등록된 갤러리가 없습니다</span></li>
        @endforelse
      </ul>
    </div>
    <div class="community-card">
      <div class="community-card-top"><div class="community-card-name">문의게시판</div><a href="{{ route('boards.qna') }}" class="community-card-link">전체보기</a></div>
      <ul class="post-list">
        @forelse($qnaPosts as $post)
        <a href="{{ route('boards.show', ['qna', $post]) }}" class="post-item">
          <span class="post-name">{{ Str::limit($post->title, 40) }}</span>
          <span class="post-date">{{ $post->created_at->format('m.d') }}</span>
        </a>
        @empty
        <li class="post-item" style="cursor:default;"><span class="post-name" style="opacity:.5;">등록된 문의가 없습니다</span></li>
        @endforelse
      </ul>
    </div>
  </div>
</section>

{{-- 인스타그램 섹션 v1 --}}
@include('sections.instagram-v1')

<!-- Footer -->
<footer>
  <div class="footer-main">
    <div><img src="{{ asset('images/logo-footer.webp') }}" alt="PAC RUN" style="height:36px;width:auto;margin-bottom:14px;"><div class="footer-desc">TEAM PAC(팀팩)<br>Birds fly. Fish swim. Pac run.<br>Eat miles, pac run.</div></div>
    <div><div class="footer-col-title">소개</div><ul class="footer-links"><li><a href="{{ route('introduce') }}">PAC-RUN 소개</a></li><li><a href="{{ route('branch') }}">지부 안내</a></li><li><a href="{{ route('administrator') }}">운영진</a></li><li><a href="{{ route('apply') }}">가입 안내</a></li></ul></div>
    <div><div class="footer-col-title">활동</div><ul class="footer-links"><li><a href="{{ route('events.index') }}">이벤트</a></li><li><a href="{{ auth()->check() ? route('running-logs.index') : route('login') }}">기록 관리</a></li><li><a href="{{ route('photos.index') }}">포토 갤러리</a></li><li><a href="{{ route('ranking.index') }}">랭킹</a></li></ul></div>
    <div><div class="footer-col-title">고객지원</div><ul class="footer-links"><li><a href="{{ route('notices.index') }}">공지사항</a></li><li><a href="{{ route('boards.qna') }}">문의하기</a></li><li><a href="{{ route('bug-reports.create') }}">버그 제보</a></li><li><a href="{{ route('privacy') }}">개인정보처리방침</a></li></ul></div>
  </div>
  <div class="footer-bottom">
    <span>© 2026 PAC-RUN. All rights reserved.</span>
    <a href="https://www.instagram.com/pac_run/" target="_blank" rel="noopener noreferrer">Instagram @pac_run</a>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
  new Swiper('.swiper-branches', { slidesPerView:'auto', spaceBetween:12, freeMode:true, grabCursor:true });
  new Swiper('.swiper-insta', { slidesPerView:'auto', spaceBetween:8, freeMode:true, grabCursor:true });
  function selectTab(el) {
    el.closest('.notice-tabs').querySelectorAll('.ntab').forEach(t => t.classList.remove('on'));
    el.classList.add('on');
  }
  function changeSkin(skin) {
    fetch('{{ route("skin.change") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: JSON.stringify({ skin: skin }),
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        // v1→v2 이동 시 페이지 새로고침 (홈 페이지는 완전히 다른 구조)
        window.location.reload();
      }
    })
    .catch(err => console.error('스킨 변경 실패:', err));
  }
</script>
</body>
</html>
