<?php

namespace App\Filament\Resources\ApplicationFormResource\Pages;

use App\Filament\Resources\ApplicationFormResource;
use Filament\Resources\Pages\CreateRecord;

class CreateApplicationForm extends CreateRecord
{
    protected static string $resource = ApplicationFormResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
