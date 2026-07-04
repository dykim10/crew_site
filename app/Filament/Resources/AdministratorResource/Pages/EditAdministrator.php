<?php

namespace App\Filament\Resources\AdministratorResource\Pages;

use App\Filament\Resources\AdministratorResource;
use App\Models\Administrator;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdministrator extends EditRecord
{
    protected static string $resource = AdministratorResource::class;

    protected ?string $existingImagePath = null;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->loadMissing('user');

        $this->existingImagePath = Administrator::normalizeStoragePath($data['profile_image'] ?? null);
        $data['profile_image'] = null;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (blank($data['profile_image'] ?? null)) {
            $data['profile_image'] = $this->existingImagePath;
        }

        if (filled($data['branch_id'] ?? null)) {
            $data['branch_custom'] = null;
        }

        if ($this->record->user_id) {
            $user = $this->record->user;
            $data['name'] = $user?->name ?? $user?->nickname ?? $data['name'] ?? $this->record->name;
        }

        return $data;
    }
}
