<?php

namespace App\Exports;

class RunningLogExport extends BaseExport
{
    public function title(): string
    {
        return '러닝 기록';
    }

    public function headings(): array
    {
        return [
            'ID', '닉네임', '날짜', '거리(km)', '시간', '평균페이스',
            '칼로리', '평균심박수', '고도(m)', '실내여부', '검수여부', '등록일',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->user->nickname ?? '-',
            $row->run_date?->format('Y-m-d'),
            $row->distance_km,
            $row->duration_formatted,
            $row->avg_pace_formatted,
            $row->calories,
            $row->avg_heart_rate,
            $row->elevation_m,
            $row->is_indoor ? '실내' : '실외',
            $row->is_confirmed ? '확인' : '미확인',
            $row->created_at?->format('Y-m-d H:i'),
        ];
    }
}
