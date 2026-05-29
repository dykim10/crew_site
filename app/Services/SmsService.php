<?php

namespace App\Services;

use App\Models\Application;
use App\Models\SmsLog;
use App\Models\User;
use App\Models\UserGeneration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function __construct(private CryptoService $crypto) {}

    public function getPhonesByGeneration(int $generationId): array
    {
        // user_generations → users.email_hash → applications.phone_enc
        $userIds = UserGeneration::where('generation_id', $generationId)
            ->pluck('user_id');

        $emailHashes = User::whereIn('id', $userIds)
            ->whereNotNull('email_hash')
            ->pluck('email_hash');

        $encPhones = Application::whereIn('email_hash', $emailHashes)
            ->where('status', 'approved')
            ->whereNotNull('phone_enc')
            ->pluck('phone_enc');

        return $this->decryptPhones($encPhones->all());
    }

    public function getAllApprovedPhones(): array
    {
        $encPhones = Application::where('status', 'approved')
            ->whereNotNull('phone_enc')
            ->pluck('phone_enc');

        return $this->decryptPhones($encPhones->all());
    }

    public function parseManualPhones(string $input): array
    {
        $phones = [];
        foreach (explode(',', $input) as $raw) {
            $phone = preg_replace('/[^0-9]/', '', trim($raw));
            if (strlen($phone) >= 10) {
                $phones[] = $phone;
            }
        }
        return array_values(array_unique($phones));
    }

    private function decryptPhones(array $encPhones): array
    {
        $phones = [];
        foreach ($encPhones as $enc) {
            $phone = $this->crypto->decrypt($enc);
            if (!$phone) continue;

            $phone = preg_replace('/[^0-9]/', '', $phone);
            if (strlen($phone) >= 10) {
                $phones[] = $phone;
            }
        }
        return array_values(array_unique($phones));
    }

    public function send(array $phones, string $message, string $sender, int $senderId, string $targetType, ?int $targetId): SmsLog
    {
        if (empty($phones)) {
            throw new \RuntimeException('발송 대상 전화번호가 없습니다.');
        }

        $result = $this->callCoreApi($phones, $message, $sender);

        return SmsLog::create([
            'group_id'     => $result['group_id'] ?? null,
            'sent_by'      => $senderId,
            'filter_type'  => $targetType,
            'filter_value' => $targetId !== null ? (string) $targetId : null,
            'recipient_cnt' => count($phones),
            'message'      => $message,
            'status'       => SmsLog::STATUS_SENT,
            'delivered_cnt' => 0,
            'failed_cnt'   => 0,
            'result_data'  => $result,
        ]);
    }

    private function callCoreApi(array $phones, string $message, string $sender): array
    {
        try {
            $response = Http::timeout(60)->post(
                config('services.core_api.url') . '/api/sms/send',
                [
                    'phones'   => $phones,
                    'message'  => $message,
                    'sender'   => $sender,
                ]
            );

            if (!$response->successful()) {
                Log::error('SMS CORE API 호출 실패', ['status' => $response->status(), 'body' => $response->body()]);
                throw new \RuntimeException('SMS 발송 API 오류: ' . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('SMS 발송 예외', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
