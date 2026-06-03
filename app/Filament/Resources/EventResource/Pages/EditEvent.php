<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\Event;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => auth()->user()->role === 'super_admin'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // B타입 수정 시: 새로 추가된 Builder 블록만 정규화 (기존 key 유지)
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['event_type'] ?? 'B') === 'B') {
            $data['form_schema'] = $this->normalizeFormSchema(
                $data['form_schema'] ?? [],
                $this->record->form_schema ?? []
            );
        }

        return $data;
    }

    private function normalizeFormSchema(array $blocks, array $existing): array
    {
        // 기존 키 맵 (label → key) 재사용
        $existingKeyMap = [];
        foreach ($existing as $f) {
            if (isset($f['label'], $f['key'])) {
                $existingKeyMap[$f['label']] = $f['key'];
            }
        }

        $defaults = Event::defaultFormSchema();
        $custom   = [];

        foreach ($blocks as $block) {
            $type  = $block['type'] ?? 'text';
            $bdata = $block['data'] ?? [];
            $label = $bdata['label'] ?? '항목';

            // 기존 key 재사용, 없으면 생성
            $key = $existingKeyMap[$label]
                ?? (Str::snake(Str::ascii($label)) ?: ('field_' . count($custom)));

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
