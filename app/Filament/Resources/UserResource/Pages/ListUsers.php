<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Actions\ExcelExportAction;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExcelExportAction::withColumns(
                columns: [
                    'ID'        => fn ($r) => $r->id,
                    '닉네임'    => fn ($r) => $r->nickname,
                    '이메일'    => fn ($r) => $r->email ?? '-',
                    '이름'      => fn ($r) => $r->name ?? '-',
                    '권한'      => fn ($r) => match ($r->role) {
                        'super_admin'  => '슈퍼관리자',
                        'region_admin' => '지부 관리자',
                        'operator'     => '운영자',
                        default        => '일반멤버',
                    },
                    '베타'      => fn ($r) => $r->is_beta ? 'Y' : 'N',
                    '초대코드'  => fn ($r) => $r->invite_code ?? '-',
                    '최근로그인' => fn ($r) => $r->last_login_at?->format('Y-m-d H:i') ?? '-',
                    '가입일'    => fn ($r) => $r->created_at?->format('Y-m-d'),
                ],
                dataResolver  : fn () => User::orderByDesc('created_at')->get(),
                filenamePrefix: '회원목록',
                sheetTitle    : '회원 목록',
            ),
            CreateAction::make()->label('구성원 생성'),
        ];
    }
}
