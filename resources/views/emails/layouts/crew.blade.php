@php
  $nick = $user->nickname ?? $user->name ?? '러너';
  $fontDisplay = "'Bebas Neue', 'Arial Black', Arial, sans-serif";
  $fontBody = "'Noto Sans KR', 'Apple SD Gothic Neo', 'Malgun Gothic', Arial, sans-serif";
  $accent = $accent ?? '#E5AD16';
  $heroBg = $heroBg ?? '#0F0D0D';
  $badgeBg = $badgeBg ?? '#E5AD16';
  $badgeText = $badgeText ?? '#0F0D0D';
  $title2Color = $title2Color ?? '#E5AD16';
  $eyebrowColor = $eyebrowColor ?? '#E5AD16';
  $ctaBg = $ctaBg ?? '#E5AD16';
  $ctaText = $ctaText ?? '#0F0D0D';
  $ctaBorder = $ctaBorder ?? '#B8881A';
  $urlLabelColor = $urlLabelColor ?? '#E5AD16';
  $urlLinkLabel = $urlLinkLabel ?? 'DIRECT LINK';
  $expireMinutes = $expireMinutes ?? 60;
  $homeUrl = $homeUrl ?? 'https://crew.pac-run.com';
  $contactUrl = $contactUrl ?? 'https://crew.pac-run.com/boards/qna';
@endphp
<!DOCTYPE html>
<html lang="ko" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="x-apple-disable-message-reformatting">
  <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
  <title>{{ $pageTitle }}</title>
  <!--[if mso]>
  <noscript>
    <xml>
      <o:OfficeDocumentSettings>
        <o:PixelsPerInch>96</o:PixelsPerInch>
      </o:OfficeDocumentSettings>
    </xml>
  </noscript>
  <![endif]-->
  <style>
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
    body { margin: 0 !important; padding: 0 !important; width: 100% !important; }
    a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; }
    @media only screen and (max-width: 620px) {
      .email-container { width: 100% !important; }
      .px-40 { padding-left: 20px !important; padding-right: 20px !important; }
      .hero-title { font-size: 40px !important; line-height: 1 !important; }
      .cta-btn { padding: 16px 24px !important; font-size: 14px !important; letter-spacing: 3px !important; }
      .hide-mobile { display: none !important; }
    }
  </style>
