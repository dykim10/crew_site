<?php

namespace App\Http\Controllers;

use App\Models\RunningLog;
use App\Services\RunningLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * 러닝 기록 컨트롤러 (app/Http/Controllers/RunningLogController.php)
 *
 * crew.running_logs 테이블의 CRUD + 이미지 파싱 흐름을 담당한다.
 * 비즈니스 로직은 RunningLogService 에 위임하고,
 * 이 컨트롤러는 유효성 검사 → Service 호출 → 응답 반환만 수행한다.
 *
 * 소유자 검증: 개별 기록 접근 시 abort_if($log->user_id !== auth()->id(), 403) 적용
 *
 * [이미지 파싱 흐름 — 2단계]
 *   1단계: POST /running-logs/parse-image  (parseImage)
 *          이미지를 CORE API 로 전송 → S3 저장 + GPT-4o Vision 파싱
 *          → is_confirmed=false 인 draft 행 INSERT → JSON 응답 (AJAX)
 *   2단계: POST /running-logs/{id}/confirm (confirm)
 *          사용자가 파싱 결과를 확인·수정 후 제출
 *          → draft 행 데이터 UPDATE (is_confirmed 변경 없음 — 관리자 검수 대기)
 *
 * [확정 흐름]
 *   사용자 등록/수정 → is_confirmed=false 유지
 *   관리자 /admin 검수 확인 → RunningLogService::adminConfirm() → is_confirmed=true
 *
 * [Resource 라우트 — CRUD]
 *   index   GET    /running-logs              기록 목록 + 이번달 통계
 *   create  GET    /running-logs/create       등록 폼
 *   store   POST   /running-logs              직접 입력 저장 (이미지 첨부 시 파싱 병행)
 *   show    GET    /running-logs/{id}         기록 상세
 *   edit    GET    /running-logs/{id}/edit    수정 폼
 *   update  PUT    /running-logs/{id}         수정
 *   destroy DELETE /running-logs/{id}         삭제
 */
class RunningLogController extends Controller
{
    public function __construct(private RunningLogService $service) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $logs = $this->service->getByUser($user);
        $now  = now();
        $monthlyStats = $this->service->getMonthlyStats($user, $now->year, $now->month);
        $pendingCount = RunningLog::byUser($user->id)->where('is_confirmed', false)->count();

