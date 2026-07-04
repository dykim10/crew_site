<?php

namespace App\Filament\Resources\BranchResource\Pages;

use App\Filament\Resources\BranchResource;
use App\Models\Branch;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBranch extends EditRecord
{
    protected static string $resource = BranchResource::class;

    protected ?string $existingImageUrl = null;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * 기존 S3 이미지를 FileUpload에 넣지 않음.
     * FilePond가 CDN URL을 cross-origin fetch 하며 CORS 오류가 발생하기 때문.
     * 미리보기는 Placeholder(image_preview)에서 처리한다.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->existingImageUrl = Branch::normalizeStoragePath($data['image_url'] ?? null);
        $data['image_url'] = null;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $newImage = $data['image_url'] ?? null;
        if (is_array($newImage)) {
            $newImage = filled($newImage) ? reset($newImage) : null;
        }

        $data['image_url'] = filled($newImage)
            ? Branch::normalizeStoragePath($newImage)
            : $this->existingImageUrl;

        return $data;
    }
}
