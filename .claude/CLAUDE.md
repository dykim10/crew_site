# CREW 프로젝트 - Claude Code 지침

> 러닝 크루 구성원 기록 관리 및 이벤트 점수 플랫폼
> 공통 정의서 참고: ./project-definition.md

---

## 기술 스택

| 구분 | 기술 |
|---|---|
| 언어 | PHP 8.3+ |
| 프레임워크 | Laravel |
| DB | Supabase PostgreSQL |
| 프론트 | Blade 템플릿 / CSS / JS |
| HTTP 클라이언트 | Guzzle (CORE-API 호출) |
| 이미지 파싱 | Claude Vision API (CORE 경유) |

---

## 디렉토리 (EC2 서버)

```
/var/www/running-crew/
```

## 로컬 경로

```
~/projects/crew/
```

## GitHub

```
https://github.com/dykim10/crew_site.git
```

---

## 주요 기능

- 구성원 러닝 이미지 업로드 → CORE API 파싱 → DB 저장
- 개인 기록 관리 (거리 / 페이스 / 실내외 / 날짜 / 고도)
- 조별 기록 관리
- 개인 목표 마일리지 → 달성 시 점수 획득
- 이벤트 점수 관리
- 관리자: 통계 / 집계 / 엑셀 다운로드 / 이미지 일괄 다운로드

---

## 조직 계층

```
crews    → 크루 (소속 크루)
branches → 지부 (소속 지부)
groups   → 그룹 (소속 그룹)
```

---

## CORE API 호출 엔드포인트

```
POST http://localhost:8000/api/parse-image  → 러닝 이미지 파싱
```

---

## DB 스키마

```
public 스키마 : users / crews / branches / groups  (공통)
crew   스키마 : running_logs / events / event_scores / user_goals
```

---

## 개발 우선순위 (v1 목표)

```
1. Laravel 기본 설치 및 Supabase 연결
2. 회원 인증 (초대 코드 기반 클로즈 베타)
3. 러닝 이미지 업로드 → CORE API 파싱 연동
4. 개인 기록 조회 / 관리
5. 이벤트 점수 관리
```

---

## 주의사항

- DB 비밀번호 / API Key 는 .env 관리 / Git 커밋 금지
- CORE API 호출 실패 시 예외 처리 필수
- 이미지 업로드는 S3 저장 후 URL만 DB에 기록
