<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Filament\Resources\EventResource\Pages\CreateEvent;
use App\Support\EventFormSchema;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (($data['event_type'] ?? $this->record->event_type) === 'B') {
            $data['form_schema'] = EventFormSchema::toBuilderBlocks($data['form_schema'] ?? $this->record->form_schema);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $event = $this->record;
        if ($event->event_type === 'A') {
            $synced = CreateEvent::syncGenerationParticipants($event);
            if ($synced > 0) {
                $this->sendSuccessNotification();
            }
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['event_type'] ?? 'B') === 'B') {
            $data['form_schema'] = EventFormSchema::fromBuilderBlocks(
                $data['form_schema'] ?? [],
                $this->record->form_schema ?? []
            );
        }

        return $data;
    }
}
