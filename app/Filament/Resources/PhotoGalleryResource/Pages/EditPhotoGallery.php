<?php

namespace App\Filament\Resources\PhotoGalleryResource\Pages;

use App\Filament\Resources\PhotoGalleryResource;
use Filament\Resources\Pages\EditRecord;

class EditPhotoGallery extends EditRecord
{
    protected static string $resource = PhotoGalleryResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['photo_urls'] = array_map(
            fn ($url) => ['url' => $url],
            $data['photo_urls'] ?? []
        );
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['photo_urls'] = array_column($data['photo_urls'] ?? [], 'url');
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
