<?php

namespace App\Services;

use App\Models\RunningLog;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            'is_confirmed'      => true,
        ]);
    }

    // 파싱 직후 미확정 상태로 INSERT
    public function createDraft(array $parseResult, User $user): RunningLog
    {
        $p = $parseResult['parsed'];
        return RunningLog::create([
            'user_id'           => $user->id,
            'group_id'          => $user->group_id,
            'run_date'          => $p['run_date'] ?? now()->toDateString(),
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

    // 사용자 최종 확인 → 데이터 보정 + is_confirmed=true
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
            'is_confirmed'      => true,
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
            ->where('is_confirmed', true)
            ->orderByDesc('run_date')
            ->paginate($perPage);
    }

    public function getMonthlyStats(User $user, int $year, int $month): array
    {
        $logs = RunningLog::byUser($user->id)
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
