<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EventRegistrationService
{
    public function __construct(private CryptoService $crypto) {}

    public function register(Event $event, User $user, array $rawData, array $files = []): EventRegistration
    {
        ['form_data' => $formData, 'phone_enc' => $phoneEnc, 'email_enc' => $emailEnc]
            = $this->buildFormData($event, $rawData, $files);

        $reg = EventRegistration::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => $user->id],
            [
                'form_data' => $formData,
                'phone_enc' => $phoneEnc,
                'email_enc' => $emailEnc,
            ]
        );

        // SMS 발송 로직 (주석 해제 시 발송 활성화)
        // $this->sendRegistrationSms($event, $user, $phoneEnc);

        return $reg;
    }

    public function cancel(Event $event, User $user): bool
    {
        return (bool) EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->delete();
    }

    public function alreadyRegistered(Event $event, ?User $user): bool
    {
        if (!$user) return false;

        return EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    // form_schema 기준으로 입력값 정제 + phone_enc / email_enc 분리 저장
    // flat 형식({ key, type, label, ... }) 과 Builder 형식({ type, data.key, data.label, ... }) 모두 처리
    private function buildFormData(Event $event, array $rawData, array $files): array
    {
        $formData = [];
        $phoneEnc = null;
        $emailEnc = null;

        foreach ($event->form_schema ?? [] as $field) {
            // Builder 형식: data.key 우선, 없으면 최상위 key
            $key       = $field['data']['key'] ?? $field['key'] ?? null;
            $type      = $field['type'] ?? 'text';
            $encrypted = $field['data']['encrypted'] ?? $field['encrypted'] ?? false;
            $colHint   = $field['data']['column']    ?? null;

            if (!$key) continue;

            // phone_enc: key='phone' + encrypted=true 또는 명시적 column='phone_enc'
            $isPhoneEnc = ($key === 'phone' && ($encrypted || $colHint === 'phone_enc'))
                       || $colHint === 'phone_enc';

            // email_enc: key='email' + encrypted=true 또는 명시적 column='email_enc'
            $isEmailEnc = ($key === 'email' && ($encrypted || $colHint === 'email_enc'))
                       || $colHint === 'email_enc';

            if ($isPhoneEnc) {
                $value    = $rawData[$key] ?? null;
                $phoneEnc = $value ? $this->crypto->encrypt((string) $value) : null;
                continue;
            }

            if ($isEmailEnc) {
                $value    = $rawData[$key] ?? null;
                $emailEnc = $value ? $this->crypto->encrypt((string) $value) : null;
                continue;
            }

            // 이미지 파일
            if ($type === 'image' && isset($files[$key]) && $files[$key] instanceof UploadedFile) {
                $path           = $files[$key]->store('event-registrations', 'public');
                $formData[$key] = Storage::url($path);
                continue;
            }

            // 체크박스 (배열)
            if ($type === 'checkbox') {
                $formData[$key] = (array) ($rawData[$key] ?? []);
                continue;
            }

            $formData[$key] = $rawData[$key] ?? null;
        }

        return ['form_data' => $formData, 'phone_enc' => $phoneEnc, 'email_enc' => $emailEnc];
    }

    /*
    // SMS 발송 (주석 해제 시 활성화)
    private function sendRegistrationSms(Event $event, User $user, ?string $phoneEnc): void
    {
        if (!$phoneEnc) return;
        $phone = $this->crypto->decrypt($phoneEnc);
        $msg   = "[PAC-RUN] {$event->name} 참가 신청이 완료되었습니다.";
        app(\App\Services\SmsService::class)->send($phone, $msg);
    }
    */
}
