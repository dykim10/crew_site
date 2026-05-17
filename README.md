# CREW — 러닝 크루 기록 관리 플랫폼

> **"함께 달린 기록을 데이터로"**
> 러닝 크루 구성원의 개인 기록을 자동으로 수집하고, 이벤트 점수로 동기부여하는 플랫폼입니다.

- URL: https://crew.pac-run.com (예정)
- GitHub: https://github.com/dykim10/crew_site.git
- 공통 정의서: `../project-definition.md`

> **현재 상태: 기획 완료 / 개발 예정**

---

## 프로젝트 배경

러닝 크루는 함께 달리는 사람들의 모임입니다. 그러나 각자 다른 앱(Nike Run Club, Strava, 가민 등)을 사용하기 때문에 크루 차원의 통합 기록 관리가 어렵습니다.

CREW는 이 문제를 세 가지로 접근합니다.
1. **이미지 업로드 한 장으로 기록 등록** — 앱 스크린샷을 찍어 올리면 AI가 기록을 자동 추출
2. **개인 / 조별 기록 시각화** — 누가 얼마나 달렸는지 한눈에 확인
3. **이벤트 점수 시스템** — 목표 달성·이벤트 참여로 점수를 쌓아 동기부여

---

## 시스템 아키텍처

```
[사용자 브라우저]
       │
       ▼
[CREW (Laravel)]  ─── 기록 관리 / 이벤트 / 통계 UI
       │
       │ HTTP (Guzzle)
       ▼
[CORE API (Python)]  ─── 이미지 AI 파싱 (GPT-4o Vision)
       │
       ▼
[Supabase PostgreSQL + S3]
```

**핵심 흐름**
```
1. 사용자가 러닝 앱 스크린샷 업로드
2. S3에 이미지 저장 (URL만 DB에 기록)
3. CORE API /api/parse-image 호출
4. GPT-4o Vision이 거리 / 페이스 / 시간 / 칼로리 등 자동 추출
5. running_logs 테이블에 저장
6. 개인 목표 달성 여부 체크 → 이벤트 점수 자동 부여
```

---

## 기술 스택

| 구분 | 기술 |
|---|---|
| 언어 | PHP 8.3+ |
| 프레임워크 | Laravel 13 |
| DB | Supabase PostgreSQL |
| 프론트 | Blade + Tailwind CSS + Alpine.js |
| 이미지 저장 | AWS S3 + CloudFront |
| 이미지 파싱 | CORE API (GPT-4o Vision) |
| 인증 | Laravel Breeze + 초대 코드 |

---

## 조직 계층

```
crews (크루)
  └── branches (지부)
        └── groups (그룹)
              └── users (구성원)
```

---

## DB 스키마

```
public 스키마 (공통)
├── users          : 통합 회원 (REVIEW와 공유)
├── crews          : 크루
├── branches       : 지부
└── groups         : 그룹

crew 스키마 (CREW 전용)
├── running_logs   : 러닝 기록
│     (user_id / log_date / distance_km / avg_pace / best_pace /
│      duration_sec / calories / avg_heart_rate / is_indoor /
│      altitude_m / image_url / created_at)
├── events         : 이벤트
│     (group_id / name / start_date / end_date / base_score / memo)
├── event_scores   : 이벤트 점수
│     (event_id / user_id / score / memo / created_at)
└── user_goals     : 개인 목표
      (user_id / target_km / start_date / end_date /
       is_achieved / reward_score)
```

---

## 이미지 파싱 스펙

CORE API가 스크린샷에서 추출하는 항목:

| 항목 | 설명 |
|---|---|
| distance_km | 거리 (km) |
| avg_pace | 평균 페이스 (예: 5'30"/km) |
| best_pace | 최고 페이스 |
| duration_sec | 총 운동시간 (초) |
| calories | 소모 칼로리 |
| avg_heart_rate | 평균 심박수 (bpm) |
| is_indoor | 실내(트레드밀) 여부 |
| altitude_m | 누적 고도 (m) |

---

## v1 개발 우선순위

```
1. Laravel 설치 + Supabase 연결
2. 회원 인증 (초대 코드 기반 클로즈 베타)
3. 러닝 이미지 업로드 → CORE API 파싱 → 기록 저장
4. 개인 기록 목록 / 통계 조회
5. 이벤트 등록 + 참가자 점수 관리
6. 관리자: 구성원 관리 / 통계 / 엑셀 다운로드
```

---

## v2 예정 기능

| 기능 | 설명 |
|---|---|
| 조별 랭킹 | 그룹별 누적 거리 / 점수 순위 |
| 개인 목표 자동 체크 | 목표 마일리지 달성 시 점수 자동 부여 |
| 이미지 일괄 다운로드 | 관리자 전용 |
| 주간 리포트 이메일 | SES로 개인 기록 요약 발송 |
| REVIEW 연동 | 크루 멤버의 대회 참가 리뷰 자동 연결 |

---

## 관리자 기능 (`/admin`)

```
- 구성원 관리 (크루 / 지부 / 그룹 배정)
- 이벤트 등록 / 수정 / 삭제
- 점수 수동 조정
- 전체 통계 조회 (기간별 / 그룹별)
- 엑셀 다운로드 (기록 / 점수)
- 이미지 일괄 다운로드 (S3)
```

---

## 로컬 개발 환경 설정 (예정)

```bash
# 1. 프로젝트 생성 (예정)
composer create-project laravel/laravel crew

# 2. 환경 설정
cp .env.example .env
php artisan key:generate

# 3. DB 마이그레이션
php artisan migrate

# 4. 개발 서버
php artisan serve
npm run dev
```

**.env 필수 항목 (예정)**
```
DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-southeast-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.[project-ref]
DB_PASSWORD=[password]

CORE_API_URL=http://localhost:8000
AWS_BUCKET=[s3-bucket-name]
```

---

## 주의사항

- 이미지는 반드시 **S3에 저장**, DB에는 URL만 기록
- CORE API 파싱 실패 시 이미지는 보관, 수동 재처리 가능하도록 설계
- 초대 코드 기반 클로즈 베타로 시작 (공개 가입 차단)
- DB 비밀번호 / API Key는 `.env` 관리, Git 커밋 금지

---

## 관련 프로젝트

| 프로젝트 | 역할 |
|---|---|
| **REVIEW** (Laravel) | 대회 리뷰 플랫폼 — 동일 users 테이블 공유 |
| **CORE API** (Python) | 이미지 파싱 / AI 분석 서버 |
