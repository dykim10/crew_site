# CREW 프로젝트 정의서

> 원본 공통 정의서: `C:\src\projects\.claude\project-definition.md`
> 최종 수정: 2026-05-19
> 이 파일은 CREW(Laravel) 프로젝트에 특화된 정의입니다.
> 기획 원문: `C:\src\projects\ADMIN-plan_1.md`, `C:\src\projects\PLAN-crew.md`

---

## 이 프로젝트의 역할

러닝 크루 구성원의 **기록 관리 + 이벤트 점수 + 커뮤니티 플랫폼**

- 러닝 이미지 업로드 → CORE API 파싱 → 기록 자동 저장
- 개인 / 조별 기록 관리 및 통계
- 이벤트 점수 관리 (마일리지 챌린지 / 미션 이벤트)
- 기수 신청서 (공개 페이지, 비회원 접근 가능)
- 게시판 (자유 / 기수 / 지역 / 이벤트)
- 관리자: 구성원 관리 / 그룹 편성 / 단체 문자 / 설문조사 / 통계

---

## 기술 스택

| 구분 | 기술 |
|---|---|
| 언어 | PHP 8.3+ |
| 프레임워크 | Laravel 13 |
| DB | Supabase PostgreSQL |
| 프론트 | Blade + Tailwind CSS (pac 커스텀 컬러) + Alpine.js |
| HTTP 클라이언트 | Guzzle (CORE API 호출) |
| 이미지 파싱 | CORE API `/api/parse-image` 경유 |
| 문자 발송 | 솔라피 API |
| 설문 | Google Forms API v1 (CORE 경유) |

---

## 경로

| 구분 | 경로 |
|---|---|
| EC2 서버 | `/var/www/running-crew/` |
| 로컬 | `C:\src\projects\crew\` |
| GitHub | `https://github.com/dykim10/crew_site.git` |
| 로컬 포트 | 8002 |

---

## 조직 계층 구조 (확정)

```
1차: 기수 (generation)         → 1기, 2기, 3기... (필수)
2차: 지역 (region)             → 반포, 연대, 인천, 군포 (필수)
3차: 조   (group)              → A조, B조... 5~15명 (이벤트/점수용, 관리자 배정)
4차: 등급 (grade)              → A / B / C (조 배정 시 자동 매칭)
5차: 훈련그룹 (training_group)  → S / 1~7조 (사용자 직접 선택, 이벤트와 무관)
```

**조 ↔ 등급 자동 매칭**
```
A조 → grade: A → 마일리지 목표 120km
B조 → grade: B → 마일리지 목표 100km
C조 → grade: C → 마일리지 목표 80km
```

---

## Role 권한 체계 (확정)

### 시스템 권한 4단계 (public.users.role)

| Role | 코드 | 범위 |
|---|---|---|
| 슈퍼관리자 | `super_admin` | 전체 시스템 |
| 지역관리자 | `region_admin` | 담당 지역 내 전체 |
| 운영자 | `operator` | 기능별 부분 권한 (슈퍼/지역관리자 지정) |
| 일반멤버 | `member` | 본인 기록/정보만 (기본값) |

> 기존 `crew_admin / group_admin` → `region_admin / operator` 로 변경됨. 마이그레이션 필요.

### 명예직 뱃지 (crew.users_detail.badges JSONB, 시스템 권한 없음)

| 뱃지 | 코드 |
|---|---|
| 페이서 | `pacer` |
| 멤버대장 | `group_leader` |

예시: `{"pacer": true, "group_leader": true}`

### 주요 권한별 기능 범위

| 기능 | 슈퍼 | 지역 | 운영자 | 멤버 |
|---|---|---|---|---|
| 전체 회원 관리 (수정/삭제) | ✅ | ❌ | ❌ | ❌ |
| 전체 회원 조회 | ✅ | ✅ | ✅ | ❌ |
| 담당 지역 회원 관리 | ✅ | ✅ | ❌ | ❌ |
| role 변경 | ✅ | ✅(담당지역) | ❌ | ❌ |
| 뱃지 부여 | ✅ | ✅ | ✅ | ❌ |
| 전체 공지 작성 | ✅ | ✅ | ❌ | ❌ |
| 지역 공지 작성 | ✅ | ✅ | ✅(담당지역) | ❌ |
| 이벤트 생성 (메인) | ✅ | ❌ | ❌ | ❌ |
| 이벤트 생성 (서브/독립) | ✅ | ✅ | ✅ | ❌ |
| 그룹 편성 | ✅ | ✅ | ✅ | ❌ |
| 모집 신청서 관리 | ✅ | ✅ | ✅ | ❌ |
| 단체 문자 발송 | ✅ | ✅ | ❌ | ❌ |
| 기수 관리 (CRUD) | ✅ | ❌ | ❌ | ❌ |
| 통계/엑셀 | ✅ | ✅ | ❌ | ❌ |

