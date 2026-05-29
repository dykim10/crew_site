<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SmsWebhookController extends Controller
{
    /**
     * Solapi 단건 수신 결과 웹훅 처리
     *
     * 등록: Solapi 콘솔 → 웹훅 → URL: https://crew.pac-run.com/webhooks/sms?token={SMS_WEBHOOK_SECRET}
     * 페이로드: {"type":"STATUS","data":{"groupId":"G4V...","statusCode":"2000",...}}
     */
    public function handle(Request $request): JsonResponse
    {
        // Secret token 검증 (Solapi 콘솔에 등록된 URL에 포함)
        $secret = config('services.sms.webhook_secret');
        if ($secret && $request->query('token') !== $secret) {
            Log::warning('SMS 웹훅 인증 실패', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // STATUS 타입만 처리 (RECEIVE 등 다른 이벤트 무시)
        if ($request->input('type') !== 'STATUS') {
            return response()->json(['ok' => true]);
        }

        $data       = $request->input('data', []);
        $groupId    = $data['groupId']    ?? null;
        $statusCode = $data['statusCode'] ?? null;

        if (!$groupId || !$statusCode) {
            return response()->json(['ok' => true]);
        }

        $log = SmsLog::where('group_id', $groupId)->first();
        if (!$log) {
            // 다른 채널 발송 건이거나 group_id 미저장 건 — 무시
            return response()->json(['ok' => true]);
        }

        DB::transaction(function () use ($log, $statusCode) {
            // 2000 = 정상 수신, 그 외 = 실패
            if ($statusCode === '2000') {
                $log->increment('delivered_cnt');
            } else {
                $log->increment('failed_cnt');
            }

            $log->refresh();
            $total = $log->delivered_cnt + $log->failed_cnt;

            // 모든 메시지 결과 수신 완료 시 최종 상태 확정
            if ($total >= $log->recipient_cnt && $log->status === SmsLog::STATUS_SENT) {
                $status = match (true) {
                    $log->failed_cnt === 0    => SmsLog::STATUS_DELIVERED,
                    $log->delivered_cnt === 0 => SmsLog::STATUS_FAILED,
                    default                   => SmsLog::STATUS_PARTIAL,
                };
                $log->update(['status' => $status]);
            }
        });

        return response()->json(['ok' => true]);
    }
}
