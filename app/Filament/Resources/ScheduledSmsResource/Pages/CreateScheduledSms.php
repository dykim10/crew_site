<?php

namespace App\Filament\Resources\ScheduledSmsResource\Pages;

use App\Filament\Resources\ScheduledSmsResource;
use App\Services\ScheduledSmsService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateScheduledSms extends CreateRecord
{
    protected static string $resource = ScheduledSmsResource::class;

    public function canCreateAnother(): bool
    {
        return false;
    }

    protected function getCancelFormAction(): Actions\Action
    {
        return parent::getCancelFormAction()->label('목록으로');
    }

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('예약 등록')
            ->requiresConfirmation()
            ->modalHeading('예약 문자 등록')
            ->modalDescription(function () {
                $data = $this->form->getState();
                $count = count($data['user_ids'] ?? []);
                $at = $data['scheduled_at'] ?? '-';

                return "총 {$count}명에게 {$at} 발송 예약합니다.";
            });
    }

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return app(ScheduledSmsService::class)->create(
                $data,
                $data['user_ids'] ?? [],
                auth()->id(),
            );
        } catch (\InvalidArgumentException $e) {
            Notification::make()->title($e->getMessage())->danger()->send();
            $this->halt();
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['filter_generation_id'], $data['filter_region_id'], $data['filter_training_group']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
