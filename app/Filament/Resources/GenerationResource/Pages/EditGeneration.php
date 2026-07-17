<?php

namespace App\Filament\Resources\GenerationResource\Pages;

use App\Filament\Resources\GenerationResource;
use App\Models\Generation;
use App\Models\GoogleForm;
use App\Services\GenerationVisibilityService;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditGeneration extends EditRecord
{
    protected static string $resource = GenerationResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return GenerationResource::splitMainRacesForForm($data, $this->record);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->validateDateOverlap($data['start_date'] ?? null, $data['end_date'] ?? null, excludeId: $this->record->id);

        if (($data['apply_method'] ?? 'internal') === 'google_form' && empty($data['google_form_id'])) {
            Notification::make()
                ->title('구글 폼 필요')
                ->body('먼저 «구글 폼 연동»에서 이 기수용 폼을 등록해주세요.')
                ->danger()
                ->send();
            $this->halt();
        }

        $cached = Cache::get('core_api_editions_upcoming');
        $editionMeta = (is_array($cached) && isset($cached['meta']) && is_array($cached['meta']))
            ? $cached['meta']
            : (Cache::get('core_api_editions_upcoming_meta') ?? []);
        $data = GenerationResource::composeMainRaces($data, is_array($editionMeta) ? $editionMeta : []);

        if (($data['apply_method'] ?? '') !== 'google_form') {
            $data['google_form_id'] = null;
        } else {
            $data['application_form_id'] = null;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $formId = $this->record->google_form_id;
        if ($formId && $this->record->apply_method === 'google_form') {
            GoogleForm::where('id', $formId)->update([
                'purpose'       => GoogleForm::PURPOSE_GENERATION_RECRUIT,
                'generation_id' => $this->record->id,
            ]);
        }
        GenerationVisibilityService::forgetCache();
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
