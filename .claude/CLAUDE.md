# CREW 프로젝트 - Claude Code 지침

@C:\Users\dykim\.claude\plugins\marketplaces\claude-plugins-official\plugins\frontend-design\skills\frontend-design\SKILL.md

> 러닝 크루 구성원 기록 관리 및 이벤트 점수 플랫폼  
> **스펙 정본:** `../../.claude/definition/07-crew.md` · **진행:** `../../developer_md/STATUS.md`  
> `./project-definition.md` · `../project-definition.md`는 **레거시** — 갱신하지 않는다.

@../../developer_md/STATUS.md
@../../.claude/definition/01-overview.md
@../../.claude/definition/02-common-rules.md
@../../.claude/definition/04-api-endpoints.md
@../../.claude/definition/07-crew.md

---

## 문서 자동 갱신 (doc-sync)

기능 완료·commit 직전·`/compact` 직전·"문서 갱신" 요청 시:

1. **`../../.claude/definition/07-crew.md`** — CREW 스펙·완료/미완·다음 작업
2. **`../../developer_md/STATUS.md`** — PLAN/TASK 진행만 (스키마 중복 금지)
3. 공통 변경 시 `03-db-schema.md` · `04-api-endpoints.md` 등 해당 파일

상세 절차: 워크스페이스 루트 `.claude/skills/doc-sync.md` 또는 `/doc-sync`

---

## 기술 스택

| 구분 | 기술 |
|---|---|
| 언어 | PHP 8.3+ |
| 프레임워크 | Laravel |
| DB | Supabase PostgreSQL |
| 프론트 | Blade / Tailwind / Filament v4 |
| HTTP | Guzzle (CORE API) |
| 이미지 파싱 | Claude Vision (CORE 경유) |

---

## 경로

| | |
|---|---|
| EC2 | `/var/www/crew-site/` |
| 로컬 | `C:\src\projects\crew\` · 포트 **8300** |
| GitHub | https://github.com/dykim10/crew_site.git |

---

## CORE API (로컬)

```
CORE_API_URL=http://localhost:8100
POST /api/parse-image  → 러닝 이미지 파싱 (상세: 04-api-endpoints.md)
```

---

## 조직 계층

```
generations → regions → groups · crews · branches
```

DB·기능·Filament·진행 현황은 import된 **`07-crew.md`** · **`STATUS.md`** 참조.

---

## 주의사항

- DB/API Key는 `.env` 관리 · Git 커밋 금지
- CORE API 호출 실패 시 예외 처리 필수
- 이미지는 S3 저장 후 URL만 DB 기록
