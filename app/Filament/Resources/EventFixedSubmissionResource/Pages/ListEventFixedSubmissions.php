<?php

namespace App\Filament\Resources\EventFixedSubmissionResource\Pages;

use App\Filament\Resources\EventFixedSubmissionResource;
use Filament\Resources\Pages\ListRecords;

class ListEventFixedSubmissions extends ListRecords
{
    protected static string $resource = EventFixedSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
