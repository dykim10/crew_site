<?php

namespace App\Filament\Resources\MainHeroImageResource\Pages;

use App\Filament\Resources\MainHeroImageResource;
use App\Models\MainHeroImage;
use Filament\Resources\Pages\CreateRecord;

class CreateMainHeroImage extends CreateRecord
{
    protected static string $resource = MainHeroImageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (is_array($data['image_path'] ?? null)) {
            $data['image_path'] = filled($data['image_path']) ? reset($data['image_path']) : null;
        }

        $data['image_path'] = MainHeroImage::normalizeStoragePath($data['image_path'] ?? null);
        $data['is_active'] = true;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
