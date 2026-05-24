<?php

namespace App\Services;

use App\Models\RunningLog;
use App\Models\User;
use App\Models\UserGoal;
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
            $response = Http::timeout(60)
                ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post(config('services.core_api.url') . '/api/parse-image');

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
                ];
            }

            Log::warning('CORE API parse-image 응답 오류: ' . $response->status());
        } catch (\Exception $e) {
            Log::warning('CORE API parse-image 실패: ' . $e->getMessage());
        }

        return ['s3_url' => null, 'parsed' => [], 'raw_parsed' => []];
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
        $log->delete();
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

    // confirmed 기록 기준으로 user_goals.achieved_km 재집계
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
    }

    // 파싱된 날짜의 연도 교정 — GPT 환각(2020, 2023 등) 방어용 2차 안전망
    // CORE API에서 1차 교정 후에도 잘못된 연도가 오면 올해로 덮어쓴다
    private function sanitizeRunDate(?string $date): string
    {
        if (!$date) return now()->toDateString();

        $parsed = Carbon::createFromFormat('Y-m-d', $date);
        if (!$parsed) return now()->toDateString();

        $currentYear = now()->year;

        // 연도가 2년 이상 과거 → 올해로 교정
        if ($parsed->year < $currentYear - 1) {
            $parsed->setYear($currentYear);
        }

        // 미래 날짜 → 전년도로 교정
        if ($parsed->isFuture()) {
            $parsed->setYear($currentYear - 1);
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
