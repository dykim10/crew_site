<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventFixedSubmissionResource\Pages;
use App\Models\EventFixedSubmission;
use App\Services\AdminLogService;
use App\Services\EventFixedSubmissionService;
use Filament\Actions\Action as TableAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class EventFixedSubmissionResource extends Resource
{
    protected static ?string $model = EventFixedSubmission::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paper-clip';
    protected static ?string $navigationLabel = '제출물 관리';
    protected static ?string $modelLabel = '고정점수 제출물';
    protected static ?string $pluralModelLabel = '고정점수 제출물 목록';
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return '이벤트 관리';
    }

    public static function canCreate(): bool
    {
        return false; // 관리자가 직접 생성 불가, 사용자 제출만 허용
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role === 'super_admin';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([

            Section::make('제출 정보')
                ->schema([
                    Placeholder::make('submitter')
                        ->label('제출자')
                        ->content(fn ($record) => $record?->user?->name ?? '—'),

                    Placeholder::make('event_name')
                        ->label('이벤트명')
                        ->content(fn ($record) => $record?->event?->name ?? '—'),

                    Placeholder::make('status_label')
                        ->label('상태')
                        ->content(fn ($record) => match($record?->status) {
                            'pending'  => '검토 중',
                            'approved' => '승인',
                            'rejected' => '반려',
                            default    => $record?->status ?? '—',
                        }),

                    Placeholder::make('submitted_at')
                        ->label('제출일시')
                        ->content(fn ($record) => $record?->created_at?->format('Y.m.d H:i') ?? '—'),
                ])
                ->columns(2),

            Section::make('제출물 이미지')
                ->schema([
                    Placeholder::make('image_preview')
                        ->label('')
                        ->content(fn ($record) => filled($record?->image_url)
                            ? new HtmlString('<img src="'.e($record->image_url).'" class="max-w-sm rounded-lg shadow" style="max-height:400px;object-fit:contain;">')
                            : new HtmlString('<span class="text-gray-400 text-sm">이미지가 없습니다.</span>')
                        ),
                ])
                ->visible(fn ($record) => filled($record?->image_url))
                ->collapsed(false),

            Section::make('관리자 처리 이력')
                ->schema([
                    Placeholder::make('confirmed_info')
                        ->label('승인/반려 정보')
                        ->content(function ($record) {
                            if (!filled($record?->confirmed_at)) {
                                return '아직 처리되지 않음';
                            }
                            $status = match($record->status) {
                                'approved' => '승인',
                                'rejected' => '반려',
                                default    => $record->status,
                            };
                            $by = $record->confirmedBy?->name ?? '(삭제됨)';
                            return "{$status} · {$by} · {$record->confirmed_at->format('Y.m.d H:i')}";
                        }),

                    Placeholder::make('admin_note_view')
                        ->label('관리자 메모')
                        ->content(fn ($record) => $record?->admin_note ?? '—')
                        ->visible(fn ($record) => filled($record?->admin_note)),
                ]),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(60),

                TextColumn::make('event.name')
                    ->label('이벤트')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('user.name')
                    ->label('제출자')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'  => '검토 중',
                        'approved' => '승인',
                        'rejected' => '반려',
                        default    => $state,
                    }),

                ImageColumn::make('image_url')
                    ->label('제출물')
                    ->square()
                    ->imageHeight(48),

                TextColumn::make('confirmed_at')
                    ->label('처리일')
                    ->date('Y.m.d')
                    ->sortable()
                    ->visible(fn () => auth()->user()->role === 'super_admin'),

                TextColumn::make('created_at')
                    ->label('제출일')
                    ->date('Y.m.d')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        'pending'  => '검토 중',
                        'approved' => '승인',
                        'rejected' => '반려',
                    ]),

                SelectFilter::make('event_id')
                    ->label('이벤트')
                    ->relationship('event', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                TableAction::make('view_image')
                    ->label('이미지 보기')
                    ->icon('heroicon-o-eye')
                    ->url(fn (EventFixedSubmission $record) => $record->image_url, shouldOpenInNewTab: true)
                    ->visible(fn (EventFixedSubmission $record) => filled($record->image_url)),

                TableAction::make('approve')
                    ->label('승인')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('제출물 승인')
                    ->modalDescription(fn (EventFixedSubmission $record) => (function () use ($record) {
                        $score = $record->event->score_rules['fixed_score'] ?? 0;
                        return "{$record->user->name} 사용자의 제출물을 승인하시겠습니까?\n이벤트: {$record->event->name}\n점수: {$score}점";
                    })())
                    ->modalSubmitActionLabel('승인')
                    ->action(function (EventFixedSubmission $record) {
                        $service = new EventFixedSubmissionService();
                        $service->approve($record, auth()->id());
                        AdminLogService::log('approved', 'EventFixedSubmission', $record->id,
                            "고정점수 제출물 #{$record->id} 승인 (사용자: {$record->user?->name}, 이벤트: {$record->event?->name})");
                    })
                    ->successNotification(
                        notification: \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('승인되었습니다')
                            ->body('제출물이 승인되었으며 점수가 지급되었습니다.'),
                    )
                    ->visible(fn (EventFixedSubmission $record) => $record->isPending()),

                TableAction::make('reject')
                    ->label('반려')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Textarea::make('note')
                            ->label('반려 사유')
                            ->required()
                            ->placeholder('반려 사유를 입력하세요. 사용자에게 안내됩니다.')
                            ->rows(4),
                    ])
                    ->action(function (EventFixedSubmission $record, array $data) {
                        $service = new EventFixedSubmissionService();
                        $service->reject($record, auth()->id(), $data['note']);
                        AdminLogService::log('rejected', 'EventFixedSubmission', $record->id,
                            "고정점수 제출물 #{$record->id} 반려 (사용자: {$record->user?->name}, 사유: {$data['note']})");
                    })
                    ->successNotification(
                        notification: \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('반려되었습니다')
                            ->body('제출물이 반려되었습니다.'),
                    )
                    ->visible(fn (EventFixedSubmission $record) => $record->isPending()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->role === 'super_admin'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventFixedSubmissions::route('/'),
            'edit'  => Pages\EditEventFixedSubmission::route('/{record}/edit'),
        ];
    }
}
