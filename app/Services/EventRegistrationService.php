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
        $formData = $this->buildFormData($event, $rawData, $files);

        return EventRegistration::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => $user->id],
            ['form_data' => $formData]
        );
    }

    // schema 기준으로 입력값 정제 + 암호화
    private function buildFormData(Event $event, array $rawData, array $files): array
    {
        $result = [];

        foreach ($event->form_schema ?? [] as $field) {
            $key       = $field['key'] ?? null;
            $type      = $field['type'] ?? 'text';
            $encrypted = $field['data']['encrypted'] ?? false;

            if (!$key) continue;

            if ($type === 'image') {
                if (isset($files[$key]) && $files[$key] instanceof UploadedFile) {
                    $path          = $files[$key]->store('event-registrations', 'public');
                    $result[$key]  = Storage::url($path);
                }
            } elseif ($type === 'checkbox') {
                $result[$key] = (array) ($rawData[$key] ?? []);
            } else {
                $value = $rawData[$key] ?? null;
                if ($value !== null && $encrypted) {
                    $result[$key] = 'enc:' . $this->crypto->encrypt($value);
                } else {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

    public function alreadyRegistered(Event $event, User $user): bool
    {
        return EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->exists();
    }
}
