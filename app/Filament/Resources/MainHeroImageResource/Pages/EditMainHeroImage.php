<?php

namespace App\Filament\Resources\MainHeroImageResource\Pages;

use App\Filament\Resources\MainHeroImageResource;
use App\Models\MainHeroImage;
use App\Services\MainHeroImageService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMainHeroImage extends EditRecord
{
    protected static string $resource = MainHeroImageResource::class;

    protected ?string $existingImagePath = null;

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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->existingImagePath = MainHeroImage::normalizeStoragePath($data['image_path'] ?? null);
        $data['image_path'] = null;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $newImage = $data['image_path'] ?? null;
        if (is_array($newImage)) {
            $newImage = filled($newImage) ? reset($newImage) : null;
        }

        $data['image_path'] = filled($newImage)
            ? MainHeroImage::normalizeStoragePath($newImage)
            : $this->existingImagePath;

        return $data;
    }

    protected function afterSave(): void
    {
        $newPath = MainHeroImage::normalizeStoragePath($this->record->image_path);
        if ($this->existingImagePath && $newPath !== $this->existingImagePath) {
            app(MainHeroImageService::class)->deleteFromStorage($this->existingImagePath);
        }
    }
}
