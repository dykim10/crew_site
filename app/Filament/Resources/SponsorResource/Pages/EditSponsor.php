<?php

namespace App\Filament\Resources\SponsorResource\Pages;

use App\Filament\Resources\SponsorResource;
use App\Models\Sponsor;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSponsor extends EditRecord
{
    protected static string $resource = SponsorResource::class;

    protected ?string $existingLogoUrl = null;

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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->existingLogoUrl = Sponsor::normalizeStoragePath($data['logo_url'] ?? null);
        $data['logo_url'] = null;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $newLogo = $data['logo_url'] ?? null;
        if (is_array($newLogo)) {
            $newLogo = filled($newLogo) ? reset($newLogo) : null;
        }

        $data['logo_url'] = filled($newLogo)
            ? Sponsor::normalizeStoragePath($newLogo)
            : $this->existingLogoUrl;

        return $data;
    }
}
