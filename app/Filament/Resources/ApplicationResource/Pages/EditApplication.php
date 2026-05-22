<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use App\Services\CryptoService;
use Filament\Resources\Pages\EditRecord;

class EditApplication extends EditRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $crypto = app(CryptoService::class);

        $data['decrypted_name']  = $crypto->decrypt($this->record->name_enc) ?? '-';
        $data['decrypted_email'] = $crypto->decrypt($this->record->email_enc) ?? '-';
        $data['decrypted_phone'] = $this->record->phone_enc
            ? ($crypto->decrypt($this->record->phone_enc) ?? '-')
            : '-';

        $lines = [];
        $formFields = $this->record->form?->form_fields ?? [];
        foreach ($formFields as $field) {
            $key   = $field['key'] ?? null;
            $label = $field['data']['label'] ?? $key;
            if (!$key) continue;
            $value = $this->record->field_values[$key] ?? '-';
            if (is_array($value)) $value = implode(', ', $value);
            $lines[] = "[{$label}]\n" . ($value ?: '-');
        }
        $data['field_values_display'] = implode("\n\n", $lines);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
