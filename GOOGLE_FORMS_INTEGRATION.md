# Google Forms 연동 가이드

> 작성일: 2026-05-24
> 현황: Sheets API (Service Account) 방식 구현 완료
> 추후 기능 확장 시 아래 방법 비교표를 참고할 것

---

## 현재 구현 방식: Google Sheets API + Service Account

Google Forms 의 응답은 자동으로 연결된 Google Sheets 에 기록된다.
현재는 그 스프레드시트를 **Service Account** 로 읽는 방식을 사용한다.

### 흐름

```
구글 폼 응답 자동 저장
  → 연결된 Google Sheets (응답 탭)
  → CREW 관리자가 Sheet URL 등록
  → GoogleFormService::getResponses(sheetId)
  → google/apiclient (Service Account 인증)
  → 응답 데이터 조회
```

### 설정 방법

1. **Google Cloud Console** (console.cloud.google.com)
   - 프로젝트 선택 → IAM 및 관리 → 서비스 계정
   - 서비스 계정 키(JSON) 다운로드

2. **키 파일 배치** (Git 커밋 금지)
   ```
   storage/app/google/service-account.json
   ```
   EC2 에는 scp 로 직접 전송:
   ```bash
   scp -i crew-new-key.pem service-account.json \
       ubuntu@<EC2-IP>:/var/www/crew-site/storage/app/google/service-account.json
   ```

3. **Sheet 공유 설정**
   - 서비스 계정 이메일 확인 (JSON 파일 내 `client_email`)
   - Google Sheet 열기 → 공유 → 서비스 계정 이메일을 **뷰어** 로 추가

4. **Sheet URL 등록**
   - CREW 관리자 → 설문 관리 → 구글 폼 연동 → 폼 추가
   - Sheet URL 전체 또는 Sheet ID 입력
   - URL 형식: `https://docs.google.com/spreadsheets/d/{SHEET_ID}/edit`

### 주의사항

- **폼 URL(viewform)이 아닌 시트 URL** 을 입력해야 한다
- 폼 URL: `https://docs.google.com/forms/d/.../viewform` ← 사용 불가
- 시트 URL: `https://docs.google.com/spreadsheets/d/{ID}/edit` ← 이것을 사용
- 시트 URL 확인: 폼 편집 화면 → 응답 탭 → 스프레드시트 아이콘 클릭

### 관련 코드

| 파일 | 역할 |
|---|---|
| `app/Services/GoogleFormService.php` | Sheets API 호출, Sheet ID 추출 |
| `app/Models/GoogleForm.php` | crew.google_forms 테이블 매핑 |
| `app/Filament/Resources/GoogleFormResource.php` | 관리자 CRUD + 결과 모달 + 엑셀 다운로드 |
| `resources/views/filament/modals/google-form-responses.blade.php` | 응답 테이블 뷰 |

### 설치된 패키지

```bash
composer require google/apiclient --ignore-platform-req=ext-gd
```

EC2 에서는:
```bash
composer install --no-dev
```

---

## 방법 2: Public CSV (인증 없음)

Google Sheets 를 **인터넷에 공개** 설정하면 별도 인증 없이 CSV 로 읽을 수 있다.

### 방법

```php
$csvUrl = "https://docs.google.com/spreadsheets/d/{SHEET_ID}/export?format=csv&gid=0";
$csv = file_get_contents($csvUrl);
$rows = array_map('str_getcsv', explode("\n", trim($csv)));
$headers = array_shift($rows);
```

### 장단점

| 항목 | 내용 |
|---|---|
| 장점 | 서비스 계정 불필요, 설정 간단 |
| 단점 | 시트를 반드시 "인터넷에 공개"로 설정해야 함 (보안 취약) |
| 적합한 경우 | 공개 설문, 비민감 데이터 수집 |

### 주의

개인정보가 포함된 폼 응답에는 절대 사용하지 않는다.

---

## 방법 3: Google Forms API (직접 호출)

Google Forms API 를 사용하면 스프레드시트 없이 폼 데이터를 직접 조회할 수 있다.
단, **OAuth 2.0** 인증이 필수이다 (Service Account 불가).

### Service Account 와 Forms API 가 다른 이유

Google Forms API 는 사용자 소유 리소스에 접근하는 개인화 API 이므로
반드시 실제 Google 계정으로 OAuth 동의(소유자 또는 공동 편집자)가 필요하다.

