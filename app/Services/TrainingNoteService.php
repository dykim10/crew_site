<?php

namespace App\Services;

use App\Models\PersonalRecord;
use App\Models\RunningLog;
use App\Models\TrainingReport;
use App\Models\TrainingSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrainingNoteService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.core_api.url', 'http://localhost:8100'), '/');
    }

    public function getCalendarData(User $user, int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $logs = RunningLog::byUser($user->id)
            ->whereBetween('run_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('run_date')
            ->get()
            ->groupBy(fn ($log) => $log->run_date->format('Y-m-d'));

        $weekStarts = [];
        $cursor = $start->copy()->startOfWeek(Carbon::MONDAY);
        while ($cursor <= $end) {
            $weekStarts[] = $cursor->copy();
            $cursor->addWeek();
        }

        $schedules = TrainingSchedule::where('user_id', $user->id)
            ->whereIn('week_start', collect($weekStarts)->map->toDateString())
            ->get()
            ->keyBy(fn ($s) => $s->week_start->format('Y-m-d'));

        $currentWeekStart = now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $currentReport = TrainingReport::where('user_id', $user->id)
            ->where('period_start', $currentWeekStart)
            ->first();
        $currentSchedule = $schedules->get($currentWeekStart);

        return [
            'year'            => $year,
            'month'           => $month,
            'logsByDate'      => $logs,
            'schedules'       => $schedules,
            'weekStarts'      => $weekStarts,
            'currentWeekStart'=> $currentWeekStart,
            'currentReport'   => $currentReport,
            'currentSchedule' => $currentSchedule,
        ];
    }

    public function coachFeedback(int $logId): array
    {
        return $this->postJson("/api/coach/feedback/{$logId}", [], 60);
    }

    public function weeklyReport(int $userId, string $weekStart): array
    {
        return $this->postJson('/api/coach/weekly-report', [
            'user_id'    => $userId,
            'week_start' => $weekStart,
        ], 120);
    }

    public function generateSchedule(int $userId, string $weekStart): array
    {
        return $this->postJson('/api/coach/schedule/generate', [
            'user_id'    => $userId,
            'week_start' => $weekStart,
        ], 120);
    }

    public function parseInbody(UploadedFile $file): array
    {
        $response = Http::timeout(60)
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post("{$this->baseUrl}/api/coach/inbody/parse");

        if (!$response->successful()) {
            throw new \RuntimeException('인바디 이미지 파싱에 실패했습니다.');
        }
        return $response->json();
    }

    public function storeBodyRecord(array $data): array
    {
        return $this->postJson('/api/coach/body-records', $data, 30);
    }

    public function bodyRecords(int $userId, int $limit = 12): array
    {
        $response = Http::timeout(30)->get("{$this->baseUrl}/api/coach/body-records/{$userId}", [
            'limit' => $limit,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('체성분 이력 조회에 실패했습니다.');
        }
        return $response->json('records', []);
    }

    public function mondayOf(Carbon|string $date): string
    {
        return Carbon::parse($date)->startOfWeek(Carbon::MONDAY)->toDateString();
    }

    public function timeToSeconds(string $time): int
    {
        $parts = explode(':', $time);
        if (count($parts) === 2) {
            return (int) $parts[0] * 60 + (int) $parts[1];
        }
        return (int) $parts[0] * 3600 + (int) $parts[1] * 60 + (int) $parts[2];
    }

    public function latestRecordsByDistance(int $userId): Collection
    {
        return PersonalRecord::where('user_id', $userId)
            ->orderByDesc('achieved_at')
            ->get()
            ->unique('distance_type');
    }

    private function postJson(string $path, array $payload, int $timeout): array
    {
        try {
            $response = Http::timeout($timeout)->post("{$this->baseUrl}{$path}", $payload);
        } catch (\Exception $e) {
            Log::warning('CORE API 연결 실패', ['path' => $path, 'error' => $e->getMessage()]);
            throw new \RuntimeException('CORE API 서버에 연결할 수 없습니다. 잠시 후 다시 시도해주세요.');
        }

        if (!$response->successful()) {
            Log::warning('CORE API 응답 오류', ['path' => $path, 'status' => $response->status()]);
            throw new \RuntimeException('AI 코칭 요청에 실패했습니다. 잠시 후 다시 시도해주세요.');
        }

        return $response->json();
    }
}
