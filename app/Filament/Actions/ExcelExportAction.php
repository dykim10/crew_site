<?php

namespace App\Filament\Actions;

use App\Exports\BaseExport;
use App\Exports\GenericExport;
use Filament\Actions\Action;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Filament 헤더 버튼용 엑셀 다운로드 액션 (app/Filament/Actions/ExcelExportAction.php)
 *
 * 두 가지 방식으로 사용한다.
 *
 * ① for() — 전용 Export 클래스가 있을 때 (RunningLogExport 등)
 *   ExcelExportAction::for(RunningLogExport::class, fn() => RunningLog::all(), '러닝기록')
 *
 * ② withColumns() — 별도 클래스 없이 컬럼 맵만 넘길 때 (권장)
 *   ExcelExportAction::withColumns(
 *       columns: ['ID' => fn($r) => $r->id, '닉네임' => fn($r) => $r->nickname],
 *       dataResolver: fn() => User::all(),
 *       filenamePrefix: '회원목록',
 *   )
 *   → 내부적으로 GenericExport 를 사용한다.
 */
class ExcelExportAction extends Action
{
    /**
     * 전용 Export 클래스를 지정하는 방식.
     *
     * @param  string    $exporterClass   BaseExport 서브클래스
     * @param  callable  $dataResolver    데이터를 반환하는 클로저
     * @param  string    $filenamePrefix  다운로드 파일명 접두어
     */
    public static function for(string $exporterClass, callable $dataResolver, string $filenamePrefix = 'export'): static
    {
        return static::make('excel_export')
            ->label('엑셀 다운로드')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->action(function () use ($exporterClass, $dataResolver, $filenamePrefix) {
                $data     = value($dataResolver);
                $exporter = new $exporterClass($data instanceof Collection ? $data : collect($data));
                $filename = $exporterClass::filename($filenamePrefix);

                return Excel::download($exporter, $filename);
            });
    }

    /**
     * 컬럼 맵만 넘기는 간편 방식 — 별도 Export 클래스 불필요.
     *
     * @param  array<string, callable>  $columns         ['헤더명' => fn($row) => 셀값]
     * @param  callable                 $dataResolver    데이터를 반환하는 클로저
     * @param  string                   $filenamePrefix  다운로드 파일명 접두어 (날짜 자동 추가)
     * @param  string                   $sheetTitle      엑셀 시트 탭 이름 (기본값: filenamePrefix)
     */
    public static function withColumns(
        array $columns,
        callable $dataResolver,
        string $filenamePrefix = 'export',
        string $sheetTitle = '',
    ): static {
        return static::make('excel_export')
            ->label('엑셀 다운로드')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->action(function () use ($columns, $dataResolver, $filenamePrefix, $sheetTitle) {
                $data     = value($dataResolver);
                $exporter = new GenericExport(
                    data   : $data,
                    columns: $columns,
                    title  : $sheetTitle ?: $filenamePrefix,
                );

                return Excel::download($exporter, BaseExport::filename($filenamePrefix));
            });
    }
}
