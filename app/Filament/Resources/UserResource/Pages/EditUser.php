<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\UsersDetail;
use App\Services\AdminLogService;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    // 저장 전 detail 필드를 임시 보관
    private array $pendingDetail = [];

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

    private string $roleBefore = '';

    // 저장 전 detail_* 필드를 User 모델 데이터에서 분리
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->roleBefore = $this->record->role ?? '';

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
            $labels = [
                'super_admin'  => '슈퍼관리자',
                'region_admin' => '지역관리자',
                'operator'     => '운영자',
                'member'       => '일반멤버',
            ];
            $before = $labels[$this->roleBefore] ?? $this->roleBefore;
            $after  = $labels[$roleAfter] ?? $roleAfter;
            AdminLogService::log(
                'role_changed',
                'User',
                $this->record->id,
                "사용자 {$this->record->name} 권한 변경: {$before} → {$after}"
            );
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
