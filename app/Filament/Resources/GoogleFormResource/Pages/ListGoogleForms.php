<?php

namespace App\Filament\Resources\GoogleFormResource\Pages;

use App\Filament\Resources\GoogleFormResource;
use App\Models\GoogleForm;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGoogleForms extends ListRecords
{
    protected static string $resource = GoogleFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('폼 추가'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return self::normalizePurposeFields($data);
    }

    /** @param  array<string, mixed>  $data */
    public static function normalizePurposeFields(array $data): array
    {
        $purpose = $data['purpose'] ?? GoogleForm::PURPOSE_GENERAL;

        if ($purpose !== GoogleForm::PURPOSE_GENERATION_RECRUIT) {
            $data['generation_id'] = null;
        }

        if ($purpose !== GoogleForm::PURPOSE_EVENT) {
            $data['event_id'] = null;
        }

        return $data;
    }
}
