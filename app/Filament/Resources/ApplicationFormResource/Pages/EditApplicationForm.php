<?php

namespace App\Filament\Resources\ApplicationFormResource\Pages;

use App\Filament\Resources\ApplicationFormResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditApplicationForm extends EditRecord
{
    protected static string $resource = ApplicationFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('삭제'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
