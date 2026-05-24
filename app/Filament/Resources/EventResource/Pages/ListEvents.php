<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Actions\ExcelExportAction;
use App\Filament\Resources\EventResource;
use App\Models\Event;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExcelExportAction::withColumns(
                columns: [
                    'ID'       => fn ($r) => $r->id,
                    '이벤트명' => fn ($r) => $r->name,
                    '타입'     => fn ($r) => $r->event_type === 'A' ? 'A타입' : 'B타입',
                    '상태'     => fn ($r) => match ($r->status) {
                        'active'   => '진행 중',
                        'upcoming' => '예정',
                        'ended'    => '종료',
                        default    => $r->status,
                    },
                    '시작일'    => fn ($r) => $r->start_date?->format('Y-m-d'),
                    '종료일'    => fn ($r) => $r->end_date?->format('Y-m-d'),
                    '참여대상'  => fn ($r) => match ($r->target_scope) {
                        'all'        => '전체',
                        'generation' => ($r->generation ? $r->generation . '기수' : '특정기수'),
                        'crew'       => '크루',
                        'branch'     => '지부',
                        'group'      => '그룹',
                        default      => $r->target_scope ?? '-',
                    },
                    '신청오픈'  => fn ($r) => $r->is_registration_open ? 'Y' : 'N',
                    '신청자수'  => fn ($r) => $r->registrations()->count(),
                    '등록일'    => fn ($r) => $r->created_at?->format('Y-m-d'),
                ],
                dataResolver  : fn () => Event::orderByDesc('start_date')->get(),
                filenamePrefix: '이벤트목록',
                sheetTitle    : '이벤트 목록',
            ),
            CreateAction::make()->label('이벤트 생성'),
        ];
    }
}
