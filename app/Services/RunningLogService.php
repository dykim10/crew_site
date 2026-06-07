<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventScore;
use App\Models\RunningLog;
use App\Models\User;
use App\Models\UserGoal;
use App\Models\UsersDetail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * 러닝 기록 서비스 (app/Services/RunningLogService.php)
 *
 * 러닝 기록 관련 비즈니스 로직 전담.
 * RunningLogController 와 Filament 관리자 패널 모두에서 사용한다.
 *
 * [이미지 파싱 흐름]
 *   parseImage()   : 이미지 → CORE API POST /api/parse-image
 *                    → S3 저장(boto3) + GPT-4o Vision 파싱 → 수치 JSON 반환
 *   createDraft()  : 파싱 결과로 is_confirmed=false draft 행 INSERT
 *   confirmLog()   : 사용자 보정 데이터로 UPDATE — is_confirmed는 그대로 유지 (관리자만 확정 가능)
 *
 * [CRUD]
 *   create()          : 직접 입력 저장 — is_confirmed=false (관리자 검수 대기)
 *   update()          : 기록 수정
 *   delete()          : 기록 삭제
 *   getByUser()       : 확정 기록 페이지네이션 조회
 *   getMonthlyStats() : 이번달 거리·횟수·총 시간·평균 페이스 집계
 *
 * [관리자 검수]
 *   adminConfirm()   : is_confirmed=true 후 recalculateGoals() 트리거
 *   adminUnconfirm() : is_confirmed=false 후 recalculateGoals() 트리거
 *
 * [내부 유틸]
 *   timeToSeconds()    : "1:23:45" / "23:45" → 초 변환
 *   recalculateGoals() : confirmed 기록 전체 재합산 → user_goals 업데이트
 *                        (가산·감산 대신 전체 재합산으로 중복 반영 방지)
 */
