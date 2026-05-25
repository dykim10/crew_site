<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventScore;
use App\Models\Generation;
use App\Models\RunningLog;
use App\Models\UsersDetail;
use App\Services\EventRegistrationService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(private EventRegistrationService $service) {}

    public function index()
    {
        $now = now()->toDateString();

        $active = Event::where('status', 'active')
            ->whereDate('end_date', '>=', $now)
            ->orderByDesc('start_date')
            ->get();

        $upcoming = Event::where('status', 'upcoming')
            ->orderBy('start_date')
            ->get();

        $ended = Event::where('status', 'ended')
            ->orWhere(function ($q) use ($now) {
                $q->where('status', 'active')->whereDate('end_date', '<', $now);
            })
            ->orderByDesc('end_date')
            ->limit(10)
            ->get();

        return view('events.index', compact('active', 'upcoming', 'ended'));
    }

    public function show(Event $event)
    {
        $eligible = $this->checkGenerationEligibility($event, auth()->user());

        // A타입: 마일리지 현황 뷰
        if ($event->isTypeA()) {
            $user       = auth()->user();
            $detail     = UsersDetail::where('user_id', $user->id)->first();
            $grade      = $detail?->grade;
            $gradeRow   = collect($event->score_config ?? [])->firstWhere('grade', $grade);
            $targetKm   = (float) ($gradeRow['target_km'] ?? 0);

            $achievedKm = 0.0;
            if ($event->start_date && $event->end_date) {
                $achievedKm = (float) RunningLog::where('user_id', $user->id)
                    ->where('is_confirmed', true)
                    ->whereBetween('run_date', [
                        $event->start_date->toDateString(),
                        $event->end_date->toDateString(),
                    ])
                    ->sum('distance_km');
            }

            $eventScore = EventScore::where('event_id', $event->id)
                ->where('user_id', $user->id)
                ->first();

            return view('events.show_a', compact(
                'event', 'eligible', 'grade', 'targetKm', 'achievedKm', 'eventScore'
            ));
        }

        // B타입: 동적 신청 폼 뷰
        $alreadyRegistered = $this->service->alreadyRegistered($event, auth()->user());

        return view('events.show', compact('event', 'alreadyRegistered', 'eligible'));
    }

    public function register(Request $request, Event $event)
    {
        abort_if(!$event->isTypeB(), 404);
        abort_if(!$event->is_registration_open, 403, '신청이 마감되었습니다.');

        // 기수 대상 제한 체크
        abort_if(
            !$this->checkGenerationEligibility($event, auth()->user()),
            403,
            '참여 대상 기수가 아닙니다.'
        );

        if ($this->service->alreadyRegistered($event, auth()->user())) {
            return back()->with('error', '이미 신청하셨습니다.');
        }

        // 동적 유효성 검사 규칙 생성
        $rules = [];
        foreach ($event->form_schema ?? [] as $field) {
            $key      = $field['key'] ?? null;
            $type     = $field['type'] ?? 'text';
            $required = $field['data']['required'] ?? false;

            if (!$key) continue;

            $rule = $required ? 'required' : 'nullable';

            $rules["field_{$key}"] = match ($type) {
                'image'    => $rule . '|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
                'checkbox' => $rule . '|array',
                'radio'    => $rule . '|string',
                'textarea' => $rule . '|string|max:3000',
                default    => $rule . '|string|max:500',
            };
        }

        $validated = $request->validate($rules);

        // key → 값 매핑 정규화
        $rawData = [];
        $files   = [];
        foreach ($event->form_schema ?? [] as $field) {
            $key  = $field['key'] ?? null;
            $type = $field['type'] ?? 'text';
            if (!$key) continue;

            if ($type === 'image' && $request->hasFile("field_{$key}")) {
                $files[$key] = $request->file("field_{$key}");
            } else {
                $rawData[$key] = $request->input("field_{$key}");
            }
        }

        $this->service->register($event, auth()->user(), $rawData, $files);

        return redirect()->route('events.show', $event)
            ->with('success', '참가 신청이 완료되었습니다!');
    }

    // target_scope가 'generation'일 때 사용자의 기수 참여 이력 검증
    // 그 외 scope(all/crew/branch/group)는 항상 true 반환
    private function checkGenerationEligibility(Event $event, $user): bool
    {
        if ($event->target_scope !== 'generation' || !$event->generation) {
            return true;
        }

        $generation = Generation::where('number', $event->generation)->first();

        if (!$generation) {
            return false;
        }

        return $user->hasGeneration($generation->id);
    }
}
