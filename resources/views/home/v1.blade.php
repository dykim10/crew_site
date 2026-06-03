<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PAC-RUN CREW</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@300;400;600;700;900&family=Noto+Sans+KR:wght@300;400;500;700;900&display=swap" rel="stylesheet">
  <style>
    :root { --yellow:#E5AD16; --black:#1A1212; --pink:#E80043; --bg:#0D0D0D; --bg2:#131313; --white:#FFFFFF; --border:rgba(255,255,255,0.08); }
    * { margin:0; padding:0; box-sizing:border-box; }
    html { scroll-behavior:smooth; }
    body { background:var(--bg); color:var(--white); font-family:'Barlow','Noto Sans KR',sans-serif; overflow-x:hidden; }

    /* ===== 어드민 테마 스위처 바 ===== */
    .admin-theme-bar {
      position:fixed; top:0; left:0; right:0; z-index:200;
      background:#1A1212; border-bottom:1px solid var(--yellow);
      display:flex; align-items:center; justify-content:space-between;
      padding:0 56px; height:40px; font-size:11px;
    }
    .admin-theme-bar-label { color:rgba(255,255,255,.5); letter-spacing:2px; text-transform:uppercase; font-weight:600; }
    .theme-switch-btns { display:flex; gap:6px; align-items:center; }
    .theme-btn {
      padding:4px 16px; font-size:10px; font-weight:700; letter-spacing:2px;
      text-transform:uppercase; border:1px solid rgba(255,255,255,.2);
      background:transparent; color:rgba(255,255,255,.5); cursor:pointer; transition:all .2s;
    }
    .theme-btn.active { background:var(--yellow); border-color:var(--yellow); color:var(--black); }
    .theme-btn:not(.active):hover { border-color:rgba(255,255,255,.5); color:rgba(255,255,255,.8); }
    .admin-bar-link { color:rgba(229,173,22,.6); text-decoration:none; font-size:10px; letter-spacing:1px; margin-left:20px; }
    .admin-bar-link:hover { color:var(--yellow); }

    /* NAV */
    .has-admin-bar nav { top:40px; }
    nav { position:fixed; top:0; left:0; right:0; z-index:100; display:flex; align-items:center; justify-content:space-between; padding:0 56px; height:68px; background:rgba(13,13,13,0.92); border-bottom:1px solid var(--border); backdrop-filter:blur(16px); }
    .nav-logo { font-family:'Bebas Neue',sans-serif; font-size:26px; letter-spacing:5px; color:var(--yellow); text-decoration:none; }
    .nav-logo span { color:var(--white); }
    .nav-links { display:flex; gap:36px; list-style:none; }
    .nav-links a { color:rgba(255,255,255,.55); text-decoration:none; font-size:12px; font-weight:600; letter-spacing:2px; text-transform:uppercase; transition:color .2s; }
    .nav-links a:hover { color:var(--yellow); }
    .nav-btn { background:var(--yellow); color:var(--black); padding:9px 22px; font-size:12px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; clip-path:polygon(6px 0,100% 0,calc(100% - 6px) 100%,0 100%); text-decoration:none; }

    /* HERO */
    .hero { min-height:100vh; display:flex; flex-direction:column; justify-content:center; padding:120px 56px 80px; position:relative; overflow:hidden; }
    .has-admin-bar .hero { padding-top:160px; }
    .hero-glow { position:absolute; top:20%; right:10%; width:600px; height:600px; background:radial-gradient(circle, rgba(229,173,22,0.07) 0%, transparent 70%); pointer-events:none; }
    .hero-grid-lines { position:absolute; inset:0; background-image:linear-gradient(rgba(255,255,255,0.02) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.02) 1px,transparent 1px); background-size:80px 80px; }
    .hero-eyebrow { font-size:11px; font-weight:700; letter-spacing:5px; text-transform:uppercase; color:var(--yellow); margin-bottom:20px; }
    .hero-title { font-family:'Bebas Neue',sans-serif; font-size:clamp(90px,15vw,220px); line-height:.85; letter-spacing:-3px; position:relative; z-index:1; }
    .hero-title .line2 { -webkit-text-stroke:2px rgba(255,255,255,0.15); color:transparent; }
    .hero-title .accent { color:var(--yellow); }
    .hero-rule { width:80px; height:3px; background:var(--yellow); margin:36px 0; }
    .hero-sub { font-size:16px; font-weight:300; color:rgba(255,255,255,.55); max-width:500px; line-height:1.8; margin-bottom:52px; }
    .hero-cta { display:flex; gap:16px; align-items:center; }
    .btn-primary { background:var(--yellow); color:var(--black); padding:14px 36px; font-size:12px; font-weight:700; letter-spacing:2px; text-transform:uppercase; text-decoration:none; display:inline-flex; align-items:center; gap:10px; }
    .btn-ghost { border:1px solid rgba(255,255,255,.25); color:rgba(255,255,255,.7); padding:14px 36px; font-size:12px; font-weight:600; letter-spacing:2px; text-transform:uppercase; text-decoration:none; transition:all .25s; }
    .btn-ghost:hover { border-color:var(--yellow); color:var(--yellow); }
    .hero-stats { display:flex; gap:0; margin-top:80px; border-top:1px solid var(--border); padding-top:40px; }
    .hero-stat { flex:1; padding-right:40px; border-right:1px solid var(--border); margin-right:40px; }
    .hero-stat:last-child { border-right:none; margin-right:0; }
    .hero-stat-num { font-family:'Bebas Neue',sans-serif; font-size:52px; color:var(--yellow); line-height:1; }
    .hero-stat-label { font-size:11px; font-weight:600; letter-spacing:2px; color:rgba(255,255,255,.35); text-transform:uppercase; margin-top:4px; }

    /* SECTION COMMON */
    section { padding:100px 56px; }
    .section-label { font-size:11px; font-weight:700; letter-spacing:4px; text-transform:uppercase; color:var(--yellow); margin-bottom:12px; display:flex; align-items:center; gap:14px; }
    .section-label::before { content:''; width:28px; height:2px; background:var(--yellow); flex-shrink:0; }
    .section-heading { font-family:'Bebas Neue',sans-serif; font-size:clamp(44px,5.5vw,80px); letter-spacing:1px; line-height:1; margin-bottom:56px; }

    /* PAC 소개 */
    .pac-section { background:var(--bg2); }
    .pac-banner { display:grid; grid-template-columns:1fr 1fr; border:1px solid var(--border); overflow:hidden; }
    .pac-image { height:420px; background:linear-gradient(135deg, #1a1212 0%, #3d2800 60%, #1a1212 100%); position:relative; display:flex; align-items:center; justify-content:center; overflow:hidden; }
    .pac-image::before { content:''; position:absolute; inset:0; background:repeating-linear-gradient(45deg, transparent, transparent 30px, rgba(229,173,22,0.03) 30px, rgba(229,173,22,0.03) 31px); }
    .pac-image-big-text { font-family:'Bebas Neue',sans-serif; font-size:100px; letter-spacing:10px; color:rgba(229,173,22,0.12); position:absolute; }
    .pac-image-tag { position:absolute; bottom:24px; left:24px; background:var(--yellow); color:var(--black); font-size:10px; font-weight:700; letter-spacing:3px; text-transform:uppercase; padding:6px 14px; }
    .pac-content { padding:64px; display:flex; flex-direction:column; justify-content:center; }
    .pac-content h2 { font-family:'Bebas Neue',sans-serif; font-size:52px; line-height:1.05; margin-bottom:20px; }
    .pac-content h2 em { color:var(--yellow); font-style:normal; }
    .pac-content p { font-size:15px; line-height:1.9; color:rgba(255,255,255,.55); margin-bottom:16px; font-weight:300; }
    .btn-outline { display:inline-flex; align-items:center; gap:10px; border:1px solid rgba(229,173,22,.5); color:var(--yellow); padding:12px 28px; font-size:11px; font-weight:700; letter-spacing:2px; text-transform:uppercase; text-decoration:none; transition:all .25s; width:fit-content; margin-top:20px; }
    .btn-outline:hover { background:var(--yellow); color:var(--black); }

    /* 지부 */
    .branch-section { background:var(--bg); }
    .swiper-branches { overflow:hidden; }
    .branch-card { height:300px; position:relative; overflow:hidden; cursor:pointer; border:1px solid var(--border); transition:border-color .3s; }
    .branch-card:hover { border-color:var(--yellow); }
    .branch-bg { position:absolute; inset:0; transition:transform .6s ease; }
    .branch-card:hover .branch-bg { transform:scale(1.06); }
    .branch-banpo { background:linear-gradient(160deg, #2d1a00, #0d0d0d); }
    .branch-yonsei { background:linear-gradient(160deg, #001a2d, #0d0d0d); }
    .branch-gunpo { background:linear-gradient(160deg, #0d2200, #0d0d0d); }
    .branch-incheon { background:linear-gradient(160deg, #001a2d, #1a001a); }
    .branch-overlay { position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,.9) 0%, transparent 55%); }
    .branch-number { position:absolute; top:20px; right:20px; font-family:'Bebas Neue',sans-serif; font-size:64px; color:rgba(255,255,255,0.05); line-height:1; }
    .branch-content { position:absolute; bottom:0; left:0; right:0; padding:24px; }
    .branch-region-tag { font-size:10px; font-weight:700; letter-spacing:3px; text-transform:uppercase; color:var(--yellow); margin-bottom:8px; }
    .branch-name { font-family:'Bebas Neue',sans-serif; font-size:34px; letter-spacing:2px; margin-bottom:8px; }
    .branch-slogan { font-size:12px; color:rgba(255,255,255,.5); line-height:1.5; }
    .branch-arrow { position:absolute; top:20px; left:20px; width:38px; height:38px; border:1px solid rgba(229,173,22,.4); display:flex; align-items:center; justify-content:center; opacity:0; transition:opacity .3s; font-size:14px; }
    .branch-card:hover .branch-arrow { opacity:1; }

    /* 이벤트 */
    .event-section { background:var(--bg2); }
    .event-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; }
    .event-card { border:1px solid var(--border); overflow:hidden; cursor:pointer; transition:border-color .3s, transform .3s; }
    .event-card:hover { border-color:var(--yellow); transform:translateY(-6px); }
    .event-thumb { height:200px; position:relative; display:flex; align-items:center; justify-content:center; overflow:hidden; }
    .et-1 { background:linear-gradient(135deg, #2d1a00, #0d0d0d); }
    .et-2 { background:linear-gradient(135deg, #00122d, #0d0d0d); }
    .et-3 { background:linear-gradient(135deg, #2d001a, #0d0d0d); }
    .et-4 { background:linear-gradient(135deg, #001a15, #0d0d0d); }
    .event-thumb::after { content:'EVENT'; font-family:'Bebas Neue',sans-serif; font-size:52px; letter-spacing:8px; color:rgba(255,255,255,0.05); position:absolute; }
    .event-date-tag { position:absolute; top:14px; left:14px; background:var(--yellow); color:var(--black); font-size:10px; font-weight:700; letter-spacing:1px; padding:4px 10px; z-index:1; }
    .event-info { padding:18px 20px 22px; }
    .event-type-tag { font-size:10px; font-weight:700; letter-spacing:3px; text-transform:uppercase; color:var(--yellow); margin-bottom:8px; }
    .event-title { font-size:15px; font-weight:700; margin-bottom:12px; line-height:1.4; }
    .event-meta { font-size:12px; color:rgba(255,255,255,.4); display:flex; flex-direction:column; gap:4px; }
    .event-meta span { display:flex; align-items:center; gap:6px; }

    /* 커뮤니티 */
    .community-section { background:var(--bg); }
    .community-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .community-card { border:1px solid var(--border); padding:28px 32px; transition:border-color .3s; }
    .community-card:hover { border-color:rgba(229,173,22,.3); }
    .community-card-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; padding-bottom:16px; border-bottom:1px solid var(--border); }
    .community-card-name { font-family:'Bebas Neue',sans-serif; font-size:22px; letter-spacing:2px; }
    .community-card-link { font-size:10px; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:var(--yellow); text-decoration:none; }
    .notice-tabs { display:flex; gap:0; margin-bottom:14px; }
    .ntab { padding:5px 12px; font-size:10px; font-weight:700; letter-spacing:1.5px; cursor:pointer; border:1px solid var(--border); border-right:none; color:rgba(255,255,255,.4); transition:all .2s; }
    .ntab:last-child { border-right:1px solid var(--border); }
    .ntab.on { background:var(--yellow); border-color:var(--yellow); color:var(--black); }
    .post-list { list-style:none; }
    .post-item { display:flex; justify-content:space-between; align-items:center; padding:9px 0; border-bottom:1px solid rgba(255,255,255,.04); cursor:pointer; }
    .post-item:last-child { border-bottom:none; }
    .post-item:hover .post-name { color:var(--yellow); }
    .post-name { font-size:13px; color:rgba(255,255,255,.75); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:220px; transition:color .2s; }
    .post-date { font-size:11px; color:rgba(255,255,255,.25); flex-shrink:0; margin-left:10px; }

    /* Instagram */
    .insta-section { background:var(--bg2); }
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

    /* Footer */
    footer { background:#090909; border-top:1px solid var(--border); padding:64px 56px 32px; }
    .footer-main { display:grid; grid-template-columns:2fr 1fr 1fr 1fr; gap:60px; margin-bottom:48px; }
    .footer-brand-name { font-family:'Bebas Neue',sans-serif; font-size:34px; letter-spacing:5px; color:var(--yellow); margin-bottom:12px; }
    .footer-brand-name span { color:white; }
    .footer-desc { font-size:13px; line-height:1.9; color:rgba(255,255,255,.3); font-weight:300; }
    .footer-col-title { font-size:10px; font-weight:700; letter-spacing:3px; text-transform:uppercase; color:var(--yellow); margin-bottom:18px; }
    .footer-links { list-style:none; }
    .footer-links li { margin-bottom:10px; }
    .footer-links a { font-size:13px; color:rgba(255,255,255,.4); text-decoration:none; transition:color .2s; }
    .footer-links a:hover { color:var(--white); }
    .footer-bottom { border-top:1px solid var(--border); padding-top:24px; display:flex; justify-content:space-between; align-items:center; font-size:12px; color:rgba(255,255,255,.2); }
    .footer-bottom a { color:var(--yellow); text-decoration:none; }
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
          <button type="submit" class="theme-btn active">V1 Dark</button>
        </form>
        <form method="POST" action="{{ route('theme.switch') }}" style="display:inline;">
          @csrf
          <input type="hidden" name="theme" value="v2">
          <button type="submit" class="theme-btn">V2 Energy</button>
        </form>
      </div>
      <a href="/admin/theme-settings" class="admin-bar-link">어드민 설정 →</a>
    </div>
  </div>
  @endif
@endauth

<!-- NAV -->
<nav>
  <a href="{{ route('home') }}" class="nav-logo">PAC<span>-RUN</span></a>
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
  <div class="hero-glow"></div>
  <div class="hero-grid-lines"></div>
  <div class="hero-eyebrow">Seoul Running Crew · Since 2024</div>
  <div class="hero-title">
    <div>PAC<span class="accent">-</span></div>
    <div>RUN</div>
    <div class="line2">CREW</div>
  </div>
  <div class="hero-rule"></div>
  <p class="hero-sub">함께 달리고, 함께 성장하는 서울 러닝 크루.<br>반포·연대·군포·인천 — 각 지역에서 달린다.</p>
  <div class="hero-cta">
    <a href="{{ route('apply') }}" class="btn-primary">크루 참여하기 →</a>
    <a href="#about" class="btn-ghost">크루 소개</a>
  </div>
  <div class="hero-stats">
    <div class="hero-stat"><div class="hero-stat-num">240+</div><div class="hero-stat-label">Active Runners</div></div>
    <div class="hero-stat"><div class="hero-stat-num">4</div><div class="hero-stat-label">지부</div></div>
    <div class="hero-stat"><div class="hero-stat-num">12</div><div class="hero-stat-label">이벤트</div></div>
    <div class="hero-stat"><div class="hero-stat-num">1,840</div><div class="hero-stat-label">Total KM</div></div>
  </div>
</section>

<!-- PAC 소개 -->
<section class="pac-section" id="about">
  <div class="section-label">About PAC-RUN</div>
  <div class="section-heading">우리는 함께 달린다</div>
  <div class="pac-banner">
    <div class="pac-image">
      <div class="pac-image-big-text">PAC</div>
      <div class="pac-image-tag">PAC-RUN OFFICIAL</div>
    </div>
    <div class="pac-content">
      <h2>달리기로 하나 되는<br><em>러닝 크루</em></h2>
      <p>PAC-RUN은 서울과 수도권을 기반으로 활동하는 러닝 크루입니다. 반포, 연대, 군포, 인천 4개 지부가 각 지역의 러너들을 연결하고, 함께 달리며 성장하는 커뮤니티를 만들어갑니다.</p>
      <p>매월 정기 런, 특별 이벤트, 지부 간 교류를 통해 혼자가 아닌 함께 달리는 즐거움을 경험하세요.</p>
      <a href="#" class="btn-outline">자세히 보기 →</a>
    </div>
  </div>
</section>

<!-- 지부 소개 -->
<section class="branch-section">
  <div class="section-label">지부 소개</div>
  <div class="section-heading">우리 지부를 만나세요</div>
  <div class="swiper swiper-branches">
    <div class="swiper-wrapper">
      <div class="swiper-slide" style="width:340px">
        <div class="branch-card">
          <div class="branch-bg branch-banpo"></div>
          <div class="branch-overlay"></div>
          <div class="branch-number">01</div>
          <div class="branch-arrow">→</div>
          <div class="branch-content">
            <div class="branch-region-tag">BANPO · 반포</div>
            <div class="branch-name">반포 지부</div>
            <div class="branch-slogan">한강변을 달리며 자유를 느끼는 반포 러너들</div>
          </div>
        </div>
      </div>
      <div class="swiper-slide" style="width:340px">
        <div class="branch-card">
          <div class="branch-bg branch-yonsei"></div>
          <div class="branch-overlay"></div>
          <div class="branch-number">02</div>
          <div class="branch-arrow">→</div>
          <div class="branch-content">
            <div class="branch-region-tag">YONSEI · 연대</div>
            <div class="branch-name">연대 지부</div>
            <div class="branch-slogan">캠퍼스와 도심을 누비는 연대 러닝팀</div>
          </div>
        </div>
      </div>
      <div class="swiper-slide" style="width:340px">
        <div class="branch-card">
          <div class="branch-bg branch-gunpo"></div>
          <div class="branch-overlay"></div>
          <div class="branch-number">03</div>
          <div class="branch-arrow">→</div>
          <div class="branch-content">
            <div class="branch-region-tag">GUNPO · 군포</div>
            <div class="branch-name">군포 지부</div>
            <div class="branch-slogan">수리산 트레일과 함께하는 군포 러너들</div>
          </div>
        </div>
      </div>
      <div class="swiper-slide" style="width:340px">
        <div class="branch-card">
          <div class="branch-bg branch-incheon"></div>
          <div class="branch-overlay"></div>
          <div class="branch-number">04</div>
          <div class="branch-arrow">→</div>
          <div class="branch-content">
            <div class="branch-region-tag">INCHEON · 인천</div>
            <div class="branch-name">인천 지부</div>
            <div class="branch-slogan">바다 내음과 함께 달리는 인천 러닝 패밀리</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 이벤트 -->
<section class="event-section">
  <div class="section-label">Events</div>
  <div class="section-heading">다가오는 이벤트</div>
  <div class="event-grid">
    <div class="event-card"><div class="event-thumb et-1"><div class="event-date-tag">2026.06.14</div></div><div class="event-info"><div class="event-type-tag">B Type · 전체</div><div class="event-title">PAC-RUN 여름 챌린지 런</div><div class="event-meta"><span>📍 반포 한강공원</span><span>🕕 오전 6:30</span></div></div></div>
    <div class="event-card"><div class="event-thumb et-2"><div class="event-date-tag">2026.06.21</div></div><div class="event-info"><div class="event-type-tag">B Type · 반포</div><div class="event-title">반포 나이트런 5K</div><div class="event-meta"><span>📍 반포대교 남단</span><span>🌙 오후 8:00</span></div></div></div>
    <div class="event-card"><div class="event-thumb et-3"><div class="event-date-tag">2026.07.05</div></div><div class="event-info"><div class="event-type-tag">B Type · 인천</div><div class="event-title">인천 오션런 10K</div><div class="event-meta"><span>📍 인천 송도 센트럴파크</span><span>🌅 오전 7:00</span></div></div></div>
    <div class="event-card"><div class="event-thumb et-4"><div class="event-date-tag">2026.07.12</div></div><div class="event-info"><div class="event-type-tag">B Type · 군포</div><div class="event-title">수리산 트레일 러닝</div><div class="event-meta"><span>📍 군포 수리산도립공원</span><span>🌄 오전 6:00</span></div></div></div>
  </div>
</section>

<!-- 커뮤니티 -->
<section class="community-section">
  <div class="section-label">Community</div>
  <div class="section-heading">커뮤니티</div>
  <div class="community-grid">
    <div class="community-card">
      <div class="community-card-top"><div class="community-card-name">공지사항</div><a href="#" class="community-card-link">전체보기</a></div>
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
      <div class="community-card-top"><div class="community-card-name">자유게시판</div><a href="#" class="community-card-link">전체보기</a></div>
      <ul class="post-list">
        <li class="post-item"><span class="post-name">오늘 새벽런 같이 뛰실 분~</span><span class="post-date">06.03</span></li>
        <li class="post-item"><span class="post-name">나이키 버퍼나이트 어떠셨나요?</span><span class="post-date">06.02</span></li>
        <li class="post-item"><span class="post-name">러닝화 추천 부탁드려요</span><span class="post-date">06.01</span></li>
        <li class="post-item"><span class="post-name">한강 새벽 5시 코스 루트 공유</span><span class="post-date">05.31</span></li>
        <li class="post-item"><span class="post-name">첫 하프마라톤 완주 후기 🏅</span><span class="post-date">05.29</span></li>
      </ul>
    </div>
    <div class="community-card">
      <div class="community-card-top"><div class="community-card-name">포토 갤러리</div><a href="#" class="community-card-link">전체보기</a></div>
      <ul class="post-list">
        <li class="post-item"><span class="post-name">반포 한강 새벽런 스냅샷 모음</span><span class="post-date">06.02</span></li>
        <li class="post-item"><span class="post-name">5월 정기런 단체사진 & 기록</span><span class="post-date">05.30</span></li>
        <li class="post-item"><span class="post-name">인천 오션런 현장 포토</span><span class="post-date">05.25</span></li>
        <li class="post-item"><span class="post-name">군포 트레일 러닝 스냅</span><span class="post-date">05.18</span></li>
        <li class="post-item"><span class="post-name">1기 수료식 기념 사진</span><span class="post-date">05.10</span></li>
      </ul>
    </div>
    <div class="community-card">
      <div class="community-card-top"><div class="community-card-name">문의게시판</div><a href="#" class="community-card-link">전체보기</a></div>
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
  <div class="section-label">Instagram</div>
  <div class="section-heading">@pac.run.crew</div>
  <div class="swiper swiper-insta">
    <div class="swiper-wrapper">
      <div class="swiper-slide" style="width:220px"><div class="insta-card ic-1"><div class="insta-hover"><div class="insta-hover-icon">📸</div><div class="insta-hover-text">보기</div></div><div class="insta-watermark">#pacrun</div></div></div>
      <div class="swiper-slide" style="width:220px"><div class="insta-card ic-2"><div class="insta-hover"><div class="insta-hover-icon">📸</div><div class="insta-hover-text">보기</div></div><div class="insta-watermark">#running</div></div></div>
      <div class="swiper-slide" style="width:220px"><div class="insta-card ic-3"><div class="insta-hover"><div class="insta-hover-icon">📸</div><div class="insta-hover-text">보기</div></div><div class="insta-watermark">#crew</div></div></div>
      <div class="swiper-slide" style="width:220px"><div class="insta-card ic-4"><div class="insta-hover"><div class="insta-hover-icon">📸</div><div class="insta-hover-text">보기</div></div><div class="insta-watermark">#pacrun</div></div></div>
      <div class="swiper-slide" style="width:220px"><div class="insta-card ic-5"><div class="insta-hover"><div class="insta-hover-icon">📸</div><div class="insta-hover-text">보기</div></div><div class="insta-watermark">#runner</div></div></div>
      <div class="swiper-slide" style="width:220px"><div class="insta-card ic-6"><div class="insta-hover"><div class="insta-hover-icon">📸</div><div class="insta-hover-text">보기</div></div><div class="insta-watermark">#seoul</div></div></div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer>
  <div class="footer-main">
    <div><div class="footer-brand-name">PAC<span>-RUN</span></div><div class="footer-desc">함께 달리고, 함께 성장하는<br>서울 러닝 크루 PAC-RUN.<br>반포 · 연대 · 군포 · 인천</div></div>
    <div><div class="footer-col-title">소개</div><ul class="footer-links"><li><a href="#">PAC-RUN 소개</a></li><li><a href="#">지부 안내</a></li><li><a href="#">운영진</a></li><li><a href="{{ route('apply') }}">가입 안내</a></li></ul></div>
    <div><div class="footer-col-title">활동</div><ul class="footer-links"><li><a href="#">이벤트</a></li><li><a href="#">기록 관리</a></li><li><a href="#">포토 갤러리</a></li><li><a href="#">랭킹</a></li></ul></div>
    <div><div class="footer-col-title">고객지원</div><ul class="footer-links"><li><a href="#">공지사항</a></li><li><a href="#">문의하기</a></li><li><a href="#">버그 제보</a></li><li><a href="#">개인정보처리방침</a></li></ul></div>
  </div>
  <div class="footer-bottom">
    <span>© 2026 PAC-RUN. All rights reserved.</span>
    <a href="#">Instagram @pac.run.crew</a>
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
</script>
</body>
</html>
