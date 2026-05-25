<?php

namespace App\Filament\Resources\GenerationResource\Pages;

use App\Filament\Resources\GenerationResource;
use App\Models\Generation;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditGeneration extends EditRecord
{
    protected static string $resource = GenerationResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->validateDateOverlap($data['start_date'] ?? null, $data['end_date'] ?? null, excludeId: $this->record->id);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    private function validateDateOverlap(?string $start, ?string $end, ?int $excludeId): void
    {
        if (!$start || !$end) {
            return;
        }

        if ($end < $start) {
            Notification::make()
                ->title('날짜 오류')
                ->body('운영 종료일은 시작일보다 이후여야 합니다.')
                ->danger()
                ->send();

            $this->halt();
        }

        $overlapping = Generation::whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->where('start_date', '<=', $end)
            ->where('end_date', '>=', $start)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->first();

        if ($overlapping) {
            $overlapStart = $overlapping->start_date->format('Y.m.d');
            $overlapEnd   = $overlapping->end_date->format('Y.m.d');

            Notification::make()
                ->title('운영 기간 중복')
                ->body("기존 {$overlapping->number}기 ({$overlapStart} ~ {$overlapEnd})와 운영 기간이 겹칩니다. 날짜를 조정해 주세요.")
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }
    }
}
