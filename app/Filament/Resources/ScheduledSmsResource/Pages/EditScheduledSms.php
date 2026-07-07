<?php

namespace App\Filament\Resources\ScheduledSmsResource\Pages;

use App\Filament\Resources\ScheduledSmsResource;
use App\Models\ScheduledSms;
use App\Services\ScheduledSmsService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditScheduledSms extends EditRecord
{
    protected static string $resource = ScheduledSmsResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if (!$this->getRecord()->isEditable()) {
            Notification::make()
                ->title('수정 불가')
                ->body('테스트 발송 이후에는 수정할 수 없습니다.')
                ->warning()
                ->send();

            $this->redirect($this->getResource()::getUrl('view', ['record' => $this->getRecord()]));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()->label('상세'),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return app(ScheduledSmsService::class)->update(
                $record,
                $data,
                $data['user_ids'] ?? [],
            );
        } catch (\RuntimeException|\InvalidArgumentException $e) {
            Notification::make()->title($e->getMessage())->danger()->send();
            $this->halt();
        }
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var ScheduledSms $record */
        $record = $this->getRecord();
        $data['user_ids'] = $record->recipients()->pluck('user_id')->all();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['filter_generation_id'], $data['filter_region_id'], $data['filter_training_group']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
