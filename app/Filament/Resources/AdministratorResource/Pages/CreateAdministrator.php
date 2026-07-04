<?php

namespace App\Filament\Resources\AdministratorResource\Pages;

use App\Filament\Resources\AdministratorResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateAdministrator extends CreateRecord
{
    protected static string $resource = AdministratorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['user_id'])) {
            $user = User::find($data['user_id']);
            if ($user) {
                $data['name'] = $user->name ?? $user->nickname;
                if (empty($data['branch_id']) && $user->branch_id) {
                    $data['branch_id'] = $user->branch_id;
                }
            }
        }

        if (!empty($data['branch_id'])) {
            $data['branch_custom'] = null;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