</head>
<body style="margin:0;padding:0;background-color:#0C0C0C;word-spacing:normal;">

  <div style="display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;">
    {{ $preheader }}&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
  </div>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#0C0C0C;">
    <tr>
      <td align="center" style="padding:32px 12px;">

        <table role="presentation" class="email-container" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background-color:#141010;">

          {{-- 상단 액센트 라인 --}}
          <tr>
            <td height="3" style="background-color:{{ $accent }};font-size:0;line-height:0;">&nbsp;</td>
          </tr>

          {{-- 헤더 --}}
          <tr>
            <td class="px-40" style="padding:20px 40px;background-color:#141010;border-bottom:1px solid #1F1C1C;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="font-family:{{ $fontDisplay }};font-size:22px;letter-spacing:5px;color:#E5AD16;line-height:1;">
                    PAC RUN
                  </td>
                  <td class="hide-mobile" style="font-family:{{ $fontDisplay }};font-size:16px;color:#2A2020;padding:0 10px;width:20px;">/</td>
                  <td class="hide-mobile" style="font-family:{{ $fontDisplay }};font-size:10px;letter-spacing:4px;color:#3D3535;">
                    CREW · SINCE 2024
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- 히어로 --}}
          <tr>
            <td class="px-40" style="padding:40px 40px 36px;background-color:{{ $heroBg }};">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="padding-bottom:24px;">
                    <span style="font-family:{{ $fontDisplay }};font-size:10px;letter-spacing:5px;color:#3A3030;">{{ $step }}</span>
                    <span style="display:inline-block;width:28px;height:1px;background-color:#2A2020;vertical-align:middle;margin:0 10px;" class="hide-mobile"></span>
                    <span style="display:inline-block;background-color:{{ $badgeBg }};color:{{ $badgeText }};font-family:{{ $fontDisplay }};font-size:10px;letter-spacing:4px;padding:5px 14px;">{{ $badge }}</span>
                  </td>
                </tr>
                <tr>
                  <td>
                    <p class="hero-title" style="margin:0 0 4px;font-family:{{ $fontDisplay }};font-size:52px;letter-spacing:2px;color:#FFFFFF;line-height:1;">{{ $title1 }}</p>
                    <p class="hero-title" style="margin:0;font-family:{{ $fontDisplay }};font-size:52px;letter-spacing:2px;color:{{ $title2Color }};line-height:1;">{{ $title2 }}</p>
                  </td>
                  <td align="right" valign="bottom" class="hide-mobile" style="font-family:{{ $fontDisplay }};font-size:96px;color:#1A1414;line-height:1;width:80px;">
                    {{ $watermark }}
                  </td>
                </tr>
                <tr>
                  <td colspan="2" style="padding-top:20px;border-top:1px solid #1F1C1C;">
                    <p style="margin:0;font-family:{{ $fontDisplay }};font-size:9px;letter-spacing:3px;color:#2E2828;line-height:1.4;">
                      HIGH INTENSITY INTERVAL TRAINING PARTNERSHIP ACTIVATION CREW
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- 본문 --}}
          <tr>
            <td class="px-40" style="padding:40px 40px 36px;background-color:#FFFFFF;">
              <p style="margin:0 0 20px;font-family:{{ $fontDisplay }};font-size:10px;letter-spacing:4px;color:{{ $eyebrowColor }};">
                — {{ $eyebrow }}
              </p>

              <p style="margin:0 0 16px;font-family:{{ $fontBody }};font-size:20px;font-weight:700;color:#141010;line-height:1.4;">
                안녕하세요,<br>
                <span style="color:#E5AD16;border-bottom:2px solid rgba(229,173,22,0.35);">{{ $nick }}</span>님!
              </p>

              <p style="margin:0 0 32px;font-family:{{ $fontBody }};font-size:14px;font-weight:400;color:#666666;line-height:1.85;">
                {!! $messageHtml !!}
              </p>

              {{-- CTA 버튼 (bulletproof) --}}
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:32px;">
                <tr>
                  <td align="center">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td align="center" style="background-color:{{ $ctaBg }};border-bottom:4px solid {{ $ctaBorder }};">
                          <a href="{{ $url }}" target="_blank" class="cta-btn" style="display:inline-block;padding:18px 48px;font-family:{{ $fontDisplay }};font-size:16px;font-weight:bold;letter-spacing:5px;color:{{ $ctaText }};text-decoration:none;mso-padding-alt:0;">
                            <!--[if mso]><i style="letter-spacing:25px;mso-font-width:-100%;mso-text-raise:30pt">&nbsp;</i><![endif]-->
                            <span style="mso-text-raise:15pt;">{{ $ctaLabel }}</span>
                            <!--[if mso]><i style="letter-spacing:25px;mso-font-width:-100%">&nbsp;</i><![endif]-->
                          </a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
                <tr><td style="border-top:1px solid #F0F0EE;font-size:0;line-height:0;">&nbsp;</td></tr>
              </table>

              {{-- 직접 링크 --}}
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;background-color:#F8F8F6;border-left:4px solid #0F0D0D;">
                <tr>
                  <td style="padding:14px 18px;">
                    <p style="margin:0 0 8px;font-family:{{ $fontDisplay }};font-size:9px;letter-spacing:4px;color:{{ $urlLabelColor }};">{{ $urlLinkLabel }}</p>
                    <p style="margin:0;font-family:{{ $fontBody }};font-size:11px;color:#999999;line-height:1.6;word-break:break-all;">
                      <a href="{{ $url }}" target="_blank" style="color:#888888;text-decoration:underline;">{{ $url }}</a>
                    </p>
                  </td>
                </tr>
              </table>

              {{-- 만료 안내 --}}
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;background-color:#FFFAF8;border:1px solid #FFE8DC;border-left:4px solid #E80043;">
                <tr>
                  <td style="padding:12px 16px;font-family:{{ $fontBody }};font-size:12px;font-weight:500;color:#CC2200;line-height:1.5;">
                    ⏱&nbsp;이 링크는 <strong>{{ $expireMinutes }}분</strong> 후 만료됩니다.
                  </td>
                </tr>
              </table>

              <p style="margin:0;font-family:{{ $fontBody }};font-size:11px;color:#BBBBBB;text-align:center;letter-spacing:0.3px;">
                본인이 요청하지 않은 경우 이 메일을 무시하세요.
              </p>
            </td>
          </tr>

          {{-- 푸터 --}}
          <tr>
            <td class="px-40" style="padding:28px 40px 24px;background-color:#0F0D0D;border-top:3px solid {{ $accent }};">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="vertical-align:bottom;">
                    <p style="margin:0;font-family:{{ $fontDisplay }};font-size:18px;letter-spacing:5px;color:#E5AD16;">PAC RUN CREW</p>
                    <p style="margin:4px 0 0;font-family:{{ $fontDisplay }};font-size:9px;letter-spacing:4px;color:#2A2020;">EST. 2024</p>
                  </td>
                  <td align="right" class="hide-mobile" style="vertical-align:bottom;">
                    <a href="{{ $homeUrl }}" style="font-family:{{ $fontBody }};font-size:11px;color:#3D3535;text-decoration:none;letter-spacing:1px;display:block;line-height:2;">홈페이지</a>
                    <a href="{{ $contactUrl }}" style="font-family:{{ $fontBody }};font-size:11px;color:#3D3535;text-decoration:none;letter-spacing:1px;display:block;line-height:2;">문의하기</a>
                  </td>
                </tr>
              </table>
              <p style="margin:16px 0 0;padding-top:14px;border-top:1px solid #1A1818;font-family:{{ $fontBody }};font-size:9px;color:#2A2020;letter-spacing:2px;text-align:center;text-transform:uppercase;">
                © {{ date('Y') }} PAC RUN. All rights reserved.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
