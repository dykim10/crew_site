<?php

namespace App\Filament\Resources\BugReportResource\Pages;

use App\Filament\Resources\BugReportResource;
use App\Services\AdminLogService;
use App\Services\BugReportService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBugReport extends EditRecord
{
    protected static string $resource = BugReportResource::class;

    private string $statusBefore = '';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => auth()->user()->role === 'super_admin'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->statusBefore = $this->record->status ?? '';
        return $data;
    }

    // 저장 시 BugReportService 경유 — resolved_by / resolved_at 자동 처리
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        app(BugReportService::class)->updateByAdmin($record, $data, auth()->user());
        return $record->fresh();
    }

    protected function afterSave(): void
    {
        $statusAfter = $this->record->status ?? '';

        if ($this->statusBefore !== $statusAfter) {
            $labels  = ['open' => '접수 대기', 'in_progress' => '처리 중', 'resolved' => '처리 완료'];
            $before  = $labels[$this->statusBefore] ?? $this->statusBefore;
            $after   = $labels[$statusAfter] ?? $statusAfter;
            AdminLogService::log(
                'status_changed',
                'BugReport',
                $this->record->id,
                "버그 제보 #{$this->record->id} 상태 변경: {$before} → {$after} (제목: {$this->record->title})"
            );
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
