<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class BaseExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected Collection $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    /**
     * 시트 제목 (기본값, 서브클래스에서 override 가능)
     */
    public function title(): string
    {
        return 'Sheet1';
    }

    /**
     * 헤더 행 정의 — 서브클래스에서 구현
     * 예: ['ID', '닉네임', '이메일', '가입일']
     */
    abstract public function headings(): array;

    /**
     * 행 데이터 매핑 — 서브클래스에서 구현
     * 예: [$row->id, $row->nickname, $row->email, ...]
     */
    abstract public function map($row): array;

    /**
     * 헤더 행 스타일 공통 적용
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1A1212']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    /**
     * 파일명 생성 헬퍼
     * 예: BaseExport::filename('러닝기록') → '러닝기록_20260521.xlsx'
     */
    public static function filename(string $prefix): string
    {
        return $prefix . '_' . now()->format('Ymd') . '.xlsx';
    }
}
