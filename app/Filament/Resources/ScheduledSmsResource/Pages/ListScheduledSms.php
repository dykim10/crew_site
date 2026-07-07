<?php

namespace App\Filament\Resources\ScheduledSmsResource\Pages;

use App\Filament\Resources\ScheduledSmsResource;
use Filament\Resources\Pages\ListRecords;

class ListScheduledSms extends ListRecords
{
    protected static string $resource = ScheduledSmsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()->label('예약 등록'),
        ];
    }
}
