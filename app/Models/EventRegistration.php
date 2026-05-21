<?php

namespace App\Models;

use App\Services\CryptoService;
use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    protected $table = 'crew.event_registrations';

    protected $fillable = ['event_id', 'user_id', 'form_data'];

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

    // form_data를 복호화해서 반환 (encrypted 필드 자동 처리)
    public function getDecryptedFormData(): array
    {
        $schema = $this->event?->form_schema ?? [];
        $data   = $this->form_data ?? [];
        $crypto = app(CryptoService::class);
        $result = [];

        foreach ($schema as $field) {
            $key   = $field['key'] ?? null;
            $value = $data[$key] ?? null;

            if ($key === null || $value === null) continue;

            $encrypted = $field['data']['encrypted'] ?? false;
            $result[$key] = ($encrypted && is_string($value) && str_starts_with($value, 'enc:'))
                ? $crypto->decrypt(substr($value, 4))
                : $value;
        }

        return $result;
    }
}
