<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\Event;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // B타입 생성 시 form_schema 정규화 + 기본 필드(성명/휴대폰) 자동 선행 삽입
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['event_type'] ?? 'B') === 'B') {
            $data['form_schema'] = $this->normalizeFormSchema($data['form_schema'] ?? []);
        }

        return $data;
    }

    private function normalizeFormSchema(array $blocks): array
    {
        $defaults = Event::defaultFormSchema();

        $custom = [];
        foreach ($blocks as $block) {
            $type  = $block['type'] ?? 'text';
            $bdata = $block['data'] ?? [];
            $label = $bdata['label'] ?? '항목';
            $key   = Str::snake(Str::ascii($label)) ?: ('field_' . count($custom));

            // name/phone 키 충돌 방지
            if (in_array($key, ['name', 'phone'])) {
                $key = 'custom_' . $key;
            }

            $custom[] = [
                'key'      => $key,
                'label'    => $label,
                'type'     => $type,
                'required' => (bool) ($bdata['required'] ?? false),
                'data'     => $bdata,
            ];
        }

        return array_merge($defaults, $custom);
    }
}
