<?php

namespace App\Models;

use App\Services\CryptoService;
use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    protected $table = 'crew.event_registrations';

    protected $fillable = [
        'event_id', 'user_id', 'form_data',
        'phone_enc', 'email_enc',
    ];

    protected function casts(): array
    {
        return ['form_data' => 'array'];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 휴대폰번호 복호화
    public function getDecryptedPhone(): ?string
    {
        if (!$this->phone_enc) return null;
        return app(CryptoService::class)->decrypt($this->phone_enc);
    }

    // 이메일 복호화
    public function getDecryptedEmail(): ?string
    {
        if (!$this->email_enc) return null;
        return app(CryptoService::class)->decrypt($this->email_enc);
    }

    // 신청 데이터를 평문으로 반환 (엑셀 출력 등)
    public function getDecryptedFormData(): array
    {
        $schema = $this->event?->form_schema ?? [];
        $result = [];

        foreach ($schema as $field) {
            $key = $field['key'] ?? null;
            $col = $field['data']['column'] ?? null;

            if ($key === null) continue;

            if ($key === 'phone' && $col === 'phone_enc') {
                $result[$key] = $this->getDecryptedPhone() ?? '';
                continue;
            }

            if ($key === 'email' && $col === 'email_enc') {
                $result[$key] = $this->getDecryptedEmail() ?? '';
                continue;
            }

            $result[$key] = $this->form_data[$key] ?? '';
        }

        return $result;
    }

    // form_data 저장 시 phone/email 자동 분리 헬퍼
    public static function buildFromFormInput(array $input, array $schema): array
    {
        $crypto   = app(CryptoService::class);
        $formData = [];
        $phoneEnc = null;
        $emailEnc = null;

        foreach ($schema as $field) {
            $key   = $field['key'] ?? null;
            $col   = $field['data']['column'] ?? null;
            $value = $input[$key] ?? null;

            if ($key === null || $value === null) continue;

            if ($key === 'phone' && $col === 'phone_enc') {
                $phoneEnc = $crypto->encrypt((string) $value);
                continue;
            }

            if ($key === 'email' && $col === 'email_enc') {
                $emailEnc = $crypto->encrypt((string) $value);
                $formData[$key] = $value; // form_data에도 원본 유지(표시용)
                continue;
            }

            $formData[$key] = $value;
        }

        return [
            'form_data' => $formData,
            'phone_enc' => $phoneEnc,
            'email_enc' => $emailEnc,
        ];
    }

    // 이름 마스킹 (김** 형식)
    public function getMaskedName(): string
    {
        $name = $this->form_data['name'] ?? '';
        if (mb_strlen($name) <= 1) return $name;
        return mb_substr($name, 0, 1) . str_repeat('*', mb_strlen($name) - 1);
    }
}
