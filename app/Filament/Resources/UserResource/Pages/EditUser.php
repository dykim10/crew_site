<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Models\UsersDetail;
use App\Services\AdminLogService;
use App\Services\BranchAdminService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function resolveRecord(int | string $key): User
    {
        return parent::resolveRecord($key)->load('administrator');
    }

    private array $pendingDetail = [];
    private string $roleBefore = '';
    private ?int $branchIdBefore = null;

    protected function getHeaderActions(): array
    {
        return [];
    }

    // 폼 로드 시 users_detail 값을 detail_* 키로 주입
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $detail = UsersDetail::where('user_id', $this->record->id)->first();

        $data['detail_grade']          = $detail?->grade;
        $data['detail_training_group'] = $detail?->training_group;
        $data['detail_join_date']      = $detail?->join_date?->format('Y-m-d');
        $data['detail_memo']           = $detail?->memo;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->roleBefore = $this->record->role ?? '';
        $this->branchIdBefore = $this->record->branch_id ? (int) $this->record->branch_id : null;

        $this->pendingDetail = [
            'grade'          => $data['detail_grade'] ?? null,
            'training_group' => $data['detail_training_group'] ?? null,
            'join_date'      => $data['detail_join_date'] ?? null,
            'memo'           => $data['detail_memo'] ?? null,
        ];

        unset(
            $data['detail_grade'],
            $data['detail_training_group'],
            $data['detail_join_date'],
            $data['detail_memo'],
        );

        return $data;
    }

    // User 저장 완료 후 users_detail upsert + 권한 변경 로깅
    protected function afterSave(): void
    {
        UsersDetail::updateOrCreate(
            ['user_id' => $this->record->id],
            $this->pendingDetail
        );

        $roleAfter = $this->record->fresh()->role ?? '';
        if ($this->roleBefore !== '' && $this->roleBefore !== $roleAfter) {
            $labels = BranchAdminService::roleLabels();
            $before = $labels[$this->roleBefore] ?? $this->roleBefore;
            $after  = $labels[$roleAfter] ?? $roleAfter;
            AdminLogService::log(
                'role_changed',
                'User',
                $this->record->id,
                "사용자 {$this->record->name} 권한 변경: {$before} → {$after}"
            );
        }

        $branchService = app(BranchAdminService::class);
        $branchAfter = $this->record->fresh()->branch_id ? (int) $this->record->fresh()->branch_id : null;

        if ($this->roleBefore !== $roleAfter) {
            $syncedBranch = $branchService->onRoleChanged($this->record, $this->roleBefore, $roleAfter);
            if ($roleAfter === 'region_admin' && ! $syncedBranch) {
                Notification::make()
                    ->warning()
                    ->title('지부 관리자 미연동')
                    ->body('소속 지부가 없어 지부 관리 페이지에 자동 지정되지 않았습니다. 소속 지부를 설정해 주세요.')
                    ->send();
            } elseif ($syncedBranch) {
                Notification::make()
                    ->success()
                    ->title('지부 관리자 연동')
                    ->body("{$syncedBranch->name} 지부의 관리자로 자동 지정되었습니다.")
                    ->send();
            }
        } elseif ($roleAfter === 'region_admin' && $branchAfter !== $this->branchIdBefore) {
            $syncedBranch = $branchService->syncFromRegionAdmin($this->record->fresh());
            if ($syncedBranch) {
                Notification::make()
                    ->success()
                    ->title('지부 관리자 연동')
                    ->body("{$syncedBranch->name} 지부의 관리자로 자동 지정되었습니다.")
                    ->send();
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
