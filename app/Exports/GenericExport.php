<?php

namespace App\Exports;

use Illuminate\Support\Collection;

/**
 * 범용 엑셀 Export (app/Exports/GenericExport.php)
 *
 * 별도의 Export 서브클래스를 만들지 않고,
 * ['헤더명' => fn($row) => 값] 컬럼 맵만 넘기면 엑셀로 내보낼 수 있다.
 *
 * 사용 예:
 *   new GenericExport(
 *       data   : User::all(),
 *       columns: [
 *           'ID'    => fn($r) => $r->id,
 *           '닉네임' => fn($r) => $r->nickname,
 *       ],
 *       title  : '회원 목록'
 *   )
 *
 * ExcelExportAction::withColumns() 내부에서 자동으로 사용된다.
 * 직접 new 해서 Excel::download() 에 넘겨도 된다.
 */
class GenericExport extends BaseExport
{
    /**
     * @param  Collection|iterable  $data     내보낼 데이터
     * @param  array<string, callable>  $columns  ['헤더명' => fn($row) => 셀값]
     * @param  string  $title    엑셀 시트 탭 이름
     */
    public function __construct(
        $data,
        private array $columns,
        private string $title = 'Sheet1',
    ) {
        parent::__construct($data instanceof Collection ? $data : collect($data));
    }

    public function title(): string
    {
        return $this->title;
    }

    public function headings(): array
    {
        return array_keys($this->columns);
    }

    public function map($row): array
    {
        return array_map(fn ($fn) => value($fn, $row), $this->columns);
    }
}
