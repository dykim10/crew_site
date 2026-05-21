<?php

namespace App\Filament\Resources\RunningLogResource\Pages;

use App\Filament\Resources\RunningLogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRunningLog extends EditRecord
{
    protected static string $resource = RunningLogResource::class;

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
