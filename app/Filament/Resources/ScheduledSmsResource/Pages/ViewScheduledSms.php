<?php

namespace App\Filament\Resources\ScheduledSmsResource\Pages;

use App\Filament\Resources\ScheduledSmsResource;
use App\Models\ScheduledSms;
use App\Services\ScheduledSmsService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Http;

class ViewScheduledSms extends ViewRecord
{
    protected static string $resource = ScheduledSmsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('수정')
                ->visible(fn (ScheduledSms $record) => $record->isEditable()),

            Actions\Action::make('refresh_delivery')
                ->label('수신결과 조회')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->visible(fn (ScheduledSms $record) => $record->status === ScheduledSms::STATUS_SENT && filled($record->solapi_group_id))
                ->action(function (ScheduledSms $record) {
                    $response = Http::timeout(10)->get(
                        config('services.core_api.url') . '/api/sms/status/' . $record->solapi_group_id
                    );
                    $data = $response->json();

                    if (!$response->successful() || is_string($data['error'] ?? null)) {
                        Notification::make()
                            ->title('상태 조회 실패')
                            ->body(is_string($data['error'] ?? null) ? $data['error'] : '알 수 없는 오류')
                            ->danger()
                            ->send();
                        return;
                    }

                    Notification::make()
                        ->title('수신 결과')
                        ->body(sprintf(
                            '수신 %d / 실패 %d / 대기 %d',
                            $data['success'] ?? 0,
                            $data['error'] ?? 0,
                            $data['waiting'] ?? 0,
                        ))
                        ->success()
                        ->send();
                }),

            Actions\Action::make('cancel')
                ->label('예약 취소')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('예약 취소')
                ->modalDescription('테스트 문자 수신 후 본 발송 전까지 취소할 수 있습니다.')
                ->visible(fn (ScheduledSms $record) => $record->isCancelable())
                ->action(function (ScheduledSms $record) {
                    try {
                        app(ScheduledSmsService::class)->cancel($record, auth()->id());
                        Notification::make()->title('예약이 취소되었습니다.')->success()->send();
                        $this->record = $record->fresh(['recipients.user.detail', 'creator']);
                    } catch (\RuntimeException $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),
        ];
    }
}
