<?php

namespace App\Filament\Resources\SmsResource\Pages;

use App\Filament\Resources\SmsResource;
use App\Models\Generation;
use App\Services\SmsService;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListSmsLogs extends ListRecords
{
    protected static string $resource = SmsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send_sms')
                ->label('새 문자 발송')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('단체 문자 발송')
                ->modalDescription('수신자 전화번호는 해당 기수 신청서(승인된 신청자)에서 가져옵니다.')
                ->modalSubmitActionLabel('발송')
                ->form([
                    Select::make('target_type')
                        ->label('발송 대상')
                        ->options([
                            'all'        => '전체 (승인된 전체 신청자)',
                            'generation' => '기수별',
                            'manual'     => '수동 입력',
                        ])
                        ->default('all')
                        ->required()
                        ->live(),

                    Select::make('generation_id')
                        ->label('기수 선택')
                        ->options(
                            Generation::orderByDesc('number')
                                ->get()
                                ->mapWithKeys(fn ($g) => [
                                    $g->id => $g->alias
                                        ? "{$g->number}기 — {$g->alias}"
                                        : "{$g->number}기",
                                ])
                                ->toArray()
                        )
                        ->searchable()
                        ->visible(fn ($get) => $get('target_type') === 'generation')
                        ->requiredIf('target_type', 'generation'),

                    Textarea::make('manual_phones')
                        ->label('전화번호 직접 입력')
                        ->rows(3)
                        ->placeholder('010-1234-5678, 01012345678, 010-9999-0000')
                        ->helperText('쉼표(,)로 구분하여 여러 번호를 입력하세요. 하이픈 자동 제거됩니다.')
                        ->visible(fn ($get) => $get('target_type') === 'manual')
                        ->requiredIf('target_type', 'manual'),

                    Textarea::make('message')
                        ->label('메시지 내용')
                        ->required()
                        ->rows(5)
                        ->maxLength(2000)
                        ->helperText('SMS 90자 이하, LMS 최대 2,000자까지 자동 전환됩니다.')
                        ->placeholder('발송할 메시지를 입력하세요.'),
                ])
                ->action(function (array $data) {
                    $service      = app(SmsService::class);
                    $targetType   = $data['target_type'];
                    $generationId = $targetType === 'generation' ? (int) $data['generation_id'] : null;
                    $message      = $data['message'];

                    try {
                        $phones = match ($targetType) {
                            'generation' => $service->getPhonesByGeneration($generationId),
                            'manual'     => $service->parseManualPhones($data['manual_phones'] ?? ''),
                            default      => $service->getAllApprovedPhones(),
                        };

                        if (empty($phones)) {
                            Notification::make()
                                ->title('발송 대상 없음')
                                ->body('해당 조건에 승인된 전화번호가 없습니다.')
                                ->warning()
                                ->send();
                            return;
                        }

                        $log = $service->send(
                            phones:     $phones,
                            message:    $message,
                            senderId:   auth()->id(),
                            targetType: $targetType,
                            targetId:   $generationId,
                        );

                        $success = $log->result_data['success_count'] ?? 0;
                        $fail    = $log->result_data['fail_count'] ?? 0;

                        Notification::make()
                            ->title('문자 발송 완료')
                            ->body("{$log->recipient_cnt}명 대상 발송 — 성공 {$success}건 / 실패 {$fail}건")
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('발송 실패')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
