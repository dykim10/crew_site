<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>이벤트 A타입 기획초안 — PAC-RUN CREW</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --yellow: #E5AD16;
    --black:  #1A1212;
    --g100: #f5f5f5; --g200: #e8e8e8; --g400: #aaa; --g600: #666; --g800: #333;
    --green: #16a34a; --red: #dc2626; --blue: #2563eb;
  }
  body { font-family:'Noto Sans KR',system-ui,sans-serif; font-size:14px; line-height:1.7; color:var(--black); background:#fff; padding:0 0 80px; }

  .doc-header { border-top:4px solid var(--yellow); padding:40px 0 32px; border-bottom:1px solid var(--g200); margin-bottom:48px; }
  .inner { max-width:760px; margin:0 auto; padding:0 24px; }
  .doc-tag { font-family:'IBM Plex Mono',monospace; font-size:11px; font-weight:500; letter-spacing:.12em; text-transform:uppercase; color:var(--yellow); margin-bottom:12px; }
  .doc-title { font-size:26px; font-weight:700; letter-spacing:-.02em; color:var(--black); line-height:1.25; margin-bottom:10px; }
  .doc-meta { font-size:12px; color:var(--g400); display:flex; gap:16px; flex-wrap:wrap; align-items:center; margin-top:16px; }
  .status-chip { display:inline-block; font-size:11px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; padding:2px 8px; border-radius:2px; background:#fef3c7; color:#92400e; }

  .section { margin-bottom:52px; }
  .section-num { font-family:'IBM Plex Mono',monospace; font-size:11px; color:var(--yellow); font-weight:500; letter-spacing:.1em; margin-bottom:6px; }
  .section-title { font-size:17px; font-weight:700; color:var(--black); border-bottom:2px solid var(--black); padding-bottom:8px; margin-bottom:20px; }

  p { color:var(--g800); margin-bottom:12px; }
  p:last-child { margin-bottom:0; }

  table { width:100%; border-collapse:collapse; font-size:13px; margin:16px 0; }
  thead tr { background:var(--g100); }
  th { text-align:left; padding:8px 12px; font-size:11px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:var(--g600); border-bottom:1px solid var(--g200); }
  td { padding:9px 12px; border-bottom:1px solid var(--g200); color:var(--g800); vertical-align:top; }
  tr:last-child td { border-bottom:none; }
  .mono { font-family:'IBM Plex Mono',monospace; font-size:12px; }

  .badge { display:inline-block; font-size:11px; font-weight:700; padding:1px 7px; border-radius:2px; }
  .badge-done   { background:#dcfce7; color:#15803d; }
  .badge-todo   { background:#fee2e2; color:#b91c1c; }
  .badge-unsure { background:#fef3c7; color:#92400e; }

  .flow { background:var(--g100); border-left:3px solid var(--yellow); border-radius:0 4px 4px 0; padding:16px 20px; margin:16px 0; font-size:13px; }
  .flow-step { display:flex; gap:12px; align-items:flex-start; padding:6px 0; border-bottom:1px dashed var(--g200); }
  .flow-step:last-child { border-bottom:none; }
  .flow-num { font-family:'IBM Plex Mono',monospace; font-size:11px; font-weight:500; color:var(--yellow); background:var(--black); padding:1px 6px; border-radius:2px; flex-shrink:0; margin-top:2px; }
  .flow-desc { color:var(--g800); line-height:1.5; }
  .flow-sub  { font-size:12px; color:var(--g400); margin-top:2px; }

  .callout { border-left:3px solid var(--blue); background:#eff6ff; padding:12px 16px; border-radius:0 4px 4px 0; font-size:13px; color:#1e3a5f; margin:16px 0; }
  .callout.warn { border-color:var(--yellow); background:#fffbeb; color:#78350f; }

  /* 설문 폼 */
  .survey-form { border:1px solid var(--g200); border-radius:6px; overflow:hidden; margin-top:16px; }
  .survey-form-head { background:var(--black); color:#fff; padding:16px 20px; }
  .survey-form-head h3 { font-size:14px; font-weight:700; letter-spacing:-.01em; }
  .survey-form-head p  { font-size:12px; color:#aaa; margin-top:4px; }
  .survey-body { padding:20px; display:flex; flex-direction:column; gap:16px; }
  .field-group label { display:block; font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; margin-bottom:6px; }
  .field-group label.good { color:var(--green); }
  .field-group label.bad  { color:var(--red); }
  .field-group label.q    { color:var(--blue); }
  .field-group label.name { color:var(--g600); }
  .field-group input,
  .field-group textarea {
    width:100%; border:1px solid var(--g200); border-radius:4px;
    padding:9px 12px; font-family:'Noto Sans KR',system-ui,sans-serif;
    font-size:13px; color:var(--g800); background:#fff; outline:none;
    transition:border-color .15s;
  }
  .field-group input:focus,
  .field-group textarea:focus { border-color:var(--yellow); }
  .field-group textarea { resize:vertical; min-height:72px; }
  .field-group .hint { font-size:11px; color:var(--g400); margin-top:4px; }
  .submit-btn {
    background:var(--black); color:#fff; border:none; border-radius:4px;
    padding:10px 24px; font-family:'Noto Sans KR',system-ui,sans-serif;
    font-size:13px; font-weight:700; cursor:pointer; letter-spacing:.02em;
    transition:background .15s;
  }
  .submit-btn:hover { background:var(--yellow); color:var(--black); }
  .error-msg { font-size:12px; color:var(--red); margin-top:4px; }

  /* 알림 */
  .alert-success { background:#dcfce7; border:1px solid #bbf7d0; border-radius:4px; padding:12px 16px; font-size:13px; color:#15803d; margin-bottom:20px; }
  .alert-error   { background:#fee2e2; border:1px solid #fecaca; border-radius:4px; padding:12px 16px; font-size:13px; color:var(--red); margin-bottom:20px; }

  /* 종합 응답 */
  .response-count { font-family:'IBM Plex Mono',monospace; font-size:22px; font-weight:500; color:var(--black); }
  .response-card { border:1px solid var(--g200); border-radius:4px; overflow:hidden; margin-bottom:12px; }
  .response-card-head { background:var(--g100); padding:8px 14px; display:flex; justify-content:space-between; align-items:center; }
  .response-card-name { font-size:13px; font-weight:700; color:var(--black); }
  .response-card-date { font-family:'IBM Plex Mono',monospace; font-size:11px; color:var(--g400); }
  .response-card-body { padding:14px; display:flex; flex-direction:column; gap:10px; }
  .response-item-label { font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; margin-bottom:3px; }
  .response-item-label.good { color:var(--green); }
  .response-item-label.bad  { color:var(--red); }
  .response-item-label.q    { color:var(--blue); }
  .response-item-text { font-size:13px; color:var(--g800); white-space:pre-wrap; line-height:1.6; }
  .no-response { text-align:center; padding:32px; background:var(--g100); border-radius:4px; font-size:13px; color:var(--g400); }

  .doc-footer { max-width:760px; margin:60px auto 0; padding:24px 24px 0; border-top:1px solid var(--g200); font-size:12px; color:var(--g400); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; }

  @media (max-width:600px) {
    .doc-title { font-size:20px; }
    .section-title { font-size:15px; }
  }
</style>
</head>
<body>

<div class="doc-header">
  <div class="inner">
    <div class="doc-tag">PAC-RUN CREW · 기획초안 v0.1</div>
    <h1 class="doc-title">이벤트 A타입<br>마일리지 경쟁형 시스템</h1>
    <p style="color:var(--g600);font-size:13px;margin-top:10px;">
      기수 구성원이 일정 기간 동안 러닝 거리를 누적해 등급별 목표 km를 달성하는 경쟁형 이벤트.
    </p>
    <div class="doc-meta">
      <span>작성일: 2026-05-30</span>
      <span>·</span>
      <span>작성: Claude (초안)</span>
      <span>·</span>
      <span class="status-chip">검토 중</span>
    </div>
  </div>
</div>

<div class="inner">

  @if(session('success'))
    <div class="alert-success">✓ {{ session('success') }}</div>
  @endif
  @if($errors->has('general'))
    <div class="alert-error">{{ $errors->first('general') }}</div>
  @endif

  <!-- 01 -->
  <div class="section">
    <div class="section-num">01</div>
    <h2 class="section-title">이벤트 A타입이란?</h2>
    <p>A타입은 <strong>마일리지 경쟁형</strong> 이벤트입니다. 관리자가 이벤트 기간과 등급별 목표 거리를 설정하면, 구성원은 해당 기간 동안 러닝 기록을 올려 목표 km 달성 여부를 경쟁합니다.</p>
    <p>B타입(신청서 기반)과 달리 별도 신청 없이, 러닝 기록만 올리면 자동으로 집계됩니다.</p>
    <table>
      <thead><tr><th>구분</th><th>A타입</th><th>B타입 (비교)</th></tr></thead>
      <tbody>
        <tr><td>진입 방식</td><td>러닝 기록 업로드만으로 자동 참여</td><td>신청서 제출 필요</td></tr>
        <tr><td>점수 산정</td><td>등급별 목표 km 달성 → 자동/수동 부여</td><td>관리자 심사 후 고정 점수</td></tr>
        <tr><td>핵심 데이터</td><td>running_logs.distance_km 누적</td><td>event_registrations.form_data</td></tr>
        <tr><td>관리자 개입</td><td>기록 검수 확인 (is_confirmed)</td><td>신청 내용 검토 + 점수 입력</td></tr>
      </tbody>
    </table>
  </div>

  <!-- 02 -->
  <div class="section">
    <div class="section-num">02</div>
    <h2 class="section-title">핵심 데이터 구조</h2>
    <p>이벤트 생성 시 관리자가 설정하는 값:</p>
    <table>
      <thead><tr><th>컬럼</th><th>예시값</th><th>설명</th></tr></thead>
      <tbody>
        <tr><td class="mono">event_type</td><td>A</td><td>이벤트 종류 구분</td></tr>
        <tr><td class="mono">score_type</td><td>mileage_grade</td><td>등급별 달성 방식</td></tr>
        <tr><td class="mono">start_date</td><td>2026-05-01</td><td>집계 시작일</td></tr>
        <tr><td class="mono">end_date</td><td>2026-05-31</td><td>집계 종료일</td></tr>
        <tr>
          <td class="mono">score_config</td>
          <td class="mono" style="font-size:11px;">[{grade:A, target_km:120},<br>{grade:B, target_km:100},<br>{grade:C, target_km:80}]</td>
          <td>등급별 목표 거리 (JSONB)</td>
        </tr>
      </tbody>
    </table>
    <p style="margin-top:16px;">구성원 등급은 <span class="mono">crew.users_detail.grade</span> (A/B/C)에서 읽어옵니다.</p>
    <div class="callout warn">⚠️ 관리자가 기록을 <strong>검수 확인</strong>하기 전까지는 마일리지에 반영되지 않습니다.</div>
  </div>

  <!-- 03 -->
  <div class="section">
    <div class="section-num">03</div>
    <h2 class="section-title">현재 구현된 흐름</h2>
    <div class="flow">
      <div class="flow-step">
        <div class="flow-num">01</div>
        <div class="flow-desc"><strong>관리자</strong> — Filament /admin에서 A타입 이벤트 생성<div class="flow-sub">이름, 기간, 등급별 목표 km, 참여 대상 설정</div></div>
      </div>
      <div class="flow-step">
        <div class="flow-num">02</div>
        <div class="flow-desc"><strong>구성원</strong> — 러닝 앱 스크린샷 업로드<div class="flow-sub">CORE API → Claude Vision 파싱 → 임시 저장 (is_confirmed = false)</div></div>
      </div>
      <div class="flow-step">
        <div class="flow-num">03</div>
        <div class="flow-desc"><strong>구성원</strong> — 파싱 결과 확인 후 제출 확정<div class="flow-sub">수정 가능, 최종 confirm 후 관리자 검수 대기</div></div>
      </div>
      <div class="flow-step">
        <div class="flow-num">04</div>
        <div class="flow-desc"><strong>관리자</strong> — Filament /admin → 러닝 기록 검수 확인<div class="flow-sub">is_confirmed = true, user_goals 재집계</div></div>
      </div>
      <div class="flow-step">
        <div class="flow-num">05</div>
        <div class="flow-desc"><strong>구성원</strong> — /events/{id} 상세 페이지에서 현황 확인<div class="flow-sub">내 마일리지 / 목표 km / 프로그레스 바 표시</div></div>
      </div>
      <div class="flow-step" style="background:#fffbeb;">
        <div class="flow-num" style="background:var(--yellow);color:var(--black);">?</div>
        <div class="flow-desc"><strong>자동 점수 부여</strong> — 달성 시 event_scores 기록<div class="flow-sub">⚠️ 미확인: 달성 감지 후 자동 INSERT 로직 연결 여부 확인 필요</div></div>
      </div>
    </div>
  </div>

  <!-- 04 -->
  <div class="section">
    <div class="section-num">04</div>
    <h2 class="section-title">구현 현황 체크리스트</h2>
    <table>
      <thead><tr><th>항목</th><th>상태</th><th>비고</th></tr></thead>
      <tbody>
        <tr><td>이벤트 생성 (Filament)</td><td><span class="badge badge-done">완료</span></td><td>등급별 목표 km, 기간 설정</td></tr>
        <tr><td>이벤트 목록 페이지 (/events)</td><td><span class="badge badge-done">완료</span></td><td>진행중/예정/종료 분류</td></tr>
        <tr><td>A타입 상세 페이지 (/events/{id})</td><td><span class="badge badge-done">완료</span></td><td>프로그레스 바, 달성 배지 표시</td></tr>
        <tr><td>마일리지 집계 계산</td><td><span class="badge badge-done">완료</span></td><td>is_confirmed 기록 기간 내 합산</td></tr>
        <tr><td>달성 시 자동 점수 기록</td><td><span class="badge badge-unsure">미확인</span></td><td>event_scores INSERT 연결 여부</td></tr>
        <tr><td>전체/등급별 순위 집계</td><td><span class="badge badge-todo">미구현</span></td><td>리더보드 화면 없음</td></tr>
        <tr><td>조별 팀 순위 집계</td><td><span class="badge badge-todo">미구현</span></td><td>event_groups 테이블 연동 필요</td></tr>
        <tr><td>이벤트 종료 후 최종 결과</td><td><span class="badge badge-todo">미구현</span></td><td>결과 공표 화면 없음</td></tr>
        <tr><td>달성 알림 발송</td><td><span class="badge badge-todo">미구현</span></td><td>이메일/문자 트리거 없음</td></tr>
      </tbody>
    </table>
  </div>

  <!-- 05 -->
  <div class="section">
    <div class="section-num">05</div>
    <h2 class="section-title">논의가 필요한 지점</h2>
    <table>
      <thead><tr><th>#</th><th>항목</th><th>현재 방향</th><th>대안</th></tr></thead>
      <tbody>
        <tr><td>1</td><td>점수 자동 부여 시점</td><td>관리자 검수 확인 시</td><td>이벤트 종료 후 일괄 정산</td></tr>
        <tr><td>2</td><td>순위 기준</td><td>달성 km 절대값</td><td>목표 대비 달성률(%), 달성 날짜 우선</td></tr>
        <tr><td>3</td><td>개인 vs 조 경쟁</td><td>개인 마일리지만 표시</td><td>조별 합산 → 조 대항전</td></tr>
        <tr><td>4</td><td>미확정 기록 표시</td><td>확정 기록만 반영</td><td>미확정 포함 예상 달성 표시</td></tr>
        <tr><td>5</td><td>리더보드 공개 범위</td><td>미정</td><td>전체 공개 / 같은 등급만 / 비공개</td></tr>
        <tr><td>6</td><td>목표 미달성 처리</td><td>미정</td><td>페널티 없음 / 부분 점수 / 0점</td></tr>
      </tbody>
    </table>
  </div>

  <!-- 06 설문 -->
  <div class="section">
    <div class="section-num">06</div>
    <h2 class="section-title">피드백 제출</h2>
    <p>이 기획초안을 읽고 의견을 남겨주세요. 제출된 내용은 저장되어 기획 보완에 활용됩니다.</p>

    <form method="POST" action="{{ route('testevent_a_type.store') }}" class="survey-form">
      @csrf
      <div class="survey-form-head">
        <h3>의견 작성</h3>
        <p>세 항목 중 해당하는 것만 작성해도 됩니다.</p>
      </div>
      <div class="survey-body">
        <div class="field-group">
          <label class="name">이름 (선택)</label>
          <input type="text" name="reviewer_name" placeholder="익명으로 남겨도 됩니다" value="{{ old('reviewer_name') }}" maxlength="100">
        </div>
        <div class="field-group">
          <label class="good">✓ 좋은 점 / 잘 되어 있는 것</label>
          <textarea name="good_points" placeholder="현재 방향이 맞다고 생각하는 부분, 이미 충분한 기능...">{{ old('good_points') }}</textarea>
          @error('good_points')<div class="error-msg">{{ $message }}</div>@enderror
        </div>
        <div class="field-group">
          <label class="bad">✕ 아쉬운 점 / 바꿔야 할 것</label>
          <textarea name="bad_points" placeholder="흐름이 어색한 부분, 빠진 기능, 다르게 가야 할 방향...">{{ old('bad_points') }}</textarea>
          @error('bad_points')<div class="error-msg">{{ $message }}</div>@enderror
        </div>
        <div class="field-group">
          <label class="q">? 질문 / 확인이 필요한 것</label>
          <textarea name="questions" placeholder="명확하지 않은 부분, 결정이 필요한 것, 궁금한 점...">{{ old('questions') }}</textarea>
          @error('questions')<div class="error-msg">{{ $message }}</div>@enderror
        </div>
        <div>
          <button type="submit" class="submit-btn">의견 제출</button>
        </div>
      </div>
    </form>
  </div>

  <!-- 07 종합 응답 -->
  <div class="section">
    <div class="section-num">07</div>
    <h2 class="section-title">제출된 의견 종합</h2>

    @if($feedbacks->isEmpty())
      <div class="no-response">아직 제출된 의견이 없습니다.</div>
    @else
      <p style="margin-bottom:16px;">
        총 <span class="response-count">{{ $feedbacks->count() }}</span>건의 의견이 제출되었습니다.
      </p>
      @foreach($feedbacks as $fb)
        <div class="response-card">
          <div class="response-card-head">
            <span class="response-card-name">{{ $fb->reviewer_name ?: '익명' }}</span>
            <span class="response-card-date">{{ $fb->created_at?->format('m.d H:i') }}</span>
          </div>
          <div class="response-card-body">
            @if($fb->good_points)
              <div>
                <div class="response-item-label good">✓ 좋은 점</div>
                <div class="response-item-text">{{ $fb->good_points }}</div>
              </div>
            @endif
            @if($fb->bad_points)
              <div>
                <div class="response-item-label bad">✕ 아쉬운 점</div>
                <div class="response-item-text">{{ $fb->bad_points }}</div>
              </div>
            @endif
            @if($fb->questions)
              <div>
                <div class="response-item-label q">? 질문</div>
                <div class="response-item-text">{{ $fb->questions }}</div>
              </div>
            @endif
          </div>
        </div>
      @endforeach
    @endif
  </div>

</div>

<div class="doc-footer">
  <span>PAC-RUN CREW · 이벤트 A타입 기획초안 v0.1</span>
  <span>최종수정: 2026-05-30</span>
</div>

</body>
</html>
