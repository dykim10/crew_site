# CREW 프로젝트 정의서

> 원본 공통 정의서: ~/projects/project-definition.md
> 이 파일은 CREW(Laravel) 프로젝트에 특화된 정의입니다.

---

## 이 프로젝트의 역할

러닝 크루 구성원의 **기록 관리 및 이벤트 점수 플랫폼**

- 러닝 이미지 업로드 → CORE API 파싱 → 기록 자동 저장
- 개인 / 조별 기록 관리
- 이벤트 점수 관리
- 관리자 통계 / 엑셀 다운로드

---

## 기술 스택

| 구분 | 기술 |
|---|---|
| 언어 | PHP 8.3+ |
| 프레임워크 | Laravel |
| DB | Supabase PostgreSQL |
| 프론트 | Blade 템플릿 / CSS / JS |
| HTTP 클라이언트 | Guzzle (CORE API 호출) |
| 이미지 파싱 | CORE API 경유 (Claude Vision) |

---

## 경로

| 구분 | 경로 |
|---|---|
| EC2 서버 | `/var/www/running-crew/` |
| 로컬 | `~/projects/crew/` |
| GitHub | `https://github.com/dykim10/crew_site.git` |

---

## 조직 계층

```
crews    → 크루 (최상위)
branches → 지부
groups   → 그룹 (최하위)
```

---

## DB 스키마

```
public 스키마 (공통)
├── users          : 통합 회원
├── crews          : 크루
├── branches       : 지부
└── groups         : 그룹

crew 스키마 (CREW 전용)
├── running_logs   : 러닝 기록 (거리/페이스/실내외/고도/날짜/이미지)
├── events         : 이벤트 (이벤트명/그룹/날짜/기본점수/메모)
├── event_scores   : 이벤트 점수 (이벤트/회원/점수/메모)
└── user_goals     : 개인 목표 (목표마일리지/기간/달성여부/점수)
```

---

## CORE API 호출

| 메서드 | 엔드포인트 | 용도 |
|---|---|---|
| POST | `http://localhost:8000/api/parse-image` | 러닝 이미지 파싱 |

---

## 회원 정책

- 1단계: 클로즈 베타 → 초대 코드로만 가입
- 2단계: 오픈 베타 → 이메일 인증
- 3단계: 정식 오픈

**통합 회원 테이블 (public.users)**
```
id / email / password / name / nickname
crew_id / branch_id / group_id
role        : super_admin / crew_admin / group_admin / member
is_beta     : 베타 여부
invite_code : 초대 코드
created_at / updated_at / last_login_at
```

---

## 관리자 기능 (/admin)

```
- 구성원 관리 (크루 / 지부 / 그룹)
- 이벤트 등록 / 점수 관리
- 통계 / 집계 조회
- 엑셀 다운로드
- 이미지 일괄 다운로드
```

**권한 구조**
```
super_admin → 전체 관리
crew_admin  → 크루 단위 관리
group_admin → 그룹 단위 관리
member      → 일반 구성원
```

---

## 개발 우선순위 (v1)

```
1. Laravel 기본 설치 및 Supabase 연결               ✅ 완료 (2026-05-17)
2. 회원 인증 (초대 코드 기반 클로즈 베타)             ✅ 완료 (2026-05-17)
3. 러닝 이미지 업로드 → CORE API 파싱 연동           ← 다음 (S3 연동 필요)
4. 개인 기록 조회 / 관리
5. 이벤트 점수 관리
```

## v1 완료 기능 상세 (2026-05-17 기준)

```
- Laravel 13 + Breeze (Blade) 설치
- Supabase pooler 연결 (search_path=public,crew)
- crew 스키마 생성: running_logs / events / event_scores / user_goals
- 초대 코드 기반 회원가입 (VALID_INVITE_CODES 상수)
- User 모델: public.users 공유 테이블, isAdmin() 헬퍼
- RunningLog 모델: avg_pace_formatted / duration_formatted Attribute
- RunningLogService: S3 업로드 + CORE API /api/parse-image 연동
- RunningLogController: CRUD (소유자 검증 포함)
- 뷰: running-logs/index, create, show, edit (Tailwind)
- 네비게이션: 대시보드 / 러닝 기록 메뉴
```

## 다음 작업
- AWS S3 버킷/IAM 키를 .env에 설정 후 이미지 업로드 테스트
- CORE API /api/parse-image 파싱 결과 → 기록 자동 입력
- 개인 기록 통계 (월별 차트)

---

## 주의사항

- DB 비밀번호 / API Key 는 `.env` 관리 / Git 커밋 금지
- 운영 환경에서 `APP_DEBUG=false` 필수
- CORE API 호출 실패 시 예외 처리 필수
- 이미지는 S3 저장 후 URL만 DB에 기록
