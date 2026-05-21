<?php

namespace App\Filament\Resources\MeetingMinuteResource\Pages;

use App\Filament\Resources\MeetingMinuteResource;
use Filament\Resources\Pages\EditRecord;

class EditMeetingMinute extends EditRecord
{
    protected static string $resource = MeetingMinuteResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
