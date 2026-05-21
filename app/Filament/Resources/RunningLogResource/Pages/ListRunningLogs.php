<?php

namespace App\Filament\Resources\RunningLogResource\Pages;

use App\Filament\Resources\RunningLogResource;
use Filament\Resources\Pages\ListRecords;

class ListRunningLogs extends ListRecords
{
    protected static string $resource = RunningLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
