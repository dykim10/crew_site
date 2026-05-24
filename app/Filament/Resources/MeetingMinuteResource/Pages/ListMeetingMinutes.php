<?php

namespace App\Filament\Resources\MeetingMinuteResource\Pages;

use App\Filament\Actions\ExcelExportAction;
use App\Filament\Resources\MeetingMinuteResource;
use App\Models\MeetingMinute;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMeetingMinutes extends ListRecords
{
    protected static string $resource = MeetingMinuteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExcelExportAction::withColumns(
                columns: [
                    'ID'       => fn ($r) => $r->id,
                    '제목'     => fn ($r) => $r->title,
                    '회의일자' => fn ($r) => $r->meeting_date?->format('Y-m-d'),
                    '작성자'   => fn ($r) => $r->author?->nickname ?? '-',
                    '조회수'   => fn ($r) => $r->views,
                    '상단고정' => fn ($r) => $r->is_pinned ? 'Y' : 'N',
                    '등록일'   => fn ($r) => $r->created_at?->format('Y-m-d'),
                ],
                dataResolver  : fn () => MeetingMinute::with('author')->orderByDesc('meeting_date')->get(),
                filenamePrefix: '회의록목록',
                sheetTitle    : '회의록 목록',
            ),
            CreateAction::make()->label('회의록 등록'),
        ];
    }
}