> 운영자 권한은 토글로 유연하게 조정 가능하게 설계 (crew.operator_permissions)

---

## 이벤트 구조 (확정)

### 이벤트 타입 3종

| type | 이름 | 설명 |
|---|---|---|
| `main` | 메인 이벤트 | "친해지길바래" — 기수 아이스브레이킹 전용, 그룹 편성 연동 |
| `sub` | 서브 이벤트 | 메인 이벤트에 종속 (parent_event_id 필수) |
| `standalone` | 독립 이벤트 | 메인-서브 관계 없이 단독 운영 |

### 이벤트 계층
```
[메인] type='main', parent_event_id=NULL
  ├── [서브] type='sub' — 마일리지 챌린지
  ├── [서브] type='sub' — 주말 인증샷 미션
  └── [서브] type='sub' — 특별런

[독립] type='standalone', parent_event_id=NULL — 번개런, 기념 이벤트 등
```

### 점수 산정 방식

| score_type | 방식 | 설명 |
|---|---|---|
| `mileage_grade` | 등급별 마일리지 달성 | 등급별 goal_km 달성 → 자동 부여 |
| `fixed` | 고정 점수 | 관리자 심사 승인 후 부여 |
| `NULL` | 점수 없음 | 메인 이벤트 (서브 합산으로 집계) |

---

## DB 스키마 전체

### public 스키마 (공통)

```
users              : 통합 회원 (role: super_admin/region_admin/operator/member)
crews              : 크루
generations        : 기수 (recruit_start_at, recruit_end_at, is_recruiting, goal_km_grades, max_members)
regions            : 지역 (반포/연대/인천/군포)
```

> `branches / groups` 테이블은 기존 구조 — 신규 기수 운영은 `generations / regions / crew.event_groups` 로 대체

### crew 스키마 (CREW 전용)

**기존 테이블**
```
running_logs       : 러닝 기록 (distance_km, duration_seconds, avg_pace_seconds,
                     best_pace_seconds, is_indoor, calories, avg_heart_rate,
                     elevation_m, image_url, parsed_data JSONB)
events             : 이벤트 (+ type, parent_event_id, score_type, score_config JSONB,
                     allow_partial, require_entry, rules JSONB 컬럼 추가 예정)
event_scores       : 이벤트 점수
user_goals         : 개인 목표 마일리지
```

**신규 테이블 목록**
```
users_detail       : CREW 전용 회원 추가정보
                     (generation_id, region_id, grade, training_group,
                      badges JSONB, admin_memo)

event_groups       : 그룹 편성 (generation_id, event_id, group_no, group_name,
                     leader_user_id)
event_group_members: 그룹 구성원 (group_id, user_id)

notices            : 공지사항 (title, content, author_id, is_pinned,
                     target_type, target_ids JSONB)
notice_reads       : 공지 읽음 기록 (notice_id, user_id)

boards             : 게시판 (type: free/generation/region/event,
                     target_id, title, content, author_id, view_count)

applications       : 기수 지원 신청서 (name_enc, email_enc, phone_enc,
                     generation_id, region_pref, running_career, motivation,
                     agree_privacy, agree_terms, agree_refund, status,
                     email_hash)
application_history: 지원 이력 (email_hash, generation_id, status — 개인정보 제외)

sms_logs           : 단체 문자 발송 로그 (sender_id, recipient_count,
                     message, result JSONB)

surveys            : 설문조사 (google_form_id, google_form_url, target_type,
                     target_ids JSONB, status, created_by)
survey_responses   : 설문 응답 (survey_id, user_id, response_id, answers JSONB)

admin_logs         : 관리자 액션 로그 (admin_id, action, target_type,
                     target_id, before_data JSONB, after_data JSONB)
```

---

## 관리자 메뉴 구조 (/admin)

```
/admin
├── 회원 관리
│   ├── 구성원 목록          /admin/members
│   ├── 등급/뱃지 관리       /admin/members/roles
│   └── 모집 신청서 관리     /admin/applications
│
├── 기수 관리
│   ├── 기수 목록/설정       /admin/generations       (슈퍼만)
│   └── 그룹 편성 (D&D)     /admin/generations/{id}/groups
│
├── 이벤트 관리
│   ├── 이벤트 목록          /admin/events
│   ├── 메인 이벤트 생성     /admin/events/create?type=main    (슈퍼만)
│   ├── 서브/독립 이벤트 생성 /admin/events/create?type=sub|standalone
│   └── 응모 심사           /admin/events/{id}/entries
│
├── 게시판 관리
│   ├── 공지사항             /admin/notices
│   └── 게시판               /admin/boards
│
├── 소통
│   ├── 단체 문자 발송       /admin/sms
│   └── 설문조사 (구글폼)    /admin/surveys
│
└── 통계
    ├── 기록 통계            /admin/stats
    └── 엑셀 다운로드        /admin/export
```

