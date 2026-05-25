<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\UsersDetail;
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

    // 저장 전 detail_* 필드를 User 모델 데이터에서 분리
    protected function mutateFormDataBeforeSave(array $data): array
    {
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

    // User 저장 완료 후 users_detail upsert
    protected function afterSave(): void
    {
        UsersDetail::updateOrCreate(
            ['user_id' => $this->record->id],
            $this->pendingDetail
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
