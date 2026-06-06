<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>이메일 인증 — PAC RUN CREW</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Noto+Sans+KR:wght@400;500;700&display=swap');

  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    background-color: #F5F5F0;
    font-family: 'Noto Sans KR', sans-serif;
    color: #1A1A1A;
    -webkit-font-smoothing: antialiased;
  }

  .email-wrapper {
    max-width: 600px;
    margin: 0 auto;
    background-color: #F5F5F0;
  }

  /* ── 헤더 ── */
  .header {
    background-color: #1A1212;
    padding: 28px 40px;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .header-logo {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 28px;
    letter-spacing: 4px;
    color: #E5AD16;
    line-height: 1;
  }

  .header-divider {
    width: 1px;
    height: 20px;
    background-color: #444;
  }

  .header-sub {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 13px;
    letter-spacing: 3px;
    color: #888;
  }

  /* ── 히어로 배너 ── */
  .hero {
    background-color: #1A1212;
    padding: 48px 40px 40px;
    border-bottom: 3px solid #E5AD16;
    position: relative;
    overflow: hidden;
  }

  .hero::before {
    content: 'CREW';
    position: absolute;
    right: -10px;
    top: 50%;
    transform: translateY(-50%);
    font-family: 'Bebas Neue', sans-serif;
    font-size: 120px;
    color: rgba(229, 173, 22, 0.06);
    letter-spacing: 8px;
    line-height: 1;
    pointer-events: none;
  }

  .hero-badge {
    display: inline-block;
    background-color: #E5AD16;
    color: #1A1212;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 11px;
    letter-spacing: 3px;
    padding: 4px 10px;
    margin-bottom: 16px;
  }

  .hero-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 42px;
    letter-spacing: 3px;
    color: #FFFFFF;
    line-height: 1.1;
    margin-bottom: 12px;
  }

  .hero-title span {
    color: #E5AD16;
  }

  .hero-desc {
    font-size: 14px;
    color: #999;
    letter-spacing: 0.3px;
    line-height: 1.6;
  }

  /* ── 본문 ── */
  .body {
    background-color: #FFFFFF;
    padding: 44px 40px;
  }

  .greeting {
    font-size: 18px;
    font-weight: 700;
    color: #1A1212;
    margin-bottom: 16px;
  }

  .greeting span {
    color: #E5AD16;
  }

  .message {
    font-size: 14px;
    color: #555;
    line-height: 1.8;
    margin-bottom: 32px;
  }

  /* ── CTA 버튼 ── */
  .cta-wrap {
    text-align: center;
    margin-bottom: 36px;
  }

  .cta-btn {
    display: inline-block;
    background-color: #E5AD16;
    color: #1A1212;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 17px;
    letter-spacing: 3px;
    padding: 16px 48px;
    text-decoration: none;
    clip-path: polygon(8px 0%, 100% 0%, calc(100% - 8px) 100%, 0% 100%);
    transition: opacity 0.15s;
  }

  /* ── 구분선 ── */
  .divider {
    border: none;
    border-top: 1px solid #E0E0E0;
    margin: 32px 0;
  }

  /* ── URL 안내 ── */
  .url-box {
    background-color: #F5F5F0;
    border-left: 3px solid #E5AD16;
    padding: 14px 16px;
    margin-bottom: 24px;
  }

  .url-label {
    font-size: 11px;
    letter-spacing: 2px;
    color: #999;
    margin-bottom: 6px;
    font-family: 'Bebas Neue', sans-serif;
  }

  .url-text {
    font-size: 11px;
    color: #888;
    word-break: break-all;
    line-height: 1.5;
  }

  /* ── 만료 안내 ── */
  .expire-notice {
    font-size: 12px;
    color: #E80043;
    font-weight: 500;
    text-align: center;
    margin-bottom: 8px;
  }

  .not-me {
    font-size: 12px;
    color: #aaa;
    text-align: center;
  }

  /* ── 푸터 ── */
  .footer {
    background-color: #1A1212;
    padding: 28px 40px;
    text-align: center;
  }

  .footer-logo {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 18px;
    letter-spacing: 5px;
    color: #E5AD16;
    margin-bottom: 8px;
  }

  .footer-copy {
    font-size: 11px;
    color: #555;
    letter-spacing: 1px;
  }

  .footer-links {
    margin-top: 12px;
  }

  .footer-links a {
    font-size: 11px;
    color: #666;
    text-decoration: none;
    margin: 0 8px;
  }
</style>
</head>
<body>
<div class="email-wrapper">

  <!-- 헤더 -->
  <div class="header">
    <div class="header-logo">PAC RUN</div>
    <div class="header-divider"></div>
    <div class="header-sub">CREW · SINCE 2024</div>
  </div>

  <!-- 히어로 -->
  <div class="hero">
    <div class="hero-badge">EMAIL VERIFICATION</div>
    <div class="hero-title">이메일<br><span>인증</span> 요청</div>
    <div class="hero-desc">HIGH INTENSITY INTERVAL TRAINING PARTNERSHIP ACTIVATION CREW</div>
  </div>

  <!-- 본문 -->
  <div class="body">

    <div class="greeting">
      안녕하세요, <span>{{ $user->name ?? '러너' }}</span>님!
    </div>

    <p class="message">
      PAC RUN CREW에 가입해주셔서 감사합니다.<br>
      아래 버튼을 클릭하여 이메일 인증을 완료하면<br>
      모든 서비스를 이용하실 수 있습니다.
    </p>

    <div class="cta-wrap">
      <a href="{{ $url }}" class="cta-btn">이메일 인증하기</a>
    </div>

    <hr class="divider">

    <div class="url-box">
      <div class="url-label">DIRECT LINK</div>
      <div class="url-text">{{ $url }}</div>
    </div>

    <p class="expire-notice">⏱ 이 링크는 60분 후 만료됩니다.</p>
    <p class="not-me">본인이 요청하지 않은 경우 이 메일을 무시하세요.</p>

  </div>

  <!-- 푸터 -->
  <div class="footer">
    <div class="footer-logo">PAC RUN CREW</div>
    <div class="footer-copy">© {{ date('Y') }} PAC RUN. ALL RIGHTS RESERVED.</div>
    <div class="footer-links">
      <a href="https://crew.pac-run.com">홈페이지</a>
      <a href="https://crew.pac-run.com/boards/qna">문의하기</a>
    </div>
  </div>

</div>
</body>
</html>
