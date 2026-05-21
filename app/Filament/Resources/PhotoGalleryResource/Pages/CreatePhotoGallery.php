<?php

namespace App\Filament\Resources\PhotoGalleryResource\Pages;

use App\Filament\Resources\PhotoGalleryResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePhotoGallery extends CreateRecord
{
    protected static string $resource = PhotoGalleryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id']    = auth()->id();
        $data['photo_urls'] = array_column($data['photo_urls'] ?? [], 'url');
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
