<?php

namespace App\Filament\Resources\RunningLogResource\Pages;

use App\Exports\RunningLogExport;
use App\Filament\Actions\ExcelExportAction;
use App\Filament\Resources\RunningLogResource;
use App\Models\RunningLog;
use Filament\Resources\Pages\ListRecords;

class ListRunningLogs extends ListRecords
{
    protected static string $resource = RunningLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 확정된 기록만 다운로드 (is_confirmed=true), 날짜 내림차순
            ExcelExportAction::for(
                exporterClass : RunningLogExport::class,
                dataResolver  : fn () => RunningLog::with('user')
                    ->where('is_confirmed', true)
                    ->orderByDesc('run_date')
                    ->get(),
                filenamePrefix: '러닝기록',
            ),
        ];
    }

}
