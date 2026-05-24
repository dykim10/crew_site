# Laravel / Filament 디버깅 가이드

> CREW 프로젝트 전용. 로컬 개발 환경(`APP_DEBUG=true`)에서만 사용.  
> **운영 서버에 디버깅 코드 커밋 금지.**

---

## 1. 쿼리 디버깅

### SQL 즉시 확인 (dd)
```php
$query = RunningLog::with('user')->where('is_confirmed', true);

dd($query->toSql(), $query->getBindings());
// 출력 예: "select * from "crew"."running_logs" where "is_confirmed" = ?"
//          [true]
```

### 실행 멈추지 않고 출력 (dump)
```php
dump([
    'sql'      => $query->toSql(),
    'bindings' => $query->getBindings(),
    'count'    => $query->count(),
]);
```

### 여러 쿼리 한꺼번에 기록 (QueryLog)
```php
use Illuminate\Support\Facades\DB;

DB::enableQueryLog();

$logs = RunningLog::with('user')->get();  // 실행

dd(DB::getQueryLog());
// [['query' => '...', 'bindings' => [...], 'time' => 2.34], ...]
```

### 로그 파일로 기록 (화면 깨질 때)
```php
use Illuminate\Support\Facades\Log;

Log::debug('query debug', [
    'sql'      => $query->toSql(),
    'bindings' => $query->getBindings(),
    'count'    => $query->count(),
]);
// 확인: storage/logs/laravel.log
```

---

## 2. Filament 테이블 쿼리 디버깅

`app/Filament/Resources/RunningLogResource/Pages/ListRunningLogs.php`에서 오버라이드:

```php
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

protected function getTableQuery(): Builder
{
    $query = parent::getTableQuery();  // Resource 기본 쿼리 상속

    // 방법 A: 즉시 출력 (페이지 렌더링 멈춤)
    dd($query->toSql(), $query->getBindings());

    // 방법 B: 로그 파일 기록 (멈추지 않음)
    Log::debug('Filament table query', [
        'sql'      => $query->toSql(),
        'bindings' => $query->getBindings(),
    ]);

    return $query;
}
```

---

## 3. 변수 / 모델 디버깅

### 단일 모델 확인
```php
$log = RunningLog::find(1);

dd($log);                    // 전체 속성
dd($log->toArray());         // 배열로 변환
dd($log->getAttributes());   // DB 원본 값 (accessor 거치기 전)
dd($log->getDirty());        // 변경된 필드만
```

### 컬렉션 확인
```php
$logs = RunningLog::limit(5)->get();

dd($logs->count());
dd($logs->pluck('distance_km', 'id'));   // id => distance_km 맵
dd($logs->toArray());
```

---

## 4. Request 디버깅 (Controller)

```php
public function store(Request $request)
{
    dd($request->all());          // 전체 입력값
    dd($request->validated());    // validate() 통과한 값
    dd($request->headers->all()); // 헤더 확인
}
```

---

## 5. CORE API 호출 디버깅

```php
use Illuminate\Support\Facades\Log;

// RunningLogService.php 또는 Controller에서
$response = Http::post(config('services.core_api.url') . '/api/parse-image', [...]);

Log::debug('CORE API response', [
    'status' => $response->status(),
    'body'   => $response->json(),
]);

if ($response->failed()) {
    dd($response->status(), $response->body());
}
```

---

## 6. 로그 파일 실시간 확인 (터미널)

```powershell
# Windows PowerShell (로컬)
Get-Content C:\src\projects\crew\storage\logs\laravel.log -Tail 50 -Wait
```

```bash
# EC2 서버
tail -f /var/www/crew-site/storage/logs/laravel.log
```

---

## 7. config / env 값 확인

```php
dd(config('database.connections.pgsql'));  // DB 연결 설정
dd(config('services.core_api'));           // CORE API 설정
dd(env('APP_ENV'));                         // .env 값 직접 확인 (비추: config() 우선)
```

---

## 주의사항

- `dd()` 는 Livewire 컴포넌트 안에서 쓰면 JSON 응답을 깨뜨릴 수 있음 → `Log::debug()` 대신 사용
- `DB::enableQueryLog()` 는 메모리를 쓰므로 확인 후 반드시 제거
- `storage/logs/laravel.log` 가 너무 커지면: `php artisan log:clear`
