<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PAC-RUN CREW</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Barlow+Condensed:wght@300;400;600;700;900&family=Noto+Sans+KR:wght@300;400;500;700;900&display=swap" rel="stylesheet">
  <style>
    :root { --yellow:#E5AD16; --black:#1A1212; --pink:#E80043; --white:#FFFFFF; --light:#F5F3EE; --mid:#E8E5DF; }
    * { margin:0; padding:0; box-sizing:border-box; }
    html { scroll-behavior:smooth; }
    body { background:var(--light); color:var(--black); font-family:'Barlow Condensed','Noto Sans KR',sans-serif; overflow-x:hidden; }

    /* ===== 어드민 테마 스위처 바 ===== */
    .admin-theme-bar {
      position:fixed; top:0; left:0; right:0; z-index:200;
      background:var(--yellow); border-bottom:2px solid var(--black);
      display:flex; align-items:center; justify-content:space-between;
      padding:0 56px; height:40px; font-size:11px;
    }
    .admin-theme-bar-label { color:var(--black); letter-spacing:2px; text-transform:uppercase; font-weight:700; }
    .theme-switch-btns { display:flex; gap:6px; align-items:center; }
    .theme-btn { padding:4px 16px; font-size:10px; font-weight:700; letter-spacing:2px; text-transform:uppercase; border:2px solid var(--black); background:transparent; color:var(--black); cursor:pointer; transition:all .2s; font-family:'Barlow Condensed',sans-serif; }
    .theme-btn.active { background:var(--black); color:var(--yellow); }
    .theme-btn:not(.active):hover { background:rgba(26,18,18,.1); }
    .admin-bar-link { color:rgba(26,18,18,.6); text-decoration:none; font-size:10px; letter-spacing:1px; margin-left:20px; font-weight:700; }
    .admin-bar-link:hover { color:var(--black); }

    /* NAV */
    .has-admin-bar nav { top:40px; }
    nav { position:fixed; top:0; left:0; right:0; z-index:100; display:flex; align-items:center; justify-content:space-between; padding:0 56px; height:64px; background:var(--black); }
    .nav-logo { font-family:'Anton',sans-serif; font-size:22px; letter-spacing:3px; color:var(--yellow); text-decoration:none; display:flex; align-items:center; gap:6px; }
    .nav-logo-dot { width:8px; height:8px; background:var(--pink); border-radius:50%; }
    .nav-links { display:flex; gap:32px; list-style:none; }
    .nav-links a { color:rgba(255,255,255,.6); text-decoration:none; font-size:13px; font-weight:700; letter-spacing:2px; text-transform:uppercase; transition:color .2s; }
    .nav-links a:hover { color:var(--yellow); }
    .nav-btn { background:var(--yellow); color:var(--black); padding:8px 20px; font-size:12px; font-weight:700; letter-spacing:2px; text-transform:uppercase; text-decoration:none; font-family:'Barlow Condensed',sans-serif; }

    /* HERO */
    .hero { min-height:100vh; position:relative; overflow:hidden; background:var(--yellow); display:flex; flex-direction:column; justify-content:flex-end; padding:0 56px 0; padding-top:64px; }
    .has-admin-bar .hero { padding-top:104px; }
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
    .branch-top-num { position:absolute; top:-10px; right:10px; font-family:'Anton',sans-serif; font-size:88px; line-height:1; color:rgba(255,255,255,.05); }
    .branch-top-region { font-size:10px; font-weight:700; letter-spacing:4px; text-transform:uppercase; color:var(--yellow); margin-bottom:6px; }
    .branch-top-name { font-family:'Anton',sans-serif; font-size:36px; letter-spacing:2px; color:var(--white); }
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
  </style>
</head>
<body class="{{ auth()->check() && in_array(auth()->user()->role, ['super_admin','region_admin']) ? 'has-admin-bar' : '' }}">

{{-- 어드민 전용 테마 스위처 바 --}}
@auth
  @if(in_array(auth()->user()->role, ['super_admin', 'region_admin']))
  <div class="admin-theme-bar">
    <span class="admin-theme-bar-label">🎨 Theme · 관리자 전용</span>
    <div style="display:flex;align-items:center;gap:12px;">
      <div class="theme-switch-btns">
        <form method="POST" action="{{ route('theme.switch') }}" style="display:inline;">
          @csrf
          <input type="hidden" name="theme" value="v1">
          <button type="submit" class="theme-btn">V1 Dark</button>
        </form>
        <form method="POST" action="{{ route('theme.switch') }}" style="display:inline;">
          @csrf
          <input type="hidden" name="theme" value="v2">
          <button type="submit" class="theme-btn active">V2 Energy</button>
        </form>
      </div>
      <a href="/admin/theme-settings" class="admin-bar-link">어드민 설정 →</a>
    </div>
  </div>
  @endif
@endauth

<!-- NAV -->
<nav>
  <a href="{{ route('home') }}" class="nav-logo">PAC-RUN<div class="nav-logo-dot"></div></a>
  <ul class="nav-links">
    <li><a href="#">소개</a></li>
    <li><a href="#">지부</a></li>
    <li><a href="#">이벤트</a></li>
    <li><a href="#">커뮤니티</a></li>
    <li><a href="#">기록</a></li>
  </ul>
  @auth
    <a href="{{ route('dashboard') }}" class="nav-btn">대시보드</a>
  @else
    <a href="{{ route('login') }}" class="nav-btn">로그인</a>
  @endauth
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-diagonal">
    <div class="hero-diagonal-text">RUNNING</div>
  </div>
  <div class="hero-eyebrow">Seoul Running Crew · Est. 2024</div>
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
      <a href="#" class="pac-content-more">자세히 보기 →</a>
    </div>
  </div>
</section>

<!-- 지부 소개 -->
<section class="branch-section">
  <div class="section-eyebrow">지부 소개</div>
  <div class="section-heading">4개 지부,<br><em>하나의</em> 크루</div>
  <div class="swiper swiper-branches">
    <div class="swiper-wrapper">
      <div class="swiper-slide" style="width:300px"><div class="branch-card"><div class="branch-top branch-banpo-color"><div class="branch-top-num">01</div><div class="branch-top-region">BANPO · 반포</div><div class="branch-top-name">반포 지부</div></div><div class="branch-bottom"><div class="branch-slogan">한강변을 달리며 자유를 느끼는 반포 러너들</div><a href="#" class="branch-arrow-btn">지부 보기</a></div></div></div>
      <div class="swiper-slide" style="width:300px"><div class="branch-card"><div class="branch-top branch-yonsei-color"><div class="branch-top-num">02</div><div class="branch-top-region">YONSEI · 연대</div><div class="branch-top-name">연대 지부</div></div><div class="branch-bottom"><div class="branch-slogan">캠퍼스와 도심을 누비는 연대 러닝팀</div><a href="#" class="branch-arrow-btn">지부 보기</a></div></div></div>
      <div class="swiper-slide" style="width:300px"><div class="branch-card"><div class="branch-top branch-gunpo-color"><div class="branch-top-num">03</div><div class="branch-top-region">GUNPO · 군포</div><div class="branch-top-name">군포 지부</div></div><div class="branch-bottom"><div class="branch-slogan">수리산 트레일과 함께하는 군포 러너들</div><a href="#" class="branch-arrow-btn">지부 보기</a></div></div></div>
      <div class="swiper-slide" style="width:300px"><div class="branch-card"><div class="branch-top branch-incheon-color"><div class="branch-top-num">04</div><div class="branch-top-region">INCHEON · 인천</div><div class="branch-top-name">인천 지부</div></div><div class="branch-bottom"><div class="branch-slogan">바다 내음과 함께 달리는 인천 러닝 패밀리</div><a href="#" class="branch-arrow-btn">지부 보기</a></div></div></div>
    </div>
  </div>
</section>

<!-- 이벤트 -->
<section class="event-section">
  <div class="section-eyebrow" style="color:var(--yellow)">Events</div>
  <div class="section-heading">다가오는<br>이벤트</div>
  <div class="event-grid">
    <div class="event-card"><div class="event-thumb et-1"><div class="event-thumb-num">01</div><div class="event-date-badge">2026.06.14</div></div><div class="event-body"><div class="event-region-tag">전체 · B Type</div><div class="event-title">PAC-RUN 여름 챌린지 런</div><div class="event-meta-line">📍 반포 한강공원</div><div class="event-meta-line">🕕 오전 6:30</div><a href="#" class="event-link">신청하기 →</a></div></div>
    <div class="event-card"><div class="event-thumb et-2"><div class="event-thumb-num">02</div><div class="event-date-badge">2026.06.21</div></div><div class="event-body"><div class="event-region-tag">반포 · B Type</div><div class="event-title">반포 나이트런 5K</div><div class="event-meta-line">📍 반포대교 남단</div><div class="event-meta-line">🌙 오후 8:00</div><a href="#" class="event-link">신청하기 →</a></div></div>
    <div class="event-card"><div class="event-thumb et-3"><div class="event-thumb-num">03</div><div class="event-date-badge">2026.07.05</div></div><div class="event-body"><div class="event-region-tag">인천 · B Type</div><div class="event-title">인천 오션런 10K</div><div class="event-meta-line">📍 송도 센트럴파크</div><div class="event-meta-line">🕖 오전 7:00</div><a href="#" class="event-link">신청하기 →</a></div></div>
    <div class="event-card"><div class="event-thumb et-4"><div class="event-thumb-num">04</div><div class="event-date-badge">2026.07.12</div></div><div class="event-body"><div class="event-region-tag">군포 · B Type</div><div class="event-title">수리산 트레일 러닝</div><div class="event-meta-line">📍 군포 수리산도립공원</div><div class="event-meta-line">🌅 오전 6:00</div><a href="#" class="event-link">신청하기 →</a></div></div>
  </div>
</section>

<!-- 커뮤니티 -->
<section class="community-section">
  <div class="section-eyebrow">Community</div>
  <div class="section-heading">커뮤니티</div>
  <div class="community-grid">
    <div class="community-card">
      <div class="community-card-head"><div class="community-card-name">공지사항</div><a href="#" class="community-card-more">전체보기</a></div>
      <div class="notice-tabs">
        <div class="ntab on" onclick="selectTab(this)">전체</div>
        <div class="ntab" onclick="selectTab(this)">반포</div>
        <div class="ntab" onclick="selectTab(this)">연대</div>
        <div class="ntab" onclick="selectTab(this)">군포</div>
        <div class="ntab" onclick="selectTab(this)">인천</div>
      </div>
      <ul class="post-list">
        <li class="post-item"><span class="post-name">[필독] 6월 정기런 일정 안내</span><span class="post-date">06.01</span></li>
        <li class="post-item"><span class="post-name">2기 모집 마감 및 합격자 발표</span><span class="post-date">05.28</span></li>
        <li class="post-item"><span class="post-name">여름 챌린지 이벤트 신청 방법</span><span class="post-date">05.25</span></li>
        <li class="post-item"><span class="post-name">5월 마일리지 집계 결과 공유</span><span class="post-date">05.20</span></li>
        <li class="post-item"><span class="post-name">크루 운영 규정 업데이트 안내</span><span class="post-date">05.15</span></li>
      </ul>
    </div>
    <div class="community-card">
      <div class="community-card-head"><div class="community-card-name">자유게시판</div><a href="#" class="community-card-more">전체보기</a></div>
      <ul class="post-list">
        <li class="post-item"><span class="post-name">오늘 새벽런 같이 뛰실 분~</span><span class="post-date">06.03</span></li>
        <li class="post-item"><span class="post-name">나이키 버퍼나이트 어떠셨나요?</span><span class="post-date">06.02</span></li>
        <li class="post-item"><span class="post-name">러닝화 추천 부탁드려요</span><span class="post-date">06.01</span></li>
        <li class="post-item"><span class="post-name">한강 새벽 5시 코스 루트 공유</span><span class="post-date">05.31</span></li>
        <li class="post-item"><span class="post-name">첫 하프마라톤 완주 후기 🏅</span><span class="post-date">05.29</span></li>
      </ul>
    </div>
    <div class="community-card">
      <div class="community-card-head"><div class="community-card-name">포토 갤러리</div><a href="#" class="community-card-more">전체보기</a></div>
      <ul class="post-list">
        <li class="post-item"><span class="post-name">반포 한강 새벽런 스냅샷 모음</span><span class="post-date">06.02</span></li>
        <li class="post-item"><span class="post-name">5월 정기런 단체사진 & 기록</span><span class="post-date">05.30</span></li>
        <li class="post-item"><span class="post-name">인천 오션런 현장 포토</span><span class="post-date">05.25</span></li>
        <li class="post-item"><span class="post-name">군포 트레일 러닝 스냅</span><span class="post-date">05.18</span></li>
        <li class="post-item"><span class="post-name">1기 수료식 기념 사진</span><span class="post-date">05.10</span></li>
      </ul>
    </div>
    <div class="community-card">
      <div class="community-card-head"><div class="community-card-name">문의게시판</div><a href="#" class="community-card-more">전체보기</a></div>
      <ul class="post-list">
        <li class="post-item"><span class="post-name">이벤트 환불 정책 문의드립니다</span><span class="post-date">06.03</span></li>
        <li class="post-item"><span class="post-name">지부 이전 신청은 어떻게?</span><span class="post-date">06.01</span></li>
        <li class="post-item"><span class="post-name">마일리지 오류 제보</span><span class="post-date">05.28</span></li>
        <li class="post-item"><span class="post-name">초대코드 재발급 요청</span><span class="post-date">05.27</span></li>
        <li class="post-item"><span class="post-name">앱 로그인 오류 문의</span><span class="post-date">05.24</span></li>
      </ul>
    </div>
  </div>
</section>

<!-- Instagram -->
<section class="insta-section">
  <div class="section-eyebrow">Instagram</div>
  <div class="section-heading">@pac.run<em style="color:var(--yellow)">.crew</em></div>
  <div class="swiper swiper-insta">
    <div class="swiper-wrapper">
      <div class="swiper-slide" style="width:220px"><div class="insta-card ic-1"><div class="insta-inner"><div class="insta-icon-big">RUN</div></div><div class="insta-hover"><div class="insta-hover-label">인스타 보기 →</div></div><div class="insta-tag">#pacrun</div></div></div>
      <div class="swiper-slide" style="width:220px"><div class="insta-card ic-2"><div class="insta-inner"><div class="insta-icon-big">PAC</div></div><div class="insta-hover"><div class="insta-hover-label">인스타 보기 →</div></div><div class="insta-tag">#running</div></div></div>
      <div class="swiper-slide" style="width:220px"><div class="insta-card ic-3"><div class="insta-inner"><div class="insta-icon-big">5K</div></div><div class="insta-hover"><div class="insta-hover-label">인스타 보기 →</div></div><div class="insta-tag">#crew</div></div></div>
      <div class="swiper-slide" style="width:220px"><div class="insta-card ic-4"><div class="insta-inner"><div class="insta-icon-big">GO!</div></div><div class="insta-hover"><div class="insta-hover-label">인스타 보기 →</div></div><div class="insta-tag">#pacrun</div></div></div>
      <div class="swiper-slide" style="width:220px"><div class="insta-card ic-5"><div class="insta-inner"><div class="insta-icon-big">KM</div></div><div class="insta-hover"><div class="insta-hover-label">인스타 보기 →</div></div><div class="insta-tag">#runner</div></div></div>
      <div class="swiper-slide" style="width:220px"><div class="insta-card ic-6"><div class="insta-inner"><div class="insta-icon-big">FIT</div></div><div class="insta-hover"><div class="insta-hover-label">인스타 보기 →</div></div><div class="insta-tag">#seoul</div></div></div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer>
  <div class="footer-main">
    <div><div class="footer-logo">PAC-RUN</div><div class="footer-desc">함께 달리고, 함께 성장하는<br>서울 러닝 크루 PAC-RUN.<br>반포 · 연대 · 군포 · 인천</div></div>
    <div><div class="footer-col-title">소개</div><ul class="footer-links"><li><a href="#">PAC-RUN 소개</a></li><li><a href="#">지부 안내</a></li><li><a href="#">운영진</a></li><li><a href="{{ route('apply') }}">가입 안내</a></li></ul></div>
    <div><div class="footer-col-title">활동</div><ul class="footer-links"><li><a href="#">이벤트</a></li><li><a href="#">기록 관리</a></li><li><a href="#">포토 갤러리</a></li><li><a href="#">랭킹</a></li></ul></div>
    <div><div class="footer-col-title">고객지원</div><ul class="footer-links"><li><a href="#">공지사항</a></li><li><a href="#">문의하기</a></li><li><a href="#">버그 제보</a></li><li><a href="#">개인정보처리방침</a></li></ul></div>
  </div>
  <div class="footer-bottom">
    <span>© 2026 PAC-RUN. All rights reserved.</span>
    <a href="#" class="footer-insta">@pac.run.crew</a>
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
</script>
</body>
</html>
