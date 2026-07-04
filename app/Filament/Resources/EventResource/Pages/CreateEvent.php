<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\Event;
use App\Support\EventFormSchema;
use App\Models\EventScore;
use App\Models\Generation;
use App\Models\UserGeneration;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // A타입 생성 후 기수 구성원 → EventScore 일괄 생성
    protected function afterCreate(): void
    {
        $event = $this->record;
        if ($event->event_type === 'A') {
            self::syncGenerationParticipants($event);
        }
    }

    public static function syncGenerationParticipants(Event $event): int
    {
        if (!$event->generation) return 0;

        $gen = Generation::where('number', $event->generation)->first();
        if (!$gen) return 0;

        $userIds = UserGeneration::where('generation_id', $gen->id)->pluck('user_id');
        $synced  = 0;

        foreach ($userIds as $userId) {
            $created = EventScore::firstOrCreate(
                ['event_id' => $event->id, 'user_id' => $userId],
                ['score' => 0, 'source' => 'auto_generation']
            );
            if ($created->wasRecentlyCreated) $synced++;
        }

        return $synced;
    }

    // B타입 생성 시 form_schema 정규화 + 기본 필드(성명/휴대폰) 자동 선행 삽입
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['event_type'] ?? 'B') === 'B') {
            $data['form_schema'] = EventFormSchema::fromBuilderBlocks($data['form_schema'] ?? []);
        }

        return $data;
    }
}
