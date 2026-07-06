<?php

namespace App\Services;

use App\Models\PersonalRecord;
use App\Models\RunningLog;
use App\Models\TrainingReport;
use App\Models\TrainingSchedule;
use App\Models\User;
use App\Models\UsersDetail;
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
            'profileEmpty'    => $this->profileIsEmpty($user),
            'trainingGoal'    => $this->getTrainingGoal($user),
        ];
    }

    public const COURSE_TYPES = [
        '5K'   => '5K',
        '10K'  => '10K',
        'HALF' => '하프마라톤 (21K)',
        'FULL' => '풀코스 (42K)',
    ];

    public function getTrainingGoal(User $user): ?array
    {
        $goal = UsersDetail::where('user_id', $user->id)->value('training_goal');
        if (!is_array($goal) || empty($goal['race_name']) || empty($goal['course_type'])) {
            return null;
        }
        return $goal;
    }

    public function saveTrainingGoal(User $user, array $data): array
    {
        $goal = [
            'race_edition_id' => $data['race_edition_id'] ?? null,
            'race_name'       => $data['race_name'],
            'course_type'     => $data['course_type'],
            'race_date'       => $data['race_date'],
            'goal_time'       => $data['goal_time'] ?? null,
        ];

        UsersDetail::updateOrCreate(
            ['user_id' => $user->id],
            ['training_goal' => $goal]
        );

        return $goal;
    }

    /** @return list<array{id:int,race_name:string,race_date:?string,year:?int}> */
    public function listUpcomingRaceEditions(): array
    {
        try {
            $response = Http::timeout(15)->get("{$this->baseUrl}/api/races/editions/upcoming", [
                'limit' => 80,
            ]);
            if (!$response->successful()) {
                Log::warning('CORE upcoming race editions 조회 실패', [
                    'status' => $response->status(),
                ]);
                return [];
            }
            $data = $response->json();
            return is_array($data) ? $data : [];
        } catch (\Throwable $e) {
            Log::warning('CORE upcoming race editions 연결 실패', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /** PB·러닝 기록 없음 → CORE 데모 모드 대상 */
    public function profileIsEmpty(User $user): bool
    {
        return !PersonalRecord::where('user_id', $user->id)->exists()
            && !RunningLog::byUser($user->id)->exists();
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

    public function parsePb(UploadedFile $file): array
    {
        $response = Http::timeout(60)
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post("{$this->baseUrl}/api/coach/pb/parse");

        if (!$response->successful()) {
            $message = $response->json('detail') ?? 'PB 이미지 파싱에 실패했습니다.';
            if (is_array($message)) {
                $message = $message[0]['msg'] ?? 'PB 이미지 파싱에 실패했습니다.';
            }
            throw new \RuntimeException(is_string($message) ? $message : 'PB 이미지 파싱에 실패했습니다.');
        }

        return $response->json();
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
