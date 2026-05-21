<?php

namespace App\Filament\Resources\MeetingMinuteResource\Pages;

use App\Filament\Resources\MeetingMinuteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMeetingMinutes extends ListRecords
{
    protected static string $resource = MeetingMinuteResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('회의록 등록')];
    }
}
