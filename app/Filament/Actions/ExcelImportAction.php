<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;

class ExcelImportAction extends Action
{
    /**
     * 사용법:
     *   ExcelImportAction::for(RunningLogImport::class)
     *
     * @param string $importerClass BaseImport 서브클래스
     */
    public static function for(string $importerClass): static
    {
        return static::make('excel_import')
            ->label('엑셀 업로드')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('info')
            ->form([
                FileUpload::make('file')
                    ->label('엑셀 파일')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                    ])
                    ->maxSize(10240)
                    ->required()
                    ->helperText('xlsx, xls 형식 · 최대 10MB'),
            ])
            ->modalHeading('엑셀 대량 업로드')
            ->modalSubmitActionLabel('업로드 시작')
            ->action(function (array $data) use ($importerClass) {
                $path     = storage_path('app/public/' . $data['file']);
                $importer = new $importerClass();

                Excel::import($importer, $path);

                $result = $importer->getResult();

                Notification::make()
                    ->title('업로드 완료')
                    ->body("성공 {$result['success']}건 / 실패 {$result['failed']}건" .
                        ($result['errors'] ? "\n" . implode("\n", array_slice($result['errors'], 0, 5)) : ''))
                    ->when($result['failed'] === 0, fn ($n) => $n->success())
                    ->when($result['failed'] > 0, fn ($n) => $n->warning())
                    ->send();
            });
    }
}
