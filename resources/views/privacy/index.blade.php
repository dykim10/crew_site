<x-app-layout>
<div class="max-w-4xl mx-auto px-4 py-12 md:px-6 lg:px-8">

  {{-- 페이지 헤더 --}}
  <div class="mb-12">
    <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mb-4">Legal</p>
    <h1 class="font-display text-[clamp(40px,6vw,64px)] leading-none tracking-wide text-pac-black-900 uppercase">
      개인정보처리방침
    </h1>
    <div class="w-20 h-0.5 bg-pac-yellow-500 mt-6 mb-6"></div>
    <p class="font-body text-sm text-pac-black-600 leading-relaxed">
      시행일자: 2026년 __월 __일<br>
      PAC-RUN(이하 "서비스")은 「개인정보 보호법」 등 관련 법령을 준수하며, 이용자의 개인정보를 안전하게 보호하기 위해 다음과 같이 개인정보처리방침을 수립·공개합니다.
      본 방침은 <strong class="text-pac-black-900">REVIEW(review.pac-run.com)</strong>와 <strong class="text-pac-black-900">CREW(crew.pac-run.com)</strong>에 공통 적용됩니다.
    </p>
  </div>

  <article class="font-body text-sm text-pac-black-600 leading-relaxed space-y-12">

    {{-- 제1조 --}}
    <section>
      <h2 class="font-display text-xl uppercase tracking-wide text-pac-black-900 mb-4">제1조 (수집하는 개인정보 항목 및 수집 방법)</h2>

      <h3 class="font-body text-sm font-bold text-pac-black-800 mb-2">1. 회원가입 시 수집 항목</h3>
      <div class="overflow-x-auto mb-6 border border-pac-black-100">
        <table class="w-full text-xs">
          <thead>
            <tr class="bg-pac-black-900 text-pac-black-500">
              <th class="px-4 py-2 text-left font-display tracking-wider uppercase text-[10px]">구분</th>
              <th class="px-4 py-2 text-left font-display tracking-wider uppercase text-[10px]">항목</th>
              <th class="px-4 py-2 text-left font-display tracking-wider uppercase text-[10px]">필수 여부</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-pac-black-100">
            <tr><td class="px-4 py-2">공통</td><td class="px-4 py-2">이메일 주소, 비밀번호(암호화 저장), 닉네임</td><td class="px-4 py-2">필수</td></tr>
            <tr><td class="px-4 py-2">CREW</td><td class="px-4 py-2">이름, 휴대폰 번호</td><td class="px-4 py-2">필수</td></tr>
            <tr><td class="px-4 py-2">CREW</td><td class="px-4 py-2">기수, 지역, 훈련그룹 등 크루 활동 정보</td><td class="px-4 py-2">필수</td></tr>
            <tr><td class="px-4 py-2">CREW (클로즈 베타)</td><td class="px-4 py-2">초대 코드</td><td class="px-4 py-2">필수</td></tr>
          </tbody>
        </table>
      </div>

      <h3 class="font-body text-sm font-bold text-pac-black-800 mb-2">2. 서비스 이용 과정에서 수집되는 항목</h3>
      <div class="overflow-x-auto mb-6 border border-pac-black-100">
        <table class="w-full text-xs">
          <thead>
            <tr class="bg-pac-black-900 text-pac-black-500">
              <th class="px-4 py-2 text-left font-display tracking-wider uppercase text-[10px]">구분</th>
              <th class="px-4 py-2 text-left font-display tracking-wider uppercase text-[10px]">항목</th>
              <th class="px-4 py-2 text-left font-display tracking-wider uppercase text-[10px]">수집 방식</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-pac-black-100">
            <tr><td class="px-4 py-2">공통</td><td class="px-4 py-2">서비스 이용 기록, 접속 로그, 접속 IP, 쿠키</td><td class="px-4 py-2">자동 수집</td></tr>
            <tr><td class="px-4 py-2">CREW</td><td class="px-4 py-2">러닝 기록(거리, 페이스, 심박수, 칼로리, 고도 등), 러닝 앱 스크린샷 이미지</td><td class="px-4 py-2">이용자 직접 업로드</td></tr>
            <tr><td class="px-4 py-2">CREW</td><td class="px-4 py-2">프로필 사진(아바타)</td><td class="px-4 py-2">이용자 직접 업로드</td></tr>
            <tr><td class="px-4 py-2">REVIEW</td><td class="px-4 py-2">대회 참가 후기, 별점, 완주 기록(시간, 배번호 등)</td><td class="px-4 py-2">이용자 직접 입력</td></tr>
            <tr><td class="px-4 py-2">REVIEW</td><td class="px-4 py-2">기록증 이미지, 워치 스크린샷 이미지</td><td class="px-4 py-2">이용자 직접 업로드</td></tr>
            <tr><td class="px-4 py-2">REVIEW</td><td class="px-4 py-2">GPX/TCX 파일(개인 주행 GPS 경로 — 위치정보 포함)</td><td class="px-4 py-2">이용자 직접 업로드 (별도 동의 후)</td></tr>
          </tbody>
        </table>
      </div>

      <h3 class="font-body text-sm font-bold text-pac-black-800 mb-2">3. GPX/TCX 파일(위치정보)에 관한 특별 안내</h3>
      <ul class="list-disc list-outside pl-5 space-y-1">
        <li>GPX/TCX 파일에는 이용자의 이동 경로, 시각, 심박수 등 민감할 수 있는 정보가 포함됩니다.</li>
        <li>해당 파일 업로드 시 별도의 명시적 동의를 받으며, 동의 시각을 기록·보관합니다.</li>
        <li>원본 파일은 외부 공개되지 않는 비공개 저장소에 보관되며, 파일 업로드 본인만 접근할 수 있습니다.</li>
      </ul>
    </section>

    {{-- 제2조 --}}
    <section>
      <h2 class="font-display text-xl uppercase tracking-wide text-pac-black-900 mb-4">제2조 (개인정보의 처리 목적)</h2>
      <p class="mb-3">서비스는 수집한 개인정보를 다음 목적으로 이용합니다.</p>
      <ol class="list-decimal list-outside pl-5 space-y-2">
        <li><strong class="text-pac-black-800">회원 관리:</strong> 회원 가입 및 본인 확인, 이메일 인증, 부정 이용 방지</li>
        <li><strong class="text-pac-black-800">서비스 제공:</strong> 러닝 기록 관리, 대회 리뷰 작성·조회, 이벤트 참여 및 점수 집계</li>
        <li>
          <strong class="text-pac-black-800">AI 기반 분석 서비스 제공:</strong>
          <ul class="list-disc list-outside pl-5 mt-1 space-y-1">
            <li>러닝 앱 스크린샷·기록증·워치 이미지에서 기록 데이터 자동 추출</li>
            <li>리뷰 요약 및 대회 종합 분석 생성</li>
            <li>GPX 데이터·날씨·과거 완주 기록을 활용한 맞춤형 레이스 플랜 생성</li>
          </ul>
        </li>
        <li><strong class="text-pac-black-800">알림 발송:</strong> 이메일(회원가입 인증, 비밀번호 재설정, 이벤트 안내), 문자메시지(크루 단체 공지)</li>
        <li><strong class="text-pac-black-800">서비스 개선:</strong> 이용 통계 분석, 오류 확인 및 개선</li>
      </ol>
    </section>

    {{-- 제3조 --}}
    <section>
      <h2 class="font-display text-xl uppercase tracking-wide text-pac-black-900 mb-4">제3조 (개인정보의 처리 및 보유 기간)</h2>
      <ol class="list-decimal list-outside pl-5 space-y-2 mb-4">
        <li>서비스는 원칙적으로 회원 탈퇴 시 지체 없이 개인정보를 파기합니다.</li>
        <li>다만 관계 법령에 따라 아래 정보는 명시된 기간 동안 보관합니다.</li>
      </ol>
      <div class="overflow-x-auto mb-4 border border-pac-black-100">
        <table class="w-full text-xs">
          <thead>
            <tr class="bg-pac-black-900 text-pac-black-500">
              <th class="px-4 py-2 text-left font-display tracking-wider uppercase text-[10px]">보관 항목</th>
              <th class="px-4 py-2 text-left font-display tracking-wider uppercase text-[10px]">근거 법령</th>
              <th class="px-4 py-2 text-left font-display tracking-wider uppercase text-[10px]">보관 기간</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-pac-black-100">
            <tr><td class="px-4 py-2">서비스 접속 기록</td><td class="px-4 py-2">통신비밀보호법</td><td class="px-4 py-2">3개월</td></tr>
            <tr><td class="px-4 py-2">소비자 불만 또는 분쟁 처리에 관한 기록</td><td class="px-4 py-2">전자상거래법</td><td class="px-4 py-2">3년</td></tr>
          </tbody>
        </table>
      </div>
      <p>백업 데이터는 최대 7일간 보관 후 자동 삭제됩니다.</p>
    </section>

    {{-- 제4조 --}}
    <section>
      <h2 class="font-display text-xl uppercase tracking-wide text-pac-black-900 mb-4">제4조 (개인정보의 제3자 제공)</h2>
      <p class="mb-2">서비스는 이용자의 개인정보를 제3자에게 제공하지 않습니다. 다만 다음의 경우는 예외로 합니다.</p>
      <ol class="list-decimal list-outside pl-5 space-y-1">
        <li>이용자가 사전에 동의한 경우</li>
        <li>법령의 규정에 의하거나 수사 목적으로 법령에 정해진 절차와 방법에 따라 수사기관의 요구가 있는 경우</li>
      </ol>
    </section>

    {{-- 제5조 --}}
    <section>
      <h2 class="font-display text-xl uppercase tracking-wide text-pac-black-900 mb-4">제5조 (개인정보 처리의 위탁 및 국외 이전)</h2>
      <p class="mb-4">서비스는 안정적인 서비스 제공을 위해 아래와 같이 개인정보 처리를 위탁하고 있으며, 일부 수탁사는 국외 사업자입니다.</p>
      <div class="overflow-x-auto mb-4 border border-pac-black-100">
        <table class="w-full text-xs">
          <thead>
            <tr class="bg-pac-black-900 text-pac-black-500">
              <th class="px-4 py-2 text-left font-display tracking-wider uppercase text-[10px]">수탁 업체</th>
              <th class="px-4 py-2 text-left font-display tracking-wider uppercase text-[10px]">위탁 업무</th>
              <th class="px-4 py-2 text-left font-display tracking-wider uppercase text-[10px]">이전 국가 / 보관 위치</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-pac-black-100">
            <tr><td class="px-4 py-2">Amazon Web Services, Inc. (AWS)</td><td class="px-4 py-2">서버 운영, 파일(이미지·GPX) 저장</td><td class="px-4 py-2">대한민국 (서울 리전)</td></tr>
            <tr><td class="px-4 py-2">Supabase, Inc.</td><td class="px-4 py-2">데이터베이스 운영</td><td class="px-4 py-2">대한민국 (서울 리전)</td></tr>
            <tr><td class="px-4 py-2">Anthropic, PBC</td><td class="px-4 py-2">AI 분석 처리 (이미지 파싱, 리뷰 요약, 레이스 플랜 생성)</td><td class="px-4 py-2">미국</td></tr>
            <tr><td class="px-4 py-2">OpenAI, L.L.C.</td><td class="px-4 py-2">텍스트 임베딩 생성 (유사 사례 검색용)</td><td class="px-4 py-2">미국</td></tr>
            <tr><td class="px-4 py-2">Resend, Inc.</td><td class="px-4 py-2">이메일 발송</td><td class="px-4 py-2">미국</td></tr>
            <tr><td class="px-4 py-2">솔라피㈜ (Solapi)</td><td class="px-4 py-2">문자메시지 발송</td><td class="px-4 py-2">대한민국</td></tr>
          </tbody>
        </table>
      </div>
      <ul class="list-disc list-outside pl-5 space-y-1">
        <li>국외 이전 항목: 서비스 이용 과정에서 생성·업로드된 데이터 중 각 위탁 업무 수행에 필요한 최소한의 정보</li>
        <li>이전 방법: 서비스 이용 시점에 네트워크(API)를 통한 전송</li>
        <li>이용자는 국외 이전을 거부할 수 있으나, 이 경우 해당 기능(AI 분석, 이메일 수신 등)의 이용이 제한될 수 있습니다.</li>
      </ul>
    </section>

    {{-- 제6조 --}}
    <section>
      <h2 class="font-display text-xl uppercase tracking-wide text-pac-black-900 mb-4">제6조 (개인정보의 파기 절차 및 방법)</h2>
      <ol class="list-decimal list-outside pl-5 space-y-2">
        <li><strong class="text-pac-black-800">파기 절차:</strong> 보유 기간이 경과하거나 처리 목적이 달성된 개인정보는 지체 없이 파기합니다.</li>
        <li>
          <strong class="text-pac-black-800">파기 방법:</strong>
          <ul class="list-disc list-outside pl-5 mt-1 space-y-1">
            <li>전자적 파일: 복구할 수 없는 기술적 방법으로 영구 삭제</li>
            <li>저장소(S3)에 보관된 이미지·GPX 파일: 원본 및 변환본(썸네일 등) 일괄 삭제</li>
          </ul>
        </li>
      </ol>
    </section>

    {{-- 제7조 --}}
    <section>
      <h2 class="font-display text-xl uppercase tracking-wide text-pac-black-900 mb-4">제7조 (이용자 및 법정대리인의 권리와 행사 방법)</h2>
      <ol class="list-decimal list-outside pl-5 space-y-2">
        <li>이용자는 언제든지 자신의 개인정보를 조회·수정·삭제·처리정지 요구할 수 있습니다.</li>
        <li>권리 행사는 서비스 내 프로필 설정 또는 개인정보 보호책임자에게 이메일로 요청할 수 있으며, 서비스는 지체 없이 조치합니다.</li>
        <li>만 14세 미만 아동의 회원가입은 받지 않습니다.</li>
      </ol>
    </section>

    {{-- 제8조 --}}
    <section>
      <h2 class="font-display text-xl uppercase tracking-wide text-pac-black-900 mb-4">제8조 (개인정보의 안전성 확보 조치)</h2>
      <p class="mb-2">서비스는 개인정보 보호를 위해 다음과 같은 조치를 취하고 있습니다.</p>
      <ol class="list-decimal list-outside pl-5 space-y-1">
        <li><strong class="text-pac-black-800">개인정보 암호화:</strong> 이름·이메일·휴대폰 번호는 암호화하여 저장하며, 비밀번호는 일방향 암호화(해시)로 저장되어 복원이 불가능합니다.</li>
        <li><strong class="text-pac-black-800">접근 통제:</strong> 데이터베이스 접근 권한 최소화, 관리자 기능 접근 제한</li>
        <li><strong class="text-pac-black-800">전송 구간 암호화:</strong> 전체 서비스 HTTPS(TLS) 적용</li>
        <li><strong class="text-pac-black-800">비공개 파일 접근 제어:</strong> GPX 원본 등 민감 파일은 비공개 저장소에 보관하고, 소유자 본인에게만 시간제한 접근 링크를 발급</li>
        <li><strong class="text-pac-black-800">정기 백업 및 보안 점검:</strong> 일일 자동 백업, 의존성 보안 취약점 정기 스캔</li>
      </ol>
    </section>

    {{-- 제9조 --}}
    <section>
      <h2 class="font-display text-xl uppercase tracking-wide text-pac-black-900 mb-4">제9조 (쿠키의 설치·운영 및 거부)</h2>
      <ol class="list-decimal list-outside pl-5 space-y-2">
        <li>서비스는 로그인 세션 유지 등 서비스 제공에 필수적인 쿠키를 사용합니다.</li>
        <li>이용자는 웹 브라우저 설정을 통해 쿠키 저장을 거부할 수 있으나, 이 경우 로그인이 필요한 서비스 이용에 제한이 있을 수 있습니다.</li>
      </ol>
    </section>

    {{-- 제10조 --}}
    <section>
      <h2 class="font-display text-xl uppercase tracking-wide text-pac-black-900 mb-4">제10조 (개인정보 보호책임자)</h2>
      <div class="overflow-x-auto mb-4 border border-pac-black-100">
        <table class="w-full text-xs">
          <tbody class="divide-y divide-pac-black-100">
            <tr><td class="px-4 py-2 w-1/3 text-pac-black-500">개인정보 보호책임자</td><td class="px-4 py-2">[이름]</td></tr>
            <tr><td class="px-4 py-2 text-pac-black-500">직책</td><td class="px-4 py-2">운영자</td></tr>
            <tr><td class="px-4 py-2 text-pac-black-500">연락처</td><td class="px-4 py-2">[이메일 주소]</td></tr>
          </tbody>
        </table>
      </div>
      <p class="mb-2">기타 개인정보 침해에 대한 신고나 상담이 필요한 경우 아래 기관에 문의할 수 있습니다.</p>
      <ul class="list-disc list-outside pl-5 space-y-1">
        <li>개인정보침해 신고센터 (privacy.kisa.or.kr / 국번없이 118)</li>
        <li>개인정보 분쟁조정위원회 (kopico.go.kr / 1833-6972)</li>
        <li>대검찰청 사이버수사과 (spo.go.kr / 국번없이 1301)</li>
        <li>경찰청 사이버수사국 (ecrm.police.go.kr / 국번없이 182)</li>
      </ul>
    </section>

    {{-- 제11조 --}}
    <section>
      <h2 class="font-display text-xl uppercase tracking-wide text-pac-black-900 mb-4">제11조 (개인정보처리방침의 변경)</h2>
      <ol class="list-decimal list-outside pl-5 space-y-2 mb-4">
        <li>본 방침은 법령·정책 또는 서비스 변경에 따라 수정될 수 있습니다.</li>
        <li>변경 시 시행 7일 전(이용자 권리에 중대한 변경이 있는 경우 30일 전)부터 서비스 내 공지사항을 통해 고지합니다.</li>
      </ol>
      <p class="text-xs text-pac-black-500">
        공고일자: 2026년 __월 __일<br>
        시행일자: 2026년 __월 __일
      </p>
    </section>

  </article>
</div>
</x-app-layout>
