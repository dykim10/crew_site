<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EventGroupExport;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Event;
use App\Models\Generation;
use App\Models\User;
use App\Models\UserGeneration;
use App\Services\EventGroupService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EventGroupController extends Controller
{
    public function __construct(private EventGroupService $service) {}

    /**
     * 조 편성 팝업 페이지
     * GET /admin/events/{event}/groups
     */
    public function index(Event $event)
    {
        abort_unless($event->isTypeA(), 404);
        abort_unless(
            in_array(auth()->user()->role, ['super_admin', 'region_admin']),
            403
        );

        // 이벤트에 참여하는 지부 목록
        // (기수 구성원의 branch_id 기준으로 실제 존재하는 지부만 노출)
        $branches = $this->resolveBranches($event);

        return view('admin.events.group_assign', compact('event', 'branches'));
    }

    /**
     * 지부별 구성원 + 현재 조 편성 데이터 반환 (AJAX)
     * GET /admin/events/{event}/groups/data?branch_id=1
     */
    public function data(Event $event, Request $request)
    {
        abort_unless($event->isTypeA(), 404);
        abort_unless(
            in_array(auth()->user()->role, ['super_admin', 'region_admin']),
            403
        );

        $branchId = (int) $request->input('branch_id');

        // 기수 구성원 중 해당 지부 소속 전체
        $members = $this->resolveBranchMembers($event, $branchId);

        // 현재 편성된 조
        $groups = $this->service->getGroups($event->id, $branchId)
            ->map(fn ($g) => [
                'id'         => $g->id,
                'group_no'   => $g->group_no,
                'group_name' => $g->group_name,
                'members'    => $g->members->map(fn ($m) => $m->user_id)->values(),
            ]);

        return response()->json([
            'members' => $members,
            'groups'  => $groups,
        ]);
    }

    /**
     * 조 편성 저장 (AJAX)
     * POST /admin/events/{event}/groups/save
     */
    public function save(Event $event, Request $request)
    {
        abort_unless($event->isTypeA(), 404);
        abort_unless(
            in_array(auth()->user()->role, ['super_admin', 'region_admin']),
            403
        );

        $validated = $request->validate([
            'branch_id'            => 'required|integer|exists:branches,id',
            'groups'               => 'required|array',
            'groups.*.group_name'  => 'required|string|max:50',
            'groups.*.members'     => 'present|array',
            'groups.*.members.*'   => 'integer|exists:users,id',
        ]);

        $this->service->saveGroups($event->id, $validated['branch_id'], $validated['groups']);

        return response()->json(['message' => '조 편성이 저장되었습니다.']);
    }

    private function resolveBranches(Event $event): \Illuminate\Support\Collection
    {
        if (!$event->generation) return collect();

        $gen = Generation::where('number', $event->generation)->first();
        if (!$gen) return collect();

        $branchIds = UserGeneration::where('generation_id', $gen->id)
            ->join('users', 'users.id', '=', 'user_generations.user_id')
            ->whereNotNull('users.branch_id')
            ->distinct()
            ->pluck('users.branch_id');

        return Branch::whereIn('id', $branchIds)->orderBy('name')->get(['id', 'name']);
    }

    private function resolveBranchMembers(Event $event, int $branchId): array
    {
        if (!$event->generation) return [];

        $gen = Generation::where('number', $event->generation)->first();
        if (!$gen) return [];

        $userIds = UserGeneration::where('generation_id', $gen->id)->pluck('user_id');

        return User::whereIn('users.id', $userIds)
            ->where('users.branch_id', $branchId)
            ->leftJoin('crew.users_detail as ud', 'ud.user_id', '=', 'users.id')
            ->get(['users.id', 'users.name', 'users.branch_id', 'ud.grade'])
            ->map(fn ($u) => [
                'id'    => $u->id,
                'name'  => $u->name,
                'grade' => $u->grade ?? null,
            ])
            ->sortBy('name')
            ->values()
            ->toArray();
    }

    /**
     * A타입 이벤트 엑셀 다운로드
     * GET /admin/events/{event}/groups/excel?branch_id=
     */
    public function downloadExcel(Event $event, Request $request)
    {
        abort_unless($event->isTypeA(), 404);
        abort_unless(
            in_array(auth()->user()->role, ['super_admin', 'region_admin']),
            403
        );

        $branchId = $request->input('branch_id') ? (int) $request->input('branch_id') : null;

        // EventGroupExport 인스턴스 생성 (지부별 필터 적용)
        $export = new EventGroupExport($event, $branchId);

        return Excel::download(
            $export,
            EventGroupExport::filename($event->name)
        );
    }
}
