<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventFixedSubmission;
use App\Models\EventScore;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * 고정점수 이벤트 제출물 서비스 (app/Services/EventFixedSubmissionService.php)
 *
 * score_type = 'fixed' 인 이벤트의 사용자 제출물(이미지) 관리를 담당한다.
 *
 * [기능]
 *   - storeSubmission()  : 이미지 업로드 → S3 저장 → DB 기록
 *   - deleteSubmission() : 제출물 삭제 (S3 + DB)
 *   - approve()          : 제출물 승인 + 점수 부여
 *   - reject()           : 제출물 반려 + 피드백
 */
class EventFixedSubmissionService
{
    /**
     * 제출물(이미지) 저장
     *
     * S3 경로: events/submissions/{eventId}/{userId}/{timestamp}.{ext}
     * DB: crew.event_fixed_submissions 에 status='pending' 으로 기록
     *
     * @param Event $event
     * @param User $user
     * @param UploadedFile $image
     * @return EventFixedSubmission
     */
    public function storeSubmission(Event $event, User $user, UploadedFile $image): EventFixedSubmission
    {
        // S3 경로 생성
        $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $path = "events/submissions/{$event->id}/{$user->id}/{$fileName}";

        // S3 에 업로드
        $s3Url = Storage::disk('s3')->put($path, file_get_contents($image->getRealPath()))
            ? Storage::disk('s3')->url($path)
            : null;

        if (!$s3Url) {
            throw new \RuntimeException('S3 업로드 실패');
        }

        // DB 기록 (중복 제출은 새로운 행으로 생성)
        $submission = EventFixedSubmission::create([
            'event_id'     => $event->id,
            'user_id'      => $user->id,
            'image_url'    => $s3Url,
            'status'       => 'pending',
            'admin_note'   => null,
            'confirmed_by' => null,
            'confirmed_at' => null,
        ]);

        Log::info('Fixed submission created', [
            'submission_id' => $submission->id,
            'event_id'      => $event->id,
            'user_id'       => $user->id,
            's3_url'        => $s3Url,
        ]);

        return $submission;
    }

    /**
     * 제출물 삭제 (S3 + DB)
     *
     * 관리자 또는 자신이 제출한 경우만 삭제 가능
     *
     * @param EventFixedSubmission $submission
     * @param User $user 요청 사용자
     * @return bool
     */
    public function deleteSubmission(EventFixedSubmission $submission, User $user): bool
    {
        // 소유자 확인 (또는 관리자)
        if ($submission->user_id !== $user->id && !in_array($user->role, ['super_admin', 'region_admin'])) {
            abort(403, '권한이 없습니다.');
        }

        // S3 이미지 삭제
        if ($submission->image_url) {
            try {
                // S3 URL → 경로 추출
                $path = $this->extractS3Path($submission->image_url);
                if ($path) {
                    Storage::disk('s3')->delete($path);
                }
            } catch (\Exception $e) {
                Log::warning('S3 image delete failed', [
                    'submission_id' => $submission->id,
                    'url'           => $submission->image_url,
                    'error'         => $e->getMessage(),
                ]);
            }
        }

        // DB 삭제
        $deleted = $submission->delete();

        if ($deleted) {
            Log::info('Fixed submission deleted', [
                'submission_id' => $submission->id,
                'event_id'      => $submission->event_id,
                'user_id'       => $submission->user_id,
            ]);
        }

        return (bool) $deleted;
    }

    /**
     * S3 URL → 저장 경로 추출
     *
     * URL 형식: https://bucket.s3.region.amazonaws.com/path/to/file
     *          또는 CloudFront: https://cdn.example.com/path/to/file
     *
     * @param string $url
     * @return string|null
     */
    private function extractS3Path(string $url): ?string
    {
        // amazonaws 패턴
        if (preg_match('#https?://.*?\.s3[.-].*?\.amazonaws\.com/(.+)$#', $url, $m)) {
            return $m[1];
        }

        // CloudFront CDN 패턴 (직접 경로로 저장되어 있다고 가정)
        if (preg_match('#https?://[^/]+/(.+)$#', $url, $m)) {
            return $m[1];
        }

        return null;
    }
    /**
     * 제출물 승인 처리
     *
     * @param EventFixedSubmission $submission
     * @param int $adminUserId
     * @return void
     */
    public function approve(EventFixedSubmission $submission, int $adminUserId): void
    {
        DB::transaction(function () use ($submission, $adminUserId) {
            // 이미 승인된 제출물은 중복 처리 방지
            if ($submission->isApproved()) {
                return;
            }

            // 상태 업데이트
            $submission->update([
                'status'       => 'approved',
                'confirmed_by' => $adminUserId,
                'confirmed_at' => now(),
            ]);

            // event.score_rules 에서 fixed_score 추출
            $event = $submission->event;
            $scoreRules = $event->score_rules ?? [];
            $fixedScore = $scoreRules['fixed_score'] ?? 0;

            // EventScore updateOrCreate
            // UNIQUE(event_id, user_id) 제약 조건으로 중복 방지
            EventScore::updateOrCreate(
                [
                    'event_id' => $submission->event_id,
                    'user_id'  => $submission->user_id,
                ],
                [
                    'score'     => $fixedScore,
                    'source'    => 'fixed_submission',
                    'awarded_by' => $adminUserId,
                ]
            );
        });
    }

    /**
     * 제출물 반려 처리
     *
     * @param EventFixedSubmission $submission
     * @param int $adminUserId
     * @param string $note
     * @return void
     */
    public function reject(EventFixedSubmission $submission, int $adminUserId, string $note): void
    {
        DB::transaction(function () use ($submission, $adminUserId, $note) {
            $submission->update([
                'status'       => 'rejected',
                'confirmed_by' => $adminUserId,
                'confirmed_at' => now(),
                'admin_note'   => $note,
            ]);

            // 승인 취소 시 EventScore 제거 (이미 생성된 점수가 있으면 삭제)
            EventScore::where('event_id', $submission->event_id)
                ->where('user_id', $submission->user_id)
                ->where('source', 'fixed_submission')
                ->delete();
        });
    }

    /**
     * 이벤트별 제출 통계 조회
     *
     * @param int $eventId
     * @return array
     */
    public function stats(int $eventId): array
    {
        $submissions = EventFixedSubmission::where('event_id', $eventId);

        return [
            'pending'  => $submissions->pending()->count(),
            'approved' => $submissions->approved()->count(),
            'rejected' => $submissions->where('status', 'rejected')->count(),
        ];
    }
}
