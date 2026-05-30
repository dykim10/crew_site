<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SmsResource\Pages;
use App\Models\SmsLog;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;

class SmsResource extends Resource
{
    protected static ?string $model = SmsLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    protected static ?string $navigationLabel = '단체 문자';
    protected static ?string $modelLabel = '문자 발송';
    protected static ?string $pluralModelLabel = '문자 발송 내역';
    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): ?string
    {
        return '알림 관리';
    }

    public static function canCreate(): bool { return false; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(60),

                TextColumn::make('sender.nickname')
                    ->label('발송자')
                    ->default('-'),

                TextColumn::make('filter_type')
                    ->label('대상')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'generation' => '기수별',
                        'manual'     => '수동입력',
                        default      => '전체',
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'generation' => 'info',
                        'manual'     => 'warning',
                        default      => 'gray',
                    }),

                TextColumn::make('recipient_cnt')
                    ->label('발송 수')
                    ->alignCenter(),

                TextColumn::make('message')
                    ->label('메시지')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->message),

                TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'delivered' => '수신 완료',
                        'partial'   => '일부 수신',
                        'failed'    => '수신 실패',
                        default     => '발송됨',
                    })
                    ->color(fn ($state) => match ($state) {
                        'delivered' => 'success',
                        'partial'   => 'warning',
                        'failed'    => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('delivered_cnt')
                    ->label('수신')
                    ->alignCenter()
                    ->color('success')
                    ->default(0),

                TextColumn::make('failed_cnt')
                    ->label('실패')
                    ->alignCenter()
                    ->color('danger')
                    ->default(0),

                TextColumn::make('created_at')
                    ->label('발송일시')
                    ->dateTime('Y.m.d H:i')
                    ->sortable(),
            ])
            ->actions([
                Action::make('view_detail')
                    ->label('상세')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('info')
                    ->modalHeading(fn ($record) => '발송 상세 내역 — ' . $record->created_at?->format('Y.m.d H:i'))
                    ->modalContent(function ($record) {
                        $messages = [];
                        if ($record->group_id) {
                            try {
                                $resp = Http::timeout(15)->get(
                                    config('services.core_api.url') . '/api/sms/messages/' . $record->group_id
                                );
                                if ($resp->successful()) {
                                    $messages = $resp->json()['messages'] ?? [];
                                }
                            } catch (\Throwable) {}
                        }
                        return view('filament.sms-log-detail', [
                            'log'      => $record,
                            'messages' => $messages,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('닫기'),

                Action::make('refresh_status')
                    ->label('상태 갱신')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->tooltip('CORE API를 통해 Solapi 최신 수신 결과를 가져옵니다.')
                    ->visible(fn ($record) => $record->group_id !== null
                        && in_array($record->status, [SmsLog::STATUS_SENT, SmsLog::STATUS_PARTIAL])
                    )
                    ->action(function ($record) {
                        $response = Http::timeout(10)->get(
                            config('services.core_api.url') . '/api/sms/status/' . $record->group_id
                        );

                        $data = $response->json();

                        // 'error' 키가 문자열일 때만 오류 (숫자 0은 실패 건수이므로 정상)
                        if (!$response->successful() || is_string($data['error'] ?? null)) {
                            Notification::make()
                                ->title('상태 조회 실패')
                                ->body(is_string($data['error'] ?? null) ? $data['error'] : '알 수 없는 오류')
                                ->danger()
                                ->send();
                            return;
                        }

                        $success = $data['success'] ?? 0;
                        $error   = $data['error']   ?? 0;
                        $waiting = $data['waiting']  ?? 0;

                        $status = $waiting > 0
                            ? SmsLog::STATUS_SENT
                            : match (true) {
                                $error === 0    => SmsLog::STATUS_DELIVERED,
                                $success === 0  => SmsLog::STATUS_FAILED,
                                default         => SmsLog::STATUS_PARTIAL,
                            };

                        $record->update([
                            'delivered_cnt' => $success,
                            'failed_cnt'    => $error,
                            'status'        => $status,
                        ]);

                        $waitMsg = $waiting > 0 ? " / 대기 {$waiting}" : '';
                        Notification::make()
                            ->title('상태 갱신 완료')
                            ->body("수신 {$success} / 실패 {$error}{$waitMsg}")
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginationPageOptions([20, 50, 100]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSmsLogs::route('/'),
        ];
    }
}
