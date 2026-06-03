<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventScore;
use App\Models\Generation;
use App\Models\RunningLog;
use App\Models\User;
use App\Models\UsersDetail;
use App\Services\EventRegistrationService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(private EventRegistrationService $service) {}

    // 이벤트 목록 (공개 — 비로그인 접근 가능)
    public function index()
    {
        $active = Event::where('event_type', 'B')
            ->where('status', 'active')
            ->withCount('registrations')
            ->orderBy('start_date')
            ->get();

        $upcoming = Event::where('event_type', 'B')
            ->where('status', 'upcoming')
            ->withCount('registrations')
            ->orderBy('recruit_start_at')
            ->get();

        $ended = Event::where('event_type', 'B')
            ->where('status', 'ended')
            ->withCount('registrations')
            ->orderByDesc('end_date')
            ->limit(12)
            ->get();

        return view('events.index', compact('active', 'upcoming', 'ended'));
    }

    // 이벤트 상세 (공개 — 비로그인 접근 가능, 신청은 로그인 필요)
    public function show(Event $event)
    {
        $user = auth()->user();

        if ($event->isTypeA()) {
            abort_unless($user, 403, '로그인이 필요합니다.');

            $detail     = UsersDetail::where('user_id', $user->id)->first();
            $grade      = $detail?->grade;
            $gradeRow   = collect($event->score_config ?? [])->firstWhere('grade', $grade);
            $targetKm   = (float) ($gradeRow['target_km'] ?? 0);

            $achievedKm = $event->start_date && $event->end_date
                ? (float) RunningLog::where('user_id', $user->id)
                    ->where('is_confirmed', true)
                    ->whereBetween('run_date', [$event->start_date->toDateString(), $event->end_date->toDateString()])
                    ->sum('distance_km')
                : 0.0;

            $eventScore = EventScore::where('event_id', $event->id)->where('user_id', $user->id)->first();

            return view('events.show_a', compact('event', 'grade', 'targetKm', 'achievedKm', 'eventScore'));
        }

        // B타입
        $alreadyRegistered = $this->service->alreadyRegistered($event, $user);
        $eligible          = $this->checkEligibility($event, $user);

        // 참여자 명단 (마스킹)
        $participants = EventRegistration::where('event_id', $event->id)
            ->orderBy('created_at')
            ->get()
            ->map(fn ($r) => $r->getMaskedName());

        return view('events.show', compact('event', 'alreadyRegistered', 'eligible', 'participants'));
    }

    // 이벤트 신청 (로그인 필수)
    public function register(Request $request, Event $event)
    {
        abort_unless(auth()->check(), 401, '로그인이 필요합니다.');
        abort_if(!$event->isTypeB(), 404);
        abort_if(!$event->is_registration_open, 403, '신청이 마감되었습니다.');
        abort_unless($this->checkEligibility($event, auth()->user()), 403, '참여 대상이 아닙니다.');

        if ($this->service->alreadyRegistered($event, auth()->user())) {
            return back()->with('error', '이미 신청하셨습니다.');
        }

        // 동적 유효성 검사 (Builder 형식/flat 형식 모두 처리)
        $rules = [];
        foreach ($event->form_schema ?? [] as $field) {
            $key      = $field['data']['key'] ?? $field['key'] ?? null;
            $type     = $field['type'] ?? 'text';
            $required = $field['data']['required'] ?? $field['required'] ?? false;
            if (!$key) continue;

            $rule               = $required ? 'required' : 'nullable';
            $rules["field_{$key}"] = match ($type) {
                'image'    => $rule . '|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
                'checkbox' => $rule . '|array',
                default    => $rule . '|string|max:500',
            };
        }
        $request->validate($rules);

        // 입력값 수집
        $rawData = [];
        $files   = [];
        foreach ($event->form_schema ?? [] as $field) {
            $key  = $field['data']['key'] ?? $field['key'] ?? null;
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

    // 신청 취소 (로그인 필수, 본인만)
    public function cancel(Event $event)
    {
        abort_unless(auth()->check(), 401);
        $cancelled = $this->service->cancel($event, auth()->user());

        if (!$cancelled) {
            return back()->with('error', '신청 내역을 찾을 수 없습니다.');
        }

        return back()->with('success', '참가 신청이 취소되었습니다.');
    }

    // 기수·지부 참여 자격 체크
    private function checkEligibility(Event $event, ?User $user): bool
    {
        if (!$user) return false;

        if ($event->target_scope === 'generation' && $event->generation) {
            $gen = Generation::where('number', $event->generation)->first();
            return $gen && $user->hasGeneration($gen->id);
        }

        return true;
    }
}
