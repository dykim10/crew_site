<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>비밀번호 재설정 — PAC RUN CREW</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    background-color: #0C0C0C;
    font-family: 'Noto Sans KR', -apple-system, sans-serif;
    -webkit-font-smoothing: antialiased;
    mso-line-height-rule: exactly;
  }

  .outer-wrap {
    background-color: #0C0C0C;
    padding: 48px 20px;
  }

  .email-wrapper {
    max-width: 600px;
    margin: 0 auto;
    background-color: #141010;
  }

  /* ── 상단 레드 라인 ── */
  .top-stripe {
    height: 3px;
    background-color: #E80043;
  }

  /* ── 헤더 ── */
  .header {
    background-color: #141010;
    padding: 22px 40px;
    border-bottom: 1px solid #1F1C1C;
  }

  .header table { width: 100%; border-collapse: collapse; }
  .header td { vertical-align: middle; }

  .header-logo {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 24px;
    letter-spacing: 5px;
    color: #E5AD16;
    line-height: 1;
  }

  .header-pipe {
    padding: 0 12px;
    color: #2A2020;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 18px;
  }

  .header-sub {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 11px;
    letter-spacing: 4px;
    color: #3D3535;
  }

  /* ── 히어로 ── */
  .hero {
    background-color: #100A0A;
    padding: 52px 40px 44px;
    position: relative;
    overflow: hidden;
  }

  .hero-watermark {
    position: absolute;
    bottom: -32px;
    right: -16px;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 200px;
    color: transparent;
    -webkit-text-stroke: 1px rgba(232, 0, 67, 0.08);
    letter-spacing: 0;
    line-height: 1;
    pointer-events: none;
    user-select: none;
  }

  .hero-meta {
    margin-bottom: 28px;
  }

  .hero-step {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 10px;
    letter-spacing: 5px;
    color: #3A3030;
    display: inline-block;
    margin-right: 10px;
    vertical-align: middle;
  }

  .hero-step-line {
    display: inline-block;
    width: 32px;
    height: 1px;
    background-color: #2A2020;
    vertical-align: middle;
    margin-right: 10px;
  }

  .hero-badge {
    display: inline-block;
    background-color: #E80043;
    color: #FFFFFF;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 10px;
    letter-spacing: 4px;
    padding: 5px 14px;
    vertical-align: middle;
  }

  .hero-title-line1 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 72px;
    letter-spacing: 2px;
    color: #FFFFFF;
    line-height: 1;
    display: block;
  }

  .hero-title-line2 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 72px;
    letter-spacing: 2px;
    color: #E80043;
    line-height: 0.9;
    display: block;
  }

  .hero-rule {
    border: none;
    border-top: 1px solid #200A0A;
    margin: 28px 0 20px;
  }

  .hero-desc {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 10px;
    letter-spacing: 3px;
    color: #2E2020;
  }

  /* ── 본문 ── */
  .body {
    background-color: #FFFFFF;
    padding: 48px 40px 44px;
  }

  .body-eyebrow {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 10px;
    letter-spacing: 4px;
    color: #E80043;
    margin-bottom: 20px;
    display: block;
  }

  .greeting {
    font-size: 22px;
    font-weight: 700;
    color: #141010;
    margin-bottom: 18px;
    line-height: 1.35;
  }

  .greeting-name {
    color: #E5AD16;
    border-bottom: 2px solid rgba(229, 173, 22, 0.4);
    padding-bottom: 1px;
  }

  .message {
    font-size: 14px;
    font-weight: 300;
    color: #666;
    line-height: 1.9;
    margin-bottom: 40px;
  }

  /* ── CTA ── */
  .cta-wrap {
    margin-bottom: 40px;
    text-align: center;
  }

  .cta-btn {
    display: inline-block;
    background-color: #E80043;
    color: #FFFFFF;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 18px;
    letter-spacing: 6px;
    padding: 20px 60px;
    text-decoration: none;
    border-bottom: 5px solid #A8002F;
  }

  /* ── 구분선 ── */
  .divider { border: none; border-top: 1px solid #F2F2F0; margin: 36px 0; }

  /* ── URL 박스 ── */
  .url-box {
    background-color: #F8F8F6;
    border-left: 4px solid #0F0D0D;
    padding: 16px 20px;
    margin-bottom: 20px;
  }

  .url-label {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 10px;
    letter-spacing: 4px;
    color: #E80043;
    display: block;
    margin-bottom: 8px;
  }

  .url-text {
    font-size: 10px;
    color: #AAA;
    word-break: break-all;
    line-height: 1.6;
  }

  /* ── 만료 경고 ── */
  .expire-box {
    background-color: #FFFAF8;
    border: 1px solid #FFE8DC;
    border-left: 4px solid #E80043;
    padding: 14px 18px;
    margin-bottom: 16px;
  }

  .expire-text {
    font-size: 12px;
    font-weight: 500;
    color: #CC2200;
    line-height: 1.5;
  }

  .not-me {
    font-size: 11px;
    color: #CCC;
    text-align: center;
    letter-spacing: 0.3px;
  }

  /* ── 푸터 ── */
  .footer {
    background-color: #0F0D0D;
    padding: 32px 40px 28px;
    border-top: 3px solid #E80043;
  }

  .footer table { width: 100%; border-collapse: collapse; }
  .footer td { vertical-align: bottom; }

  .footer-logo {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 20px;
    letter-spacing: 6px;
    color: #E5AD16;
    display: block;
  }

  .footer-est {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 10px;
    letter-spacing: 4px;
    color: #2A2020;
    display: block;
    margin-top: 4px;
  }

  .footer-links-cell { text-align: right; }

  .footer-link {
    font-size: 11px;
    color: #3D3535;
    text-decoration: none;
    letter-spacing: 1px;
    display: block;
    line-height: 2;
  }

  .footer-copy {
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #1A1818;
    font-size: 9px;
    color: #2A2020;
    letter-spacing: 2px;
    text-align: center;
    text-transform: uppercase;
  }

  /* ── 모바일 반응형 ── */
  @media screen and (max-width: 620px) {
    .outer-wrap { padding: 0 !important; }
    .email-wrapper { width: 100% !important; }

    .header { padding: 14px 20px !important; }
    .header-logo { font-size: 18px !important; letter-spacing: 3px !important; }
    .header-sub { display: none !important; }

    .hero { padding: 32px 20px 26px !important; }
    .hero-title-line1 { font-size: 48px !important; }
    .hero-title-line2 { font-size: 48px !important; }
    .hero-watermark { font-size: 120px !important; bottom: -16px !important; right: -8px !important; }
    .hero-desc { font-size: 8px !important; letter-spacing: 1px !important; }
    .hero-step-line { display: none !important; }

    .body { padding: 28px 20px 24px !important; }
    .greeting { font-size: 17px !important; }
    .message { font-size: 13px !important; }

    .cta-btn {
      display: block !important;
      padding: 16px 20px !important;
      font-size: 14px !important;
      letter-spacing: 3px !important;
      text-align: center !important;
      box-sizing: border-box !important;
    }

    .url-box { padding: 12px 14px !important; }
    .expire-box { padding: 12px 14px !important; }

    .footer { padding: 22px 20px 18px !important; }
    .footer-links-cell { display: none !important; }
    .footer-logo { font-size: 15px !important; letter-spacing: 4px !important; }
  }
</style>
</head>
<body>
<div class="outer-wrap">
<div class="email-wrapper">

  <!-- 상단 레드 라인 -->
  <div class="top-stripe"></div>

  <!-- 헤더 -->
  <div class="header">
    <table>
      <tr>
        <td><span class="header-logo">PAC RUN</span></td>
        <td><span class="header-pipe">/</span></td>
        <td><span class="header-sub">CREW · SINCE 2024</span></td>
      </tr>
    </table>
  </div>

  <!-- 히어로 -->
  <div class="hero">
    <div class="hero-watermark">R</div>

    <div class="hero-meta">
      <span class="hero-step">SEC</span>
      <span class="hero-step-line"></span>
      <span class="hero-badge">PASSWORD RESET</span>
    </div>

    <span class="hero-title-line1">비밀번호</span>
    <span class="hero-title-line2">재설정 요청</span>

    <hr class="hero-rule">
    <div class="hero-desc">HIGH INTENSITY INTERVAL TRAINING PARTNERSHIP ACTIVATION CREW</div>
  </div>

  <!-- 본문 -->
  <div class="body">

    <span class="body-eyebrow">— 비밀번호 재설정 안내</span>

    <div class="greeting">
      안녕하세요,<br>
      <span class="greeting-name">{{ $user->nickname ?? $user->name ?? '러너' }}</span>님!
    </div>

    <p class="message">
      비밀번호 재설정 요청이 접수되었습니다.<br>
      아래 버튼을 클릭하여 새로운 비밀번호를 설정해 주세요.<br>
      본인이 요청하지 않은 경우 이 메일을 무시하시면 됩니다.
    </p>

    <div class="cta-wrap">
      <a href="{{ $url }}" class="cta-btn">비밀번호 재설정하기</a>
    </div>

    <hr class="divider">

    <div class="url-box">
      <span class="url-label">RESET LINK</span>
      <div class="url-text">{{ $url }}</div>
    </div>

    <div class="expire-box">
      <div class="expire-text">⏱&ensp;이 링크는 <strong>60분</strong> 후 만료됩니다.</div>
    </div>

    <p class="not-me">본인이 요청하지 않은 경우 이 메일을 무시하세요.</p>

  </div>

  <!-- 푸터 -->
  <div class="footer">
    <table>
      <tr>
        <td>
          <span class="footer-logo">PAC RUN CREW</span>
          <span class="footer-est">EST. 2024</span>
        </td>
        <td class="footer-links-cell">
          <a href="https://crew.pac-run.com" class="footer-link">홈페이지</a>
          <a href="https://crew.pac-run.com/boards/qna" class="footer-link">문의하기</a>
        </td>
      </tr>
    </table>
    <div class="footer-copy">© {{ date('Y') }} PAC RUN. All rights reserved.</div>
  </div>

</div>
</div>
</body>
</html>
