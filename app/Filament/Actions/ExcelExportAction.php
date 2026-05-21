<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExportAction extends Action
{
    protected string $exporterClass;
    protected string $filename = 'export.xlsx';

    /**
     * 사용법:
     *   ExcelExportAction::for(RunningLogExport::class, fn() => RunningLog::all(), '러닝기록')
     *
     * @param string   $exporterClass  BaseExport 서브클래스
     * @param callable $dataResolver   데이터를 반환하는 클로저 (Collection)
     * @param string   $filenamePrefix 다운로드 파일명 접두어
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
}
