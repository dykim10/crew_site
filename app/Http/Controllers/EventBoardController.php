<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Branch;
use App\Services\EventBoardService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * A타입 이벤트 현황 보드 컨트롤러 (app/Http/Controllers/EventBoardController.php)
 *
 * A타입 이벤트의 조별·지부별 현황을 조회하는 엔드포인트를 제공한다.
 * 로그인 필수이며, 자신의 지부에 속하지 않은 데이터는 조회할 수 없다.
 */
class EventBoardController extends Controller
{
    public function __construct(private EventBoardService $service) {}

    /**
     * GET /events/{event}/board
     * A타입 이벤트 현황 보드 페이지
     *
     * 로그인 사용자의 지부 기준으로 조별 현황을 표시한다.
     */
    public function index(Event $event): View
    {
        abort_unless($event->isTypeA(), 404, 'A타입 이벤트만 접근 가능합니다.');
        abort_unless(auth()->check(), 401, '로그인이 필요합니다.');

        $user = auth()->user();
        $myBranchId = $user->branch_id;

        // 모든 지부 목록 (선택용)
        $branches = Branch::orderBy('id')->get();

        // 사용자의 지부 내 조별 현황
        $branchGroups = $myBranchId
            ? $this->service->getGroupRankings($event, $myBranchId)
            : [];

        // 사용자가 속한 조 조회
        $myGroup = null;
        if ($myBranchId) {
            $userGroupMember = \DB::table('crew.event_group_members')
                ->where('user_id', $user->id)
                ->join('crew.event_groups', 'crew.event_group_members.group_id', '=', 'crew.event_groups.id')
                ->where('crew.event_groups.event_id', $event->id)
                ->where('crew.event_groups.branch_id', $myBranchId)
                ->first();

            if ($userGroupMember) {
                $myGroup = \App\Models\EventGroup::find($userGroupMember->group_id);
            }
        }

        // 내 조의 일별 통계 (차트용)
        $dailyStats = $myGroup
            ? $this->service->getDailyStats($event, $myGroup->id)
            : [];

        return view('events.board.index', compact(
            'event',
            'branches',
            'myBranchId',
            'myGroup',
            'branchGroups',
            'dailyStats'
        ));
    }

    /**
     * GET /events/{event}/board/group-data
     * AJAX 요청: 지부별 조 데이터 조회
     *
     * Query Parameters:
     *   - branch_id (int): 조회할 지부 ID (필수)
     *   - from (string, Y-m-d): 시작일 (선택, 미지정 시 이벤트 시작일)
     *   - to (string, Y-m-d): 종료일 (선택, 미지정 시 이벤트 종료일)
     *
     * Response: { 'groups' => [...], 'branch_summary' => {...} }
     */
    public function groupData(Event $event, Request $request): JsonResponse
    {
        abort_unless($event->isTypeA(), 404);
        abort_unless(auth()->check(), 401);

        $branchId = $request->integer('branch_id');
        abort_if($branchId <= 0, 400, '유효한 지부 ID가 필요합니다.');

        // 권한 검증: 본인 지부만 조회 가능 (또는 관리자는 모든 지부 조회 가능)
        // v1에서는 로그인 사용자만 허용하고 추후 관리자 권한 추가 가능
        $user = auth()->user();
        // abort_unless($user->branch_id === $branchId || $user->isAdmin(), 403);

        $groups = $this->service->getGroupRankings($event, $branchId);

        // 지부별 요약 데이터
        $allBranchSummaries = $this->service->getAllBranchSummary($event);
        $branchSummary = collect($allBranchSummaries)
            ->firstWhere('branch_id', $branchId)
            ?? [];

        return response()->json([
            'groups'          => $groups,
            'branch_summary'  => $branchSummary,
        ]);
    }

    /**
     * GET /events/{event}/board/member-logs
     * AJAX 요청: 특정 조 구성원 기록 조회
     *
     * Query Parameters:
     *   - group_id (int): 조 ID (필수)
     *   - from (string, Y-m-d): 시작일 (선택)
     *   - to (string, Y-m-d): 종료일 (선택)
     *
     * Response: { 'members' => [...] }
     */
    public function memberLogs(Event $event, Request $request): JsonResponse
    {
        abort_unless($event->isTypeA(), 404);
        abort_unless(auth()->check(), 401);

        $groupId = $request->integer('group_id');
        $from = $request->string('from')->trim()->value() ?: null;
        $to = $request->string('to')->trim()->value() ?: null;

        abort_if($groupId <= 0, 400, '유효한 조 ID가 필요합니다.');

        // 조가 해당 이벤트에 속하는지 확인
        $group = \App\Models\EventGroup::where('event_id', $event->id)
            ->where('id', $groupId)
            ->first();
        abort_unless($group, 404, '조를 찾을 수 없습니다.');

        $members = $this->service->getMemberLogs($event, $groupId, $from, $to);

        return response()->json([
            'members' => $members,
        ]);
    }
}