| 항목 | Sheets API | Forms API |
|---|---|---|
| 인증 방식 | Service Account ✅ / OAuth 둘 다 가능 | OAuth 2.0 필수 |
| 사용 가능 계정 | 서비스 계정(봇) | 폼 소유자 또는 공동 편집자 |
| 폼 공유 필요 | 필요 (뷰어로 서비스 계정 추가) | 필요 (편집자로 OAuth 계정 추가) |

### OAuth 2.0 접근 권한 범위

Forms API 에 접근하려면 Google 계정이 해당 폼에 대해 아래 중 하나여야 한다:
- **소유자**: 폼을 직접 만든 계정
- **편집자(공동작업자)**: 폼 공유 → 편집자로 추가된 계정

뷰어(Viewer) 권한으로는 Forms API 호출 불가.

### OAuth 구현 개요 (추후 참고용)

```php
// 1. Google Cloud Console → OAuth 2.0 클라이언트 ID 생성 (웹 애플리케이션)
// 2. 리다이렉트 URI 등록: https://crew.pac-run.com/admin/google/oauth/callback

use Google\Client;
use Google\Service\Forms;

$client = new Client();
$client->setClientId(env('GOOGLE_CLIENT_ID'));
$client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
$client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
$client->addScope('https://www.googleapis.com/auth/forms.responses.readonly');
$client->addScope('https://www.googleapis.com/auth/forms.body.readonly');

// 인증 URL → 관리자가 브라우저에서 승인
$authUrl = $client->createAuthUrl();

// 콜백에서 액세스 토큰 발급
$token = $client->fetchAccessTokenWithAuthCode($code);

// Forms API 호출
$service = new Forms($client);
$responses = $service->forms_responses->listFormsResponses($formId);
```

### Forms API 로 얻을 수 있는 데이터

- 폼 구조(질문 목록): `GET /v1/forms/{formId}`
- 응답 목록: `GET /v1/forms/{formId}/responses`
- 개별 응답: `GET /v1/forms/{formId}/responses/{responseId}`

### 장단점

| 항목 | 내용 |
|---|---|
| 장점 | 스프레드시트 없이 폼 데이터 직접 접근 가능 |
| 단점 | OAuth 구현 복잡, 토큰 갱신 관리 필요, 편집자 권한 필요 |
| 적합한 경우 | 폼을 스프레드시트 없이 관리하거나, 실시간 스트리밍 Webhook 필요 시 |

---

## 방법 비교 요약

| 방법 | 인증 | 설정 난이도 | 보안 | 권장 여부 |
|---|---|---|---|---|
| **Sheets API + Service Account** (현재) | 서비스 계정 JSON | 중간 | ✅ 높음 | **현재 사용 중** |
| Public CSV | 없음 | 쉬움 | ❌ 낮음 (공개 설정 필요) | 공개 데이터에만 |
| Forms API + OAuth 2.0 | OAuth 소유자/편집자 | 높음 | ✅ 높음 | 추후 확장 시 검토 |

---

## EC2 배포 체크리스트

Google Sheets 연동 기능을 EC2 에 배포할 때 필요한 추가 작업:

```bash
# 1. google/apiclient 패키지 설치 (composer.json 에 이미 추가됨)
cd /var/www/crew-site
composer install --no-dev

# 2. 서비스 계정 키 파일 배치 (scp 로 로컬에서 전송)
# 로컬에서 실행:
# scp -i crew-new-key.pem service-account.json ubuntu@<EC2-IP>:/var/www/crew-site/storage/app/google/service-account.json

# 3. 권한 설정
chmod 600 /var/www/crew-site/storage/app/google/service-account.json

# 4. crew.google_forms 테이블 생성 (Supabase SQL Editor 또는 마이그레이션)
# php artisan migrate 또는 Supabase SQL Editor 에서 직접 실행
```

---

## Supabase 테이블 생성 SQL

```sql
CREATE TABLE IF NOT EXISTS crew.google_forms (
    id          BIGSERIAL PRIMARY KEY,
    title       VARCHAR(255)    NOT NULL,
    sheet_id    VARCHAR(255)    NOT NULL,
    description TEXT,
    is_active   BOOLEAN         NOT NULL DEFAULT true,
    created_at  TIMESTAMPTZ(6)  DEFAULT now(),
    updated_at  TIMESTAMPTZ(6)  DEFAULT now()
);
```
