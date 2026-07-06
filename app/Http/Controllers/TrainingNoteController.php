<?php

namespace App\Http\Controllers;

use App\Models\PersonalRecord;
use App\Models\RunningLog;
use App\Models\TrainingReport;
use App\Models\TrainingSchedule;
use App\Models\UsersDetail;
use App\Services\TrainingNoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainingNoteController extends Controller
{
    public function __construct(private TrainingNoteService $service) {}

    public function index(Request $request): View
    {
        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);
        $data = $this->service->getCalendarData($request->user(), $year, $month);

        return view('training-notes.index', $data);
    }

    public function showLog(RunningLog $log): View
    {
        abort_if($log->user_id !== auth()->id(), 403);

        return view('training-notes.log-show', compact('log'));
    }

    public function saveNote(Request $request, RunningLog $log): RedirectResponse
    {
        abort_if($log->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'user_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $log->update(['user_note' => $validated['user_note'] ?? null]);

        return redirect()
            ->route('training-notes.logs.show', $log)
            ->with('success', '훈련 메모가 저장되었습니다.');
    }

    public function feedback(Request $request, RunningLog $log): JsonResponse
    {
        abort_if($log->user_id !== auth()->id(), 403);

        if ($log->feedback_at) {
            return response()->json([
                'success' => true,
                'feedback' => $log->coach_feedback,
                'cached'  => true,
            ]);
        }

        try {
            $result = $this->service->coachFeedback($log->id);
            $log->refresh();
            return response()->json(['success' => true, 'feedback' => $result, 'cached' => false]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function report(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'week_start' => ['nullable', 'date'],
        ]);

        $user = $request->user();
        $weekStart = $this->service->mondayOf($validated['week_start'] ?? now());

        $existing = TrainingReport::where('user_id', $user->id)
            ->where('period_start', $weekStart)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'report'  => $existing->report,
                'cached'  => true,
            ]);
        }

        try {
            $report = $this->service->weeklyReport($user->id, $weekStart);
            return response()->json(['success' => true, 'report' => $report, 'cached' => false]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function schedule(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'week_start' => ['nullable', 'date'],
        ]);

        $user = $request->user();
        $weekStart = $this->service->mondayOf($validated['week_start'] ?? now());

        $existing = TrainingSchedule::where('user_id', $user->id)
            ->where('week_start', $weekStart)
            ->first();

        if ($existing) {
            return response()->json([
                'success'  => true,
                'schedule' => [
                    'point_workout' => $existing->point_workout,
                    'weekly_volume' => $existing->weekly_volume,
                    'rationale'     => $existing->rationale,
                ],
                'cached' => true,
            ]);
        }

        try {
            $schedule = $this->service->generateSchedule($user->id, $weekStart);
            return response()->json(['success' => true, 'schedule' => $schedule, 'cached' => false]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function matchSchedule(Request $request, TrainingSchedule $schedule): JsonResponse
    {
        abort_if($schedule->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'matched_log_id' => ['required', 'integer', 'exists:crew.running_logs,id'],
        ]);

        $log = RunningLog::findOrFail($validated['matched_log_id']);
        abort_if($log->user_id !== auth()->id(), 403);

        $schedule->update(['matched_log_id' => $log->id]);

        return response()->json(['success' => true, 'matched_log_id' => $log->id]);
    }

    public function records(Request $request): View
    {
        $records = PersonalRecord::where('user_id', $request->user()->id)
            ->orderByDesc('achieved_at')
            ->get();

        $latestByDistance = $this->service->latestRecordsByDistance($request->user()->id);

        return view('training-notes.records', compact('records', 'latestByDistance'));
    }

    public function storeRecord(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'distance_type' => ['required', 'in:1K,5K,10K,HALF,FULL'],
            'record_time'   => ['required', 'regex:/^\d{1,2}:\d{2}(:\d{2})?$/'],
            'achieved_at'   => ['required', 'date', 'before_or_equal:today'],
        ]);

        PersonalRecord::create([
            'user_id'        => $request->user()->id,
            'distance_type'  => $validated['distance_type'],
            'record_seconds' => $this->service->timeToSeconds($validated['record_time']),
            'achieved_at'    => $validated['achieved_at'],
            'source'         => 'manual',
        ]);

        return redirect()->route('training-notes.records')->with('success', 'PB 기록이 등록되었습니다.');
    }

    public function destroyRecord(Request $request, PersonalRecord $record): RedirectResponse
    {
        abort_if($record->user_id !== auth()->id(), 403);
        $record->delete();

        return redirect()->route('training-notes.records')->with('success', 'PB 기록이 삭제되었습니다.');
    }

    public function body(Request $request): View
    {
        $user = $request->user();
        $detail = UsersDetail::where('user_id', $user->id)->first();
        $hasConsent = $detail?->body_data_consent_at !== null;
        $records = [];

        if ($hasConsent) {
            try {
                $records = $this->service->bodyRecords($user->id);
            } catch (\RuntimeException) {
                $records = [];
            }
        }

        return view('training-notes.body', compact('hasConsent', 'records'));
    }

    public function consent(Request $request): RedirectResponse
    {
        $request->validate(['consent' => ['accepted']]);

        UsersDetail::updateOrCreate(
            ['user_id' => $request->user()->id],
            ['body_data_consent_at' => now()]
        );

        return redirect()->route('training-notes.body')->with('success', '체성분 데이터 수집·이용에 동의하였습니다.');
    }

    public function parseBody(Request $request): JsonResponse
    {
        $detail = UsersDetail::where('user_id', $request->user()->id)->first();
        if (!$detail?->body_data_consent_at) {
            return response()->json(['success' => false, 'message' => '체성분 데이터 수집 동의가 필요합니다.'], 403);
        }

        $request->validate(['image' => ['required', 'image', 'max:10240']]);

        try {
            $parsed = $this->service->parseInbody($request->file('image'));
            return response()->json(['success' => true, 'data' => $parsed]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function storeBody(Request $request): JsonResponse
    {
        $detail = UsersDetail::where('user_id', $request->user()->id)->first();
        if (!$detail?->body_data_consent_at) {
            return response()->json(['success' => false, 'message' => '체성분 데이터 수집 동의가 필요합니다.'], 403);
        }

        $validated = $request->validate([
            'measured_at'         => ['required', 'string'],
            'weight_kg'           => ['required', 'numeric', 'between:30,150'],
            'skeletal_muscle_kg'  => ['nullable', 'numeric'],
            'body_fat_kg'         => ['nullable', 'numeric'],
            'bmi'                 => ['nullable', 'numeric'],
            'body_fat_percent'    => ['nullable', 'numeric'],
            'inbody_score'        => ['nullable', 'integer'],
            'source'              => ['required', 'in:manual,image'],
            'image_url'           => ['nullable', 'string'],
        ]);

        try {
            $result = $this->service->storeBodyRecord(array_merge($validated, [
                'user_id' => $request->user()->id,
            ]));
            return response()->json(['success' => true, 'data' => $result]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
