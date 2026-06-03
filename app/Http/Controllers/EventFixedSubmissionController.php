<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventFixedSubmission;
use App\Services\EventFixedSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * 고정점수 이벤트 제출물 컨트롤러 (app/Http/Controllers/EventFixedSubmissionController.php)
 *
 * score_type = 'fixed' 인 A타입 이벤트에서 사용자가 미션 제출물(이미지)을 업로드하는 기능을 담당한다.
 * 비즈니스 로직은 EventFixedSubmissionService 에 위임한다.
 *
 * [접근 제한]
 *   - 로그인 필수 (auth 미들웨어)
 *   - score_type = 'fixed' 인 이벤트만 접근 허용
 *
 * [이미지 검증]
 *   - image, max:10240 (10MB), mimes:jpg,jpeg,png,webp
 *   - S3 에 업로드: Storage::disk('s3')->put("events/submissions/{eventId}/{userId}/...", $file)
 *
 * [제출 규칙]
 *   - 중복 제출 허용 (status=rejected 후 재제출 가능)
 *   - 이전 제출 내역은 모두 조회 가능 (status 배지 포함)
 */
class EventFixedSubmissionController extends Controller
{
    public function __construct(private EventFixedSubmissionService $service) {}

    /**
     * 이미지 업로드 및 제출물 생성 (POST /events/{event}/submit-fixed)
     *
     * @param Request $request
     * @param Event $event score_type='fixed' 인지 검증
     * @return RedirectResponse
     */
    public function store(Request $request, Event $event): RedirectResponse
    {
        // score_type='fixed' 확인
        if ($event->score_type !== 'fixed') {
            abort(404, '고정점수 이벤트만 접근 가능합니다.');
        }

        // 이미지 검증
        $validated = $request->validate([
            'image' => ['required', 'image', 'max:10240', 'mimes:jpg,jpeg,png,webp'],
        ], [
            'image.required' => '이미지를 선택해주세요.',
            'image.image'    => '유효한 이미지 파일이어야 합니다.',
            'image.max'      => '이미지 크기는 10MB 이하여야 합니다.',
            'image.mimes'    => 'JPG, PNG, WebP 형식만 지원합니다.',
        ]);

        // Service 에 위임
        try {
            $this->service->storeSubmission(
                $event,
                $request->user(),
                $request->file('image')
            );

            return redirect()->back()
                ->with('success', '제출이 완료되었습니다. 관리자 검수 후 점수가 부여됩니다.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Fixed submission upload failed', [
                'event_id' => $event->id,
                'user_id'  => $request->user()->id,
                'error'    => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withErrors(['image' => '제출 중 오류가 발생했습니다. 다시 시도해주세요.']);
        }
    }

    /**
     * 사용자의 해당 이벤트 제출 내역 조회 (GET /events/{event}/my-submissions)
     *
     * AJAX 응답 JSON: [{id, status, image_url, created_at, admin_note, confirmed_at}]
     *
     * @param Request $request
     * @param Event $event
     * @return JsonResponse
     */
    public function mySubmissions(Request $request, Event $event): JsonResponse
    {
        // score_type='fixed' 확인
        if ($event->score_type !== 'fixed') {
            return response()->json(['error' => 'Not found'], 404);
        }

        $submissions = EventFixedSubmission::query()
            ->where('event_id', $event->id)
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function (EventFixedSubmission $sub) {
                return [
                    'id'            => $sub->id,
                    'status'        => $sub->status,
                    'status_label'  => $this->getStatusLabel($sub->status),
                    'image_url'     => $sub->image_url,
                    'created_at'    => $sub->created_at->format('Y.m.d H:i'),
                    'confirmed_at'  => $sub->confirmed_at ? $sub->confirmed_at->format('Y.m.d H:i') : null,
                    'admin_note'    => $sub->admin_note,
                ];
            });

        return response()->json(['submissions' => $submissions]);
    }

    /**
     * 상태 코드 → 한글 라벨 변환
     */
    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'pending'  => '검수 대기',
            'approved' => '승인 완료',
            'rejected' => '반려됨',
            default    => $status,
        };
    }
}