        return view('running-logs.index', compact('logs', 'monthlyStats', 'pendingCount'));
    }

    public function create(): View
    {
        return view('running-logs.create');
    }

    // AJAX: 이미지 파싱 → draft INSERT
    public function parseImage(Request $request): JsonResponse
    {
        $request->validate(['image' => ['required', 'image', 'max:10240']]);

        $result = $this->service->parseImage($request->file('image'));

        if (!$result['s3_url']) {
            return response()->json(['success' => false, 'message' => 'CORE API 파싱 실패'], 422);
        }

        $log = $this->service->createDraft($result, $request->user());

        $durationSec = $result['parsed']['duration_seconds'] ?? null;

        return response()->json([
            'success'   => true,
            'log_id'    => $log->id,
            'image_url' => $result['s3_url'],
            'parsed'    => array_merge($result['parsed'], [
                'duration' => $this->secondsToTime($durationSec),
            ]),
            'raw'       => $result['raw_parsed'],
        ]);
    }

    // 사용자 데이터 보정 저장 (is_confirmed 변경 없음 — 관리자 검수 후 확정)
    public function confirm(Request $request, RunningLog $runningLog): RedirectResponse
    {
        abort_if($runningLog->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'run_date'          => ['required', 'date'],
            'distance_km'       => ['required', 'numeric', 'min:0.1', 'max:999'],
            'duration'          => ['required', 'string', 'regex:/^\d+:\d{2}(:\d{2})?$/'],
            'is_indoor'         => ['nullable', 'boolean'],
            'avg_pace_seconds'  => ['nullable', 'integer'],
            'best_pace_seconds' => ['nullable', 'integer'],
            'calories'          => ['nullable', 'integer', 'min:0'],
            'avg_heart_rate'    => ['nullable', 'integer', 'min:0', 'max:300'],
            'elevation_m'       => ['nullable', 'numeric'],
            'weather_desc'      => ['nullable', 'string', 'max:50'],
            'memo'              => ['nullable', 'string', 'max:500'],
        ], [
            'run_date.required'    => '날짜를 입력해주세요.',
            'distance_km.required' => '거리를 입력해주세요.',
            'duration.required'    => '운동 시간을 입력해주세요.',
            'duration.regex'       => '시간 형식이 올바르지 않습니다. (예: 1:23:45)',
        ]);

        $this->service->confirmLog($runningLog, $data);

        return redirect()->route('running-logs.index')->with('success', '러닝 기록이 등록되었습니다.');
    }

    // 다중 이미지 파싱 결과 일괄 확정
    public function batchConfirm(Request $request): JsonResponse
    {
        $request->validate([
            'items'                     => ['required', 'array', 'min:1'],
            'items.*.log_id'            => ['required', 'integer'],
            'items.*.run_date'          => ['required', 'date'],
            'items.*.distance_km'      => ['required', 'numeric', 'min:0.1', 'max:999'],
            'items.*.duration'          => ['required', 'string', 'regex:/^\d+:\d{2}(:\d{2})?$/'],
            'items.*.is_indoor'         => ['nullable'],
            'items.*.avg_pace_seconds'  => ['nullable'],
            'items.*.best_pace_seconds' => ['nullable'],
            'items.*.calories'          => ['nullable'],
            'items.*.avg_heart_rate'    => ['nullable'],
            'items.*.elevation_m'       => ['nullable'],
            'items.*.memo'              => ['nullable', 'string', 'max:500'],
        ]);

        $user      = $request->user();
        $confirmed = 0;

        foreach ($request->input('items') as $item) {
            $log = RunningLog::find($item['log_id']);
            if (!$log || $log->user_id !== $user->id) continue;

            $this->service->confirmLog($log, $item);
            $confirmed++;
        }

        return response()->json([
            'success'  => true,
            'count'    => $confirmed,
            'redirect' => route('running-logs.index'),
        ]);
    }

    private function secondsToTime(?int $seconds): ?string
    {
        if (!$seconds) return null;
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;
        return $h > 0 ? sprintf('%d:%02d:%02d', $h, $m, $s) : sprintf('%d:%02d', $m, $s);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'run_date'       => ['required', 'date'],
            'distance_km'    => ['required', 'numeric', 'min:0.1', 'max:999'],
            'duration'       => ['required', 'string', 'regex:/^\d+:\d{2}(:\d{2})?$/'],
            'is_indoor'      => ['nullable', 'boolean'],
            'calories'       => ['nullable', 'integer', 'min:0'],
            'avg_heart_rate' => ['nullable', 'integer', 'min:0', 'max:300'],
            'elevation_m'    => ['nullable', 'numeric'],
            'weather_desc'   => ['nullable', 'string', 'max:50'],
            'memo'           => ['nullable', 'string', 'max:500'],
            'image'          => ['nullable', 'image', 'max:10240'],
            // 이미지 파싱으로 채워진 hidden 필드
            'avg_pace_seconds'  => ['nullable', 'integer'],
            'best_pace_seconds' => ['nullable', 'integer'],
            'image_url'         => ['nullable', 'string'],
            'parsed_data'       => ['nullable', 'string'],
        ], [
            'run_date.required'    => '날짜를 입력해주세요.',
            'distance_km.required' => '거리를 입력해주세요.',
            'duration.required'    => '운동 시간을 입력해주세요.',
            'duration.regex'       => '시간 형식이 올바르지 않습니다. (예: 1:23:45)',
        ]);

        // 이미지 업로드 → CORE API 파싱
        if ($request->hasFile('image')) {
            $result = $this->service->parseImage($request->file('image'));
            $data['image_url']          = $result['s3_url'];
            $data['parsed_data']        = $result['raw_parsed'] ?: null;
            $data['avg_pace_seconds']   = $result['parsed']['avg_pace_seconds'] ?? $data['avg_pace_seconds'] ?? null;
            $data['best_pace_seconds']  = $result['parsed']['best_pace_seconds'] ?? $data['best_pace_seconds'] ?? null;
        }

        $this->service->create($data, $request->user());

        return redirect()->route('running-logs.index')->with('success', '러닝 기록이 등록되었습니다.');
    }

    public function show(RunningLog $runningLog): View
    {
        abort_if($runningLog->user_id !== auth()->id(), 403);
        return view('running-logs.show', compact('runningLog'));
    }

    public function edit(RunningLog $runningLog): View
    {
        abort_if($runningLog->user_id !== auth()->id(), 403);
        return view('running-logs.edit', compact('runningLog'));
    }

    public function update(Request $request, RunningLog $runningLog): RedirectResponse
    {
        abort_if($runningLog->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'run_date'       => ['required', 'date'],
            'distance_km'    => ['required', 'numeric', 'min:0.1', 'max:999'],
            'duration'       => ['required', 'string', 'regex:/^\d+:\d{2}(:\d{2})?$/'],
            'is_indoor'      => ['nullable', 'boolean'],
            'calories'       => ['nullable', 'integer', 'min:0'],
            'avg_heart_rate' => ['nullable', 'integer', 'min:0', 'max:300'],
            'elevation_m'    => ['nullable', 'numeric'],
            'weather_desc'   => ['nullable', 'string', 'max:50'],
            'memo'           => ['nullable', 'string', 'max:500'],
        ]);

        $this->service->update($runningLog, $data);

        return redirect()->route('running-logs.index')->with('success', '기록이 수정되었습니다.');
    }

    public function destroy(RunningLog $runningLog): RedirectResponse
    {
        abort_if($runningLog->user_id !== auth()->id(), 403);
        $this->service->delete($runningLog);
        return redirect()->route('running-logs.index')->with('success', '기록이 삭제되었습니다.');
    }
}
