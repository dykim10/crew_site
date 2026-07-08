<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Services\BranchAdminService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        if ($this->record->role !== 'region_admin') {
            return;
        }

        $syncedBranch = app(BranchAdminService::class)->syncFromRegionAdmin($this->record->fresh());
        if (! $syncedBranch) {
            Notification::make()
                ->warning()
                ->title('지부 관리자 미연동')
                ->body('소속 지부가 없어 지부 관리 페이지에 자동 지정되지 않았습니다.')
                ->send();

            return;
        }

        Notification::make()
            ->success()
            ->title('지부 관리자 연동')
            ->body("{$syncedBranch->name} 지부의 관리자로 자동 지정되었습니다.")
            ->send();
    }
}
