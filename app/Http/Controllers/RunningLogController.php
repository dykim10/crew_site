<?php

namespace App\Http\Controllers;

use App\Models\RunningLog;
use App\Services\RunningLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RunningLogController extends Controller
{
    public function __construct(private RunningLogService $service) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $logs = $this->service->getByUser($user);
        $now  = now();
        $monthlyStats = $this->service->getMonthlyStats($user, $now->year, $now->month);

        return view('running-logs.index', compact('logs', 'monthlyStats'));
    }

    public function create(): View
    {
        return view('running-logs.create');
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
