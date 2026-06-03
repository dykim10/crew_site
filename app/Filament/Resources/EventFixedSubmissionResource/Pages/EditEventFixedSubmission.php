<?php

namespace App\Filament\Resources\EventFixedSubmissionResource\Pages;

use App\Filament\Resources\EventFixedSubmissionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEventFixedSubmission extends EditRecord
{
    protected static string $resource = EventFixedSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => auth()->user()->role === 'super_admin'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
