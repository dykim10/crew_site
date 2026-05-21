<?php

namespace App\Filament\Resources\MeetingMinuteResource\Pages;

use App\Filament\Resources\MeetingMinuteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMeetingMinute extends CreateRecord
{
    protected static string $resource = MeetingMinuteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
