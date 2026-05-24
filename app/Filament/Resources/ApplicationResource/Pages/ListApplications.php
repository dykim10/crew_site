<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Actions\ExcelExportAction;
use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use App\Services\CryptoService;
use Filament\Resources\Pages\ListRecords;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 이름·이메일·연락처는 AES 암호화 저장 → CryptoService 복호화 (CORE API 호출)
            // 전체 건수만큼 HTTP 요청이 발생하므로 대량 데이터 시 처리 시간이 걸릴 수 있음
            ExcelExportAction::withColumns(
                columns: [
                    'ID'       => fn ($r) => $r->id,
                    '폼'       => fn ($r) => $r->form?->title ?? '-',
                    '기수'     => fn ($r) => $r->form?->cohort ?? '-',
                    '이름'     => fn ($r) => app(CryptoService::class)->decrypt($r->name_enc) ?? '-',
                    '이메일'   => fn ($r) => app(CryptoService::class)->decrypt($r->email_enc) ?? '-',
                    '연락처'   => fn ($r) => $r->phone_enc
                        ? (app(CryptoService::class)->decrypt($r->phone_enc) ?? '-')
                        : '-',
                    '상태'     => fn ($r) => Application::STATUS_LABELS[$r->status] ?? $r->status,
                    '관리자메모' => fn ($r) => $r->admin_memo ?? '',
                    '신청일'   => fn ($r) => $r->created_at?->format('Y-m-d H:i'),
                ],
                dataResolver  : fn () => Application::with('form')->orderByDesc('created_at')->get(),
                filenamePrefix: '신청내역',
                sheetTitle    : '신청 내역',
            ),
        ];
    }
}