class RunningLogService
{
    /**
     * 이미지를 CORE API로 전달 → S3 업로드 + GPT-4o 파싱 결과 반환
     * 반환: ['s3_url' => ..., 'parsed' => [...파싱된 수치들...]]
     */
    public function parseImage(UploadedFile $file): array
    {
        try {
            $webp = $this->convertToWebp($file);
            [$fileContent, $fileName] = $webp
                ? [$webp, pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp']
                : [file_get_contents($file->getRealPath()), $file->getClientOriginalName()];

            $coreUrl = rtrim(config('services.core_api.url', 'http://localhost:8100'), '/');

            $response = Http::timeout(60)
                ->attach('file', $fileContent, $fileName)
                ->post($coreUrl . '/api/parse-image');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    's3_url' => $data['s3_url'] ?? null,
                    'parsed' => [
                        'run_date'          => $data['run_date'] ?? null,
                        'distance_km'       => $data['distance_km'] ?? null,
                        'duration_seconds'  => $data['duration_seconds'] ?? null,
                        'avg_pace_seconds'  => $data['avg_pace_seconds'] ?? null,
                        'best_pace_seconds' => $data['best_pace_seconds'] ?? null,
                        'calories'          => $data['calories'] ?? null,
                        'avg_heart_rate'    => $data['avg_heart_rate'] ?? null,
                        'is_indoor'         => $data['is_indoor'] ?? false,
                        'elevation_m'       => $data['elevation_m'] ?? null,
                    ],
                    'raw_parsed' => $data,
                    'error'      => null,
                ];
            }

            Log::warning('CORE API parse-image 응답 오류', [
                'url'    => $coreUrl . '/api/parse-image',
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return ['s3_url' => null, 'parsed' => [], 'raw_parsed' => [], 'error' => 'HTTP ' . $response->status()];
        } catch (\Exception $e) {
            Log::warning('CORE API parse-image 연결 실패', [
                'url'   => config('services.core_api.url', 'http://localhost:8100') . '/api/parse-image',
                'error' => $e->getMessage(),
            ]);
            return ['s3_url' => null, 'parsed' => [], 'raw_parsed' => [], 'error' => $e->getMessage()];
        }

        return ['s3_url' => null, 'parsed' => [], 'raw_parsed' => [], 'error' => 'unknown'];
    }

    public function create(array $data, User $user): RunningLog
    {
        return RunningLog::create([
            'user_id'           => $user->id,
            'group_id'          => $user->group_id,
            'run_date'          => $data['run_date'],
            'distance_km'       => $data['distance_km'],
            'duration_seconds'  => $this->timeToSeconds($data['duration']),
            'avg_pace_seconds'  => $data['avg_pace_seconds'] ?? null,
            'best_pace_seconds' => $data['best_pace_seconds'] ?? null,
            'is_indoor'         => (bool) ($data['is_indoor'] ?? false),
            'calories'          => $data['calories'] ?? null,
            'avg_heart_rate'    => $data['avg_heart_rate'] ?? null,
            'elevation_m'       => $data['elevation_m'] ?? null,
            'weather_desc'      => $data['weather_desc'] ?? null,
            'image_url'         => $data['image_url'] ?? null,
            'parsed_data'       => $data['parsed_data'] ?? null,
            'memo'              => $data['memo'] ?? null,
            'is_confirmed'      => false,
        ]);
    }

    // 파싱 직후 미확정 상태로 INSERT
    public function createDraft(array $parseResult, User $user): RunningLog
    {
        $p = $parseResult['parsed'];
        return RunningLog::create([
            'user_id'           => $user->id,
            'group_id'          => $user->group_id,
            'run_date'          => $this->sanitizeRunDate($p['run_date'] ?? null),
            'distance_km'       => $p['distance_km'] ?? 0,
            'duration_seconds'  => $p['duration_seconds'] ?? 0,
            'avg_pace_seconds'  => $p['avg_pace_seconds'] ?? null,
            'best_pace_seconds' => $p['best_pace_seconds'] ?? null,
            'is_indoor'         => (bool) ($p['is_indoor'] ?? false),
            'calories'          => $p['calories'] ?? null,
            'avg_heart_rate'    => $p['avg_heart_rate'] ?? null,
            'elevation_m'       => $p['elevation_m'] ?? null,
            'image_url'         => $parseResult['s3_url'],
            'parsed_data'       => $parseResult['raw_parsed'] ?: null,
            'is_confirmed'      => false,
        ]);
    }

    // 사용자 데이터 보정 저장 — is_confirmed는 변경하지 않음 (관리자 검수 후 확정)
    public function confirmLog(RunningLog $log, array $data): RunningLog
    {
        $log->update([
            'run_date'          => $data['run_date'],
            'distance_km'       => $data['distance_km'],
            'duration_seconds'  => $this->timeToSeconds($data['duration']),
            'avg_pace_seconds'  => $data['avg_pace_seconds'] ?? $log->avg_pace_seconds,
            'best_pace_seconds' => $data['best_pace_seconds'] ?? $log->best_pace_seconds,
            'is_indoor'         => (bool) ($data['is_indoor'] ?? false),
            'calories'          => $data['calories'] ?? null,
            'avg_heart_rate'    => $data['avg_heart_rate'] ?? null,
            'elevation_m'       => $data['elevation_m'] ?? null,
            'weather_desc'      => $data['weather_desc'] ?? null,
            'memo'              => $data['memo'] ?? null,
        ]);
        return $log->fresh();
    }

    public function update(RunningLog $log, array $data): RunningLog
    {
        $log->update([
            'run_date'         => $data['run_date'],
            'distance_km'      => $data['distance_km'],
            'duration_seconds' => $this->timeToSeconds($data['duration']),
            'is_indoor'        => (bool) ($data['is_indoor'] ?? false),
            'calories'         => $data['calories'] ?? null,
            'avg_heart_rate'   => $data['avg_heart_rate'] ?? null,
            'elevation_m'      => $data['elevation_m'] ?? null,
            'weather_desc'     => $data['weather_desc'] ?? null,
            'memo'             => $data['memo'] ?? null,
        ]);
        return $log->fresh();
    }

    public function delete(RunningLog $log): void
    {
        $log->delete(); // S3 삭제는 RunningLog::booted() deleting 이벤트에서 자동 처리
    }

    // CORE API를 통해 S3 이미지 삭제 — 실패 시 로그만 기록하고 DB 삭제는 차단하지 않음
    public function deleteS3Image(string $imageUrl): void
    {
        try {
            $endpoint = config('services.core_api.url') . '/api/s3/image?' . http_build_query(['url' => $imageUrl]);
            Http::timeout(10)->delete($endpoint);
        } catch (\Throwable $e) {
            Log::warning('S3 이미지 삭제 실패', [
                'url'   => $imageUrl,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getByUser(User $user, int $perPage = 20)
    {
        return RunningLog::byUser($user->id)
            ->orderByDesc('run_date')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getMonthlyStats(User $user, int $year, int $month): array
    {
        $logs = RunningLog::byUser($user->id)
            ->where('is_confirmed', true)
            ->whereYear('run_date', $year)
            ->whereMonth('run_date', $month)
            ->get();

        return [
            'total_km'    => round($logs->sum('distance_km'), 2),
            'total_count' => $logs->count(),
            'total_time'  => $logs->sum('duration_seconds'),
            'avg_pace'    => $logs->avg('avg_pace_seconds') ? (int) $logs->avg('avg_pace_seconds') : null,
        ];
    }

    // 관리자 검수 확인 — is_confirmed=true 후 마일리지 재집계
    public function adminConfirm(RunningLog $log): void
    {
        if ($log->is_confirmed) return;

        $log->update(['is_confirmed' => true]);
        $this->recalculateGoals($log->user_id, $log->run_date);
    }

    // 관리자 검수 취소 — is_confirmed=false 후 마일리지 재집계
    public function adminUnconfirm(RunningLog $log): void
    {
        if (!$log->is_confirmed) return;

        $log->update(['is_confirmed' => false]);
        $this->recalculateGoals($log->user_id, $log->run_date);
    }

    // confirmed 기록 기준으로 user_goals.achieved_km 재집계 + A타입 이벤트 자동 점수 부여
    // add/subtract 대신 전체 재합산 — 중복 반영 방지
    private function recalculateGoals(int $userId, $runDate): void
    {
        $date  = Carbon::parse($runDate);
        $year  = $date->year;
        $month = $date->month;

        $yearlyKm = RunningLog::where('user_id', $userId)
            ->where('is_confirmed', true)
            ->whereYear('run_date', $year)
            ->sum('distance_km');

        $monthlyKm = RunningLog::where('user_id', $userId)
            ->where('is_confirmed', true)
            ->whereYear('run_date', $year)
            ->whereMonth('run_date', $month)
            ->sum('distance_km');

        UserGoal::yearly($userId, $year)->each(function (UserGoal $goal) use ($yearlyKm) {
            $goal->update([
                'achieved_km' => $yearlyKm,
                'is_achieved' => $yearlyKm >= $goal->target_km,
            ]);
        });

        UserGoal::monthly($userId, $year, $month)->each(function (UserGoal $goal) use ($monthlyKm) {
            $goal->update([
                'achieved_km' => $monthlyKm,
                'is_achieved' => $monthlyKm >= $goal->target_km,
            ]);
        });

        $this->awardMileageGradeEventScores($userId, $runDate);
    }

    // A타입(mileage_grade) 이벤트 — 기간 내 달성 km가 등급 목표 이상이면 event_scores upsert
    private function awardMileageGradeEventScores(int $userId, $runDate): void
    {
        $dateStr = Carbon::parse($runDate)->toDateString();

        $events = Event::where('event_type', 'A')
            ->where('score_type', 'mileage_grade')
            ->where('status', 'active')
            ->whereDate('start_date', '<=', $dateStr)
            ->whereDate('end_date', '>=', $dateStr)
            ->whereNotNull('score_config')
            ->get();

        if ($events->isEmpty()) return;

        $detail = UsersDetail::where('user_id', $userId)->first();
        $grade  = $detail?->grade; // A, B, C

        if (!$grade) return;

        foreach ($events as $event) {
            // score_config는 [["grade"=>"A","target_km"=>120], ...] 배열 구조
            $gradeRow = collect($event->score_config ?? [])
                ->firstWhere('grade', $grade);
            $targetKm = (float) ($gradeRow['target_km'] ?? 0);
            if ($targetKm <= 0) continue;

            $achievedKm = (float) RunningLog::where('user_id', $userId)
                ->where('is_confirmed', true)
                ->whereDate('run_date', '>=', $event->start_date->toDateString())
                ->whereDate('run_date', '<=', $event->end_date->toDateString())
                ->sum('distance_km');

            // 기존 auto 점수 행이 있으면 갱신, 없으면 삽입 — source='auto'만 덮어씀
            EventScore::updateOrCreate(
                ['event_id' => $event->id, 'user_id' => $userId, 'source' => 'auto'],
                ['score' => $achievedKm]
            );
        }
    }

    private function convertToWebp(UploadedFile $file): ?string
    {
        if (!function_exists('imagecreatefromstring')) {
            return null;
        }

        $image = @\imagecreatefromstring(file_get_contents($file->getRealPath()));
        if (!$image) return null;

        ob_start();
        \imagewebp($image, null, 82);
        $content = ob_get_clean();
        \imagedestroy($image);

        return $content ?: null;
    }

    // 파싱된 날짜 유효성 검사 — CORE API가 연도를 1차 교정하므로 여기서는 최소한만 처리
    // "2년 이상 과거 교정"은 CORE API에서만 수행 (이미지에 연도가 있으면 과거 기록도 신뢰해야 함)
    private function sanitizeRunDate(?string $date): string
    {
        if (!$date) return now()->toDateString();

        $parsed = Carbon::createFromFormat('Y-m-d', $date);
        if (!$parsed) return now()->toDateString();

        // 미래 날짜 → 전년도로 교정 (최후 안전망)
        if ($parsed->isFuture()) {
            $parsed->setYear(now()->year - 1);
        }

        return $parsed->toDateString();
    }

    // "1:23:45" 또는 "23:45" → 초 변환
    private function timeToSeconds(string $time): int
    {
        $parts = explode(':', $time);
        if (count($parts) === 3) {
            return (int)$parts[0] * 3600 + (int)$parts[1] * 60 + (int)$parts[2];
        }
        return (int)$parts[0] * 60 + (int)$parts[1];
    }
}
