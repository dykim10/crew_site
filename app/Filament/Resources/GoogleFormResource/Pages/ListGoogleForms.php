<?php

namespace App\Filament\Resources\GoogleFormResource\Pages;

use App\Filament\Resources\GoogleFormResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGoogleForms extends ListRecords
{
    protected static string $resource = GoogleFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('폼 추가'),
        ];
    }
}
