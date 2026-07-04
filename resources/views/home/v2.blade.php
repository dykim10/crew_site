@php $skinClass = $skinClass ?? '_skin_v2'; @endphp
<!DOCTYPE html>
<html lang="ko" data-theme="v2">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>PAC-RUN CREW</title>
  {{-- 스킨별 CSS 변수 (id 필수 — Ajax 교체용) --}}
  <link id="skin-css" rel="stylesheet" href="{{ asset('css/skin/' . $skinClass . '.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
  {{-- Bebas Neue (공용 GNB), Anton + Barlow Condensed (v2 홈 전용) --}}
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Anton&family=Barlow+Condensed:wght@300;400;600;700;900&family=Noto+Sans+KR:wght@300;400;500;700;900&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    :root { --yellow:#E5AD16; --black:#1A1212; --pink:#E80043; --white:#FFFFFF; --light:#F5F3EE; --mid:#E8E5DF; }
    * { margin:0; padding:0; box-sizing:border-box; }
    html { scroll-behavior:smooth; }
    body { background:var(--light); color:var(--black); font-family:'Barlow Condensed','Noto Sans KR',sans-serif; overflow-x:hidden; }

    /* HERO */
    .hero { min-height:calc(100vh - 72px); position:relative; overflow:hidden; background:var(--yellow); display:flex; flex-direction:column; justify-content:flex-end; padding:0 56px 0; padding-top:40px; }
    .hero-diagonal { position:absolute; bottom:0; right:0; width:55%; height:100%; background:var(--black); clip-path:polygon(15% 0, 100% 0, 100% 100%, 0% 100%); }
    .hero-diagonal-inner { position:absolute; bottom:0; right:0; width:55%; height:100%; clip-path:polygon(15% 0, 100% 0, 100% 100%, 0% 100%); display:flex; align-items:center; justify-content:center; }
    .hero-diagonal-text { font-family:'Anton',sans-serif; font-size:clamp(60px,10vw,140px); letter-spacing:5px; color:rgba(229,173,22,0.08); writing-mode:vertical-rl; }
    .hero-eyebrow { font-size:12px; font-weight:700; letter-spacing:6px; text-transform:uppercase; color:var(--black); opacity:.7; margin-bottom:16px; position:relative; z-index:1; }
    .hero-title { font-family:'Anton',sans-serif; font-size:clamp(80px,16vw,240px); line-height:.82; letter-spacing:-4px; position:relative; z-index:1; color:var(--black); }
    .hero-title .white { color:var(--white); }
    .hero-bottom { display:flex; justify-content:space-between; align-items:flex-end; margin-top:48px; position:relative; z-index:1; }
    .hero-sub { font-size:16px; font-weight:400; color:rgba(26,18,18,.7); max-width:400px; line-height:1.7; }
    .hero-cta-area { display:flex; flex-direction:column; align-items:flex-end; gap:16px; }
    .btn-black { background:var(--black); color:var(--yellow); padding:16px 40px; font-family:'Anton',sans-serif; font-size:18px; letter-spacing:3px; text-transform:uppercase; text-decoration:none; display:inline-block; }
    .hero-scroll-hint { font-size:11px; font-weight:700; letter-spacing:3px; text-transform:uppercase; color:rgba(26,18,18,.5); }
    .hero-stats-bar { display:flex; background:var(--black); position:relative; z-index:1; margin-left:-56px; margin-right:-56px; }
    .hero-stat-item { flex:1; padding:28px 40px; border-right:1px solid rgba(255,255,255,.08); }
    .hero-stat-item:last-child { border-right:none; }
    .hero-stat-num { font-family:'Anton',sans-serif; font-size:48px; color:var(--yellow); line-height:1; }
    .hero-stat-label { font-size:11px; font-weight:700; letter-spacing:2px; color:rgba(255,255,255,.4); text-transform:uppercase; margin-top:4px; }

    /* SECTIONS */
    section { padding:100px 56px; }
    .section-eyebrow { font-size:12px; font-weight:700; letter-spacing:5px; text-transform:uppercase; color:var(--pink); margin-bottom:12px; }
    .section-heading { font-family:'Anton',sans-serif; font-size:clamp(48px,6vw,88px); letter-spacing:0; line-height:.9; margin-bottom:56px; color:var(--black); }
    .section-heading em { color:var(--yellow); font-style:normal; }

    /* PAC 소개 */
    .pac-section { background:var(--white); }
    .pac-banner { display:grid; grid-template-columns:1fr 1fr; border:3px solid var(--black); overflow:hidden; }
    .pac-image { min-height:460px; background:var(--black); position:relative; overflow:hidden; display:flex; align-items:center; justify-content:center; }
    .pac-image-pattern { position:absolute; inset:0; background-image:repeating-linear-gradient(0deg, transparent, transparent 48px, rgba(229,173,22,.08) 48px, rgba(229,173,22,.08) 49px), repeating-linear-gradient(90deg, transparent, transparent 48px, rgba(229,173,22,.08) 48px, rgba(229,173,22,.08) 49px); }
    .pac-image-big { font-family:'Anton',sans-serif; font-size:110px; letter-spacing:8px; color:rgba(229,173,22,.15); position:absolute; }
    .pac-image-badge { position:absolute; bottom:0; left:0; right:0; background:var(--yellow); color:var(--black); padding:14px 28px; font-family:'Anton',sans-serif; font-size:20px; letter-spacing:3px; }
    .pac-content { padding:64px 56px; background:var(--light); display:flex; flex-direction:column; justify-content:center; }
    .pac-content-tag { display:inline-block; background:var(--pink); color:var(--white); font-size:10px; font-weight:700; letter-spacing:3px; text-transform:uppercase; padding:4px 12px; margin-bottom:20px; }
    .pac-content h2 { font-family:'Anton',sans-serif; font-size:48px; letter-spacing:1px; line-height:1.05; margin-bottom:20px; }
    .pac-content p { font-size:15px; color:rgba(26,18,18,.65); line-height:1.85; margin-bottom:10px; }
    .pac-content-more { display:inline-flex; align-items:center; gap:10px; margin-top:28px; font-size:13px; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:var(--black); text-decoration:none; border-bottom:2px solid var(--yellow); padding-bottom:2px; }

    /* 지부 */
    .branch-section { background:var(--light); }
    .swiper-branches { overflow:hidden; }
    .branch-card { border:2px solid var(--black); overflow:hidden; position:relative; cursor:pointer; height:320px; transition:transform .3s; }
    .branch-card:hover { transform:translateY(-8px); }
    .branch-top { padding:20px 24px; position:relative; overflow:hidden; height:200px; display:flex; flex-direction:column; justify-content:flex-end; }
    .branch-top-img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; object-position:center; }
    .branch-top-overlay { position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,.88) 0%, rgba(0,0,0,.2) 55%); }
    .branch-top-num { position:absolute; top:-10px; right:10px; font-family:'Anton',sans-serif; font-size:88px; line-height:1; color:rgba(255,255,255,.05); z-index:2; }
    .branch-top-region { font-size:10px; font-weight:700; letter-spacing:4px; text-transform:uppercase; color:var(--yellow); margin-bottom:6px; position:relative; z-index:2; }
    .branch-top-name { font-family:'Anton',sans-serif; font-size:36px; letter-spacing:2px; color:var(--white); position:relative; z-index:2; }
    .branch-bottom { padding:18px 24px; background:var(--white); border-top:2px solid var(--black); height:120px; display:flex; flex-direction:column; justify-content:space-between; }
    .branch-slogan { font-size:13px; color:rgba(26,18,18,.6); line-height:1.5; }
    .branch-arrow-btn { display:inline-flex; align-items:center; gap:6px; font-size:11px; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:var(--black); text-decoration:none; }
    .branch-arrow-btn::after { content:'→'; font-size:14px; }
    .branch-banpo-color { background:linear-gradient(160deg, #3d2800 0%, var(--black) 100%); }
    .branch-yonsei-color { background:linear-gradient(160deg, #001838 0%, var(--black) 100%); }
    .branch-gunpo-color { background:linear-gradient(160deg, #0d2500 0%, var(--black) 100%); }
    .branch-incheon-color { background:linear-gradient(160deg, #00122d 0%, #1a001a 100%); }

    /* 이벤트 */
    .event-section { background:var(--black); }
    .event-section .section-heading { color:var(--white); }
    .event-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:0; border:1px solid rgba(255,255,255,.1); }
    .event-card { border-right:1px solid rgba(255,255,255,.1); overflow:hidden; cursor:pointer; transition:background .3s; }
    .event-card:last-child { border-right:none; }
    .event-card:hover { background:rgba(229,173,22,.05); }
    .event-thumb { height:220px; position:relative; overflow:hidden; }
    .et-1 { background:linear-gradient(160deg,#3d2800,#1a1212); }
    .et-2 { background:linear-gradient(160deg,#001838,#1a1212); }
    .et-3 { background:linear-gradient(160deg,#2d0028,#1a1212); }
    .et-4 { background:linear-gradient(160deg,#002d1a,#1a1212); }
    .event-thumb-num { position:absolute; bottom:-10px; right:10px; font-family:'Anton',sans-serif; font-size:80px; color:rgba(255,255,255,.05); line-height:1; }
    .event-date-badge { position:absolute; top:0; left:0; background:var(--yellow); color:var(--black); font-family:'Anton',sans-serif; font-size:13px; letter-spacing:1px; padding:6px 14px; }
    .event-body { padding:24px 20px; border-top:1px solid rgba(255,255,255,.08); }
    .event-region-tag { font-size:10px; font-weight:700; letter-spacing:3px; text-transform:uppercase; color:var(--pink); margin-bottom:10px; }
    .event-title { font-family:'Anton',sans-serif; font-size:22px; letter-spacing:.5px; color:var(--white); margin-bottom:14px; line-height:1.2; }
    .event-meta-line { font-size:12px; color:rgba(255,255,255,.4); margin-bottom:4px; }
    .event-link { display:block; margin-top:18px; font-size:11px; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:var(--yellow); text-decoration:none; }

    /* 커뮤니티 */
    .community-section { background:var(--light); }
    .community-grid { display:grid; grid-template-columns:1fr 1fr; gap:0; border:2px solid var(--black); }
    .community-card { padding:32px 36px; border-right:2px solid var(--black); border-bottom:2px solid var(--black); }
    .community-card:nth-child(2) { border-right:none; }
    .community-card:nth-child(3) { border-bottom:none; }
    .community-card:nth-child(4) { border-right:none; border-bottom:none; }
    .community-card-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; padding-bottom:16px; border-bottom:2px solid var(--black); }
    .community-card-name { font-family:'Anton',sans-serif; font-size:24px; letter-spacing:1px; }
    .community-card-more { font-size:11px; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:var(--pink); text-decoration:none; }
    .notice-tabs { display:flex; gap:0; margin-bottom:14px; }
    .ntab { padding:5px 11px; font-size:10px; font-weight:700; letter-spacing:1.5px; cursor:pointer; border:1px solid var(--black); border-right:none; color:rgba(26,18,18,.5); transition:all .2s; }
    .ntab:last-child { border-right:1px solid var(--black); }
    .ntab.on { background:var(--black); border-color:var(--black); color:var(--yellow); }
    .post-list { list-style:none; }
    .post-item { display:flex; justify-content:space-between; align-items:center; padding:9px 0; border-bottom:1px solid rgba(26,18,18,.08); cursor:pointer; }
    a.post-item { text-decoration:none; color:inherit; }
    .post-item:last-child { border-bottom:none; }
    .post-item:hover .post-name { color:var(--pink); }
    .post-name { font-size:14px; color:rgba(26,18,18,.8); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:220px; transition:color .2s; }
    .post-date { font-size:11px; color:rgba(26,18,18,.35); flex-shrink:0; margin-left:10px; font-weight:600; }

    /* Instagram */
    .insta-section { background:var(--white); }
    .swiper-insta { overflow:hidden; }
    .insta-card { aspect-ratio:1; position:relative; overflow:hidden; cursor:pointer; border:2px solid var(--black); }
    .ic-1 { background:linear-gradient(135deg,var(--yellow) 0%,#ff6b00 100%); }
    .ic-2 { background:linear-gradient(135deg,var(--pink) 0%,#6600cc 100%); }
    .ic-3 { background:linear-gradient(135deg,#0066ff 0%,#00ccff 100%); }
    .ic-4 { background:linear-gradient(135deg,#00aa44 0%,#006622 100%); }
    .ic-5 { background:linear-gradient(135deg,#cc6600 0%,var(--yellow) 100%); }
    .ic-6 { background:linear-gradient(135deg,#6600aa 0%,var(--pink) 100%); }
    .insta-inner { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; }
    .insta-icon-big { font-family:'Anton',sans-serif; font-size:48px; letter-spacing:4px; color:rgba(255,255,255,.2); }
    .insta-hover { position:absolute; inset:0; background:rgba(26,18,18,.7); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px; opacity:0; transition:opacity .3s; }
    .insta-card:hover .insta-hover { opacity:1; }
    .insta-hover-label { font-size:12px; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:var(--white); }
    .insta-tag { position:absolute; bottom:10px; left:12px; font-size:10px; font-weight:700; letter-spacing:2px; color:rgba(255,255,255,.6); }

    /* Footer */
    footer { background:var(--black); padding:72px 56px 32px; }
    .footer-main { display:grid; grid-template-columns:2fr 1fr 1fr 1fr; gap:60px; padding-bottom:48px; border-bottom:1px solid rgba(255,255,255,.1); margin-bottom:28px; }
    .footer-logo { font-family:'Anton',sans-serif; font-size:32px; letter-spacing:4px; color:var(--yellow); margin-bottom:12px; }
    .footer-desc { font-size:13px; color:rgba(255,255,255,.35); line-height:1.9; }
    .footer-col-title { font-size:10px; font-weight:700; letter-spacing:3px; text-transform:uppercase; color:var(--yellow); margin-bottom:18px; }
    .footer-links { list-style:none; }
    .footer-links li { margin-bottom:10px; }
    .footer-links a { font-size:13px; color:rgba(255,255,255,.45); text-decoration:none; transition:color .2s; }
    .footer-links a:hover { color:var(--white); }
    .footer-bottom { display:flex; justify-content:space-between; align-items:center; font-size:12px; color:rgba(255,255,255,.25); }
    .footer-insta { color:var(--yellow); text-decoration:none; font-size:12px; font-weight:700; }

    @media (max-width: 767px) {
      section { padding:60px 20px; }
      .section-heading { margin-bottom:32px; word-break:keep-all; }
      .section-eyebrow { letter-spacing:3px; }

      .hero { min-height:auto; padding:32px 20px 0; }
      .hero-diagonal { display:none; }
      .hero-eyebrow { font-size:9px; letter-spacing:3px; line-height:1.6; word-break:keep-all; }
      .hero-title { font-size:clamp(48px,18vw,72px); letter-spacing:-2px; word-break:keep-all; }
      .hero-bottom { flex-direction:column; align-items:flex-start; gap:24px; margin-top:32px; }
      .hero-sub { max-width:none; word-break:keep-all; }
      .hero-cta-area { align-items:stretch; width:100%; }
      .btn-black { width:100%; text-align:center; padding:14px 28px; font-size:16px; }
      .hero-stats-bar { flex-wrap:wrap; margin-left:-20px; margin-right:-20px; }
      .hero-stat-item { flex:1 1 50%; min-width:50%; padding:20px 16px; border-right:none; border-bottom:1px solid rgba(255,255,255,.08); }
      .hero-stat-item:nth-child(odd) { border-right:1px solid rgba(255,255,255,.08); }
      .hero-stat-item:nth-child(3), .hero-stat-item:nth-child(4) { border-bottom:none; }
      .hero-stat-num { font-size:36px; }

      .pac-banner { grid-template-columns:1fr; }
      .pac-image { min-height:280px; }
      .pac-image-big { font-size:64px; letter-spacing:4px; }
      .pac-content { padding:32px 24px; }
      .pac-content h2 { font-size:32px; word-break:keep-all; }
      .pac-content p { word-break:keep-all; }

      .event-grid { grid-template-columns:1fr; }
      .event-card { border-right:none; border-bottom:1px solid rgba(255,255,255,.1); }
      .event-card:last-child { border-bottom:none; }
      .event-title { word-break:keep-all; }

      .community-grid { grid-template-columns:1fr; }
      .community-card { border-right:none !important; border-bottom:2px solid var(--black); }
      .community-card:last-child { border-bottom:none; }
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
  <div class="hero-diagonal">
    <div class="hero-diagonal-text">RUNNING</div>
  </div>
  <div class="hero-eyebrow">High Intensity Interval Training · Partnership Activation Crew · Since 2024</div>
  <div class="hero-title">
    <div>PAC<span class="white">-</span></div>
    <div><span class="white">RUN</span></div>
    <div>CREW</div>
  </div>
  <div class="hero-bottom">
    <div class="hero-sub">함께 달리고, 함께 성장하는<br>서울 & 수도권 러닝 크루.<br>반포 · 연대 · 군포 · 인천</div>
    <div class="hero-cta-area">
      <a href="{{ route('apply') }}" class="btn-black">크루 합류하기 →</a>
      <div class="hero-scroll-hint">SCROLL TO EXPLORE ↓</div>
    </div>
  </div>
  <div class="hero-stats-bar">
    <div class="hero-stat-item"><div class="hero-stat-num">240+</div><div class="hero-stat-label">Active Runners</div></div>
    <div class="hero-stat-item"><div class="hero-stat-num">4</div><div class="hero-stat-label">지부</div></div>
    <div class="hero-stat-item"><div class="hero-stat-num">12</div><div class="hero-stat-label">이벤트</div></div>
    <div class="hero-stat-item"><div class="hero-stat-num">1,840</div><div class="hero-stat-label">Total KM</div></div>
  </div>
</section>

<!-- PAC 소개 -->
<section class="pac-section" id="about">
  <div class="section-eyebrow">About PAC-RUN</div>
  <div class="section-heading">우리는 왜<br>함께 <em>달리나</em></div>
  <div class="pac-banner">
    <div class="pac-image">
      <div class="pac-image-pattern"></div>
      <div class="pac-image-big">PAC</div>
      <div class="pac-image-badge">PAC-RUN CREW 2024</div>
    </div>
    <div class="pac-content">
      <div class="pac-content-tag">Official Statement</div>
      <h2>달리기로 하나 되는<br>러닝 크루</h2>
      <p>PAC-RUN은 서울과 수도권을 기반으로 활동하는 러닝 크루입니다. 반포, 연대, 군포, 인천 4개 지부가 각 지역의 러너들을 연결합니다.</p>
      <p>매월 정기 런, 특별 이벤트, 지부 간 교류를 통해 혼자가 아닌 함께 달리는 즐거움을 경험하세요.</p>
      <a href="{{ route('introduce') }}" class="pac-content-more">자세히 보기 →</a>
    </div>
  </div>
</section>

{{-- 지부 섹션 v2 --}}
@include('sections.branch-v2')

{{-- 이벤트 섹션 v2 --}}
@include('sections.event-v2')

<!-- 커뮤니티 -->
<section class="community-section">
  <div class="section-eyebrow">Community</div>
  <div class="section-heading">커뮤니티</div>
  <div class="community-grid">
    <div class="community-card">
      <div class="community-card-head"><div class="community-card-name">공지사항</div><a href="{{ route('notices.index') }}" class="community-card-more">전체보기</a></div>
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
      <div class="community-card-head"><div class="community-card-name">자유게시판</div><a href="{{ route('boards.free') }}" class="community-card-more">전체보기</a></div>
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
      <div class="community-card-head"><div class="community-card-name">포토 갤러리</div><a href="{{ route('photos.index') }}" class="community-card-more">전체보기</a></div>
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
      <div class="community-card-head"><div class="community-card-name">문의게시판</div><a href="{{ route('boards.qna') }}" class="community-card-more">전체보기</a></div>
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

{{-- 인스타그램 섹션 v2 --}}
@include('sections.instagram-v2')

<!-- Footer -->
<footer>
  <div class="footer-main">
    <div><img src="{{ asset('images/logo-footer.webp') }}" alt="PAC RUN" style="height:36px;width:auto;margin-bottom:14px;"><div class="footer-desc">함께 달리고, 함께 성장하는<br>서울 러닝 크루 PAC-RUN.<br>반포 · 연대 · 군포 · 인천</div></div>
    <div><div class="footer-col-title">소개</div><ul class="footer-links"><li><a href="{{ route('introduce') }}">PAC-RUN 소개</a></li><li><a href="{{ route('branch') }}">지부 안내</a></li><li><a href="{{ route('introduce') }}">운영진</a></li><li><a href="{{ route('apply') }}">가입 안내</a></li></ul></div>
    <div><div class="footer-col-title">활동</div><ul class="footer-links"><li><a href="{{ route('events.index') }}">이벤트</a></li><li><a href="{{ auth()->check() ? route('running-logs.index') : route('login') }}">기록 관리</a></li><li><a href="{{ route('photos.index') }}">포토 갤러리</a></li><li><a href="{{ route('ranking.index') }}">랭킹</a></li></ul></div>
    <div><div class="footer-col-title">고객지원</div><ul class="footer-links"><li><a href="{{ route('notices.index') }}">공지사항</a></li><li><a href="{{ route('boards.qna') }}">문의하기</a></li><li><a href="{{ route('bug-reports.create') }}">버그 제보</a></li><li><a href="{{ route('apply') }}">개인정보처리방침</a></li></ul></div>
  </div>
  <div class="footer-bottom">
    <span>© 2026 PAC-RUN. All rights reserved.</span>
    <a href="https://www.instagram.com/pac_run/" class="footer-insta" target="_blank" rel="noopener noreferrer">@pac_run</a>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
  new Swiper('.swiper-branches', { slidesPerView:'auto', spaceBetween:0, freeMode:true, grabCursor:true });
  new Swiper('.swiper-insta', { slidesPerView:'auto', spaceBetween:0, freeMode:true, grabCursor:true });
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
        // v1↔v2 전환 시 페이지 새로고침 (홈 페이지는 완전히 다른 구조)
        window.location.reload();
      }
    })
    .catch(err => console.error('스킨 변경 실패:', err));
  }
</script>
</body>
</html>
