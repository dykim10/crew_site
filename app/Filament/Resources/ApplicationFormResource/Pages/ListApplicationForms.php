<?php

namespace App\Filament\Resources\ApplicationFormResource\Pages;

use App\Filament\Resources\ApplicationFormResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListApplicationForms extends ListRecords
{
    protected static string $resource = ApplicationFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('신청서 폼 생성'),
        ];
    }
}