---

## 기수 신청서 흐름 (공개 페이지)

```
URL: crew.pac-run.com/apply  (비회원 접근 가능)

신청자 폼 제출
  ↓
email_hash 로 중복 체크 → 기존 신청 있으면 불러오기 / 없으면 신규 INSERT
  ↓
crew.applications 저장 (status: pending)
  ↓
관리자 검토 → 승인
  ↓
초대 코드 자동 생성
  ↓
이메일(SES) + 문자(솔라피) 동시 발송

거절 처리 시:
  crew.application_history 저장 (email_hash + status)
  crew.applications 개인정보 컬럼 NULL 처리
  다음 기수 재신청 가능
```

---

## 모집 기간 관리

```
generations.is_recruiting = TRUE  → 신청 폼 활성화
generations.is_recruiting = FALSE → "현재 모집 중인 기수가 없습니다"
모집 마감일(recruit_end_at) 초과 → 자동 비활성화 (CORE 스케줄러)
MAX_RECRUIT_PER_GENERATION = 100 (상수 관리)
```

---

## 게시판 접근 제어

| 게시판 | 작성 | 조회 |
|---|---|---|
| 자유게시판 | 전체 멤버 | 전체 멤버 |
| 기수 게시판 | 해당 기수 멤버 | 해당 기수 멤버 |
| 지역 게시판 | 해당 지역 멤버 | 해당 지역 멤버 |
| 이벤트 게시판 | 이벤트 참여자 | 이벤트 참여자 |

---

## CORE API 호출

| 메서드 | 엔드포인트 | 용도 |
|---|---|---|
| POST | `http://localhost:8000/api/parse-image` | 러닝 이미지 파싱 |
| GET | `http://localhost:8000/api/surveys/responses` | 구글폼 응답 수집 (예정) |

---

## 개발 우선순위 (v1)

```
1. Laravel 기본 설치 및 Supabase 연결               ✅ 완료 (2026-05-17)
2. 회원 인증 (초대 코드 기반 클로즈 베타)             ✅ 완료 (2026-05-17)
3. PAC-RUN 디자인 시스템 적용 (레이아웃/컬러/폰트)    ✅ 완료 (2026-05-19)
4. 대시보드 뷰 구현                                  ← 다음 (PAGE-dashboard.md 기준)
5. DB 마이그레이션 (신규 테이블 일괄 생성)
6. Role 4단계 재정의 + 마이그레이션
7. 기수 신청서 공개 페이지 (/apply)
8. 관리자 레이아웃 + 구성원 목록/관리
9. 이벤트 관리 (main/sub/standalone)
10. 러닝 이미지 파싱 실테스트
11. 게시판 (자유/기수/지역/이벤트)
12. 단체 문자 발송 (솔라피)
```

---

## v1 완료 기능 상세 (2026-05-19 기준)

```
- Laravel 13 + Breeze (Blade) 설치
- Supabase pooler 연결 (search_path=public,crew)
- crew 스키마 생성: running_logs / events / event_scores / user_goals
- 초대 코드 기반 회원가입 (VALID_INVITE_CODES 상수)
- User 모델: public.users 공유 테이블, isAdmin() 헬퍼
- RunningLog CRUD (Controller → Service → Model 3계층)
- 러닝 이미지 업로드 → CORE API 파싱 → 기록 저장 (구현 완료, 실테스트 미완)
- EC2 배포 + Nginx HTTPS 설정
- [2026-05-19] PAC-RUN 디자인 시스템 적용
  - tailwind.config.js: pac-yellow/pac-black/pac-pink/pac-green/pac-red
  - 폰트: Barlow Condensed(display) + Noto Sans KR(body)
  - layouts/app.blade.php, navigation.blade.php, guest.blade.php 교체
  - 폼 컴포넌트 전체 pac 컬러 적용
```

---

## 디자인 참고 문서

```
C:\src\projects\crew-site-sample_design\
  DESIGN-SYSTEM.md   — 컬러/폰트/컴포넌트/반응형 가이드
  DESIGN-REVIEW.md   — 검수 체크리스트
  PAGE-dashboard.md  — 대시보드 기획서
```

---

## 주의사항

- DB 비밀번호 / API Key `.env` 관리 / Git 커밋 금지
- 운영 환경에서 `APP_DEBUG=false` 필수
- CORE API 호출 실패 시 예외 처리 필수
- 이미지는 S3 저장 후 URL만 DB에 기록
- 개인정보(이름/이메일/휴대폰)는 암호화 저장 (`_enc` 컬럼), 평문 저장 금지
- `public.users` 등 기존 Supabase 테이블은 `Schema::table` 로만 수정, DROP/CREATE 금지
- 마이그레이션은 `IF NOT EXISTS` / `hasColumn` 으로 멱등성 보장
