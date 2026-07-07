<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduledSmsResource\Pages;
use App\Models\Generation;
use App\Models\ScheduledSms;
use App\Services\ScheduledSmsService;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class ScheduledSmsResource extends Resource
{
    protected static ?string $model = ScheduledSms::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = '예약 문자';
    protected static ?string $modelLabel = '예약 문자';
    protected static ?string $pluralModelLabel = '예약 문자';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return '알림 / 설문';
    }

    public static function canCreate(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'region_admin']);
    }

    public static function canEdit(Model $record): bool
    {
        return $record instanceof ScheduledSms
            && $record->isEditable()
            && in_array(auth()->user()->role, ['super_admin', 'region_admin']);
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['recipients.user.detail', 'creator']);
    }

    public static function form(Schema $schema): Schema
    {
        $service = app(ScheduledSmsService::class);

        return $schema->columns(1)->components([
            Section::make('발송 내용')
                ->description('예약 10분 전 [테스트] 접두어 문자가 관리자에게 먼저 발송됩니다.')
                ->schema([
                    TextInput::make('title')
                        ->label('관리용 제목')
                        ->required()
                        ->maxLength(200)
                        ->placeholder('예: 7기 OT 안내 문자')
                        ->helperText('문자 본문에 포함되지 않습니다.')
                        ->columnSpanFull(),

                    Textarea::make('message_body')
                        ->label('문자 내용')
                        ->required()
                        ->rows(6)
                        ->maxLength(2000)
                        ->placeholder('발송할 문자 내용을 입력하세요.')
                        ->live(debounce: 300)
                        ->helperText(fn (Get $get) => self::messageByteHelper($get('message_body')))
                        ->columnSpanFull(),

                    Select::make('sender_number')
                        ->label('발신번호')
                        ->required()
                        ->options(fn () => self::senderOptions())
                        ->searchable()
                        ->native(false),

                    DateTimePicker::make('scheduled_at')
                        ->label('예약 발송 시각')
                        ->required()
                        ->seconds(false)
                        ->minDate(now()->addMinutes(10))
                        ->helperText('현재 시각 +10분 이후만 선택 가능'),
                ])
                ->columns(2),

            Section::make('수신자 선택')
                ->description('필터로 범위를 좁힌 뒤 아래 목록에서 수신자를 선택하세요.')
                ->schema([
                    Select::make('filter_generation_id')
                        ->label('기수')
                        ->options(
                            Generation::orderByDesc('number')
                                ->get()
                                ->mapWithKeys(fn ($g) => [
                                    $g->id => $g->alias ? "{$g->number}기 — {$g->alias}" : "{$g->number}기",
                                ])
                        )
                        ->searchable()
                        ->live()
                        ->placeholder('전체'),

                    Select::make('filter_region_id')
                        ->label('지역')
                        ->options(fn () => $service->regionOptions()->all())
                        ->searchable()
                        ->live()
                        ->placeholder('전체'),

                    Select::make('filter_training_group')
                        ->label('훈련조')
                        ->options(fn () => $service->trainingGroupOptions()->all())
                        ->searchable()
                        ->live()
                        ->placeholder('전체'),

                    Placeholder::make('recipient_count')
                        ->hiddenLabel()
                        ->content(function (Get $get) {
                            $count = count($get('user_ids') ?? []);

                            return new HtmlString(
                                '<div class="adm-recipient-bar">'
                                . '<span class="adm-recipient-bar__count">' . $count . '</span>'
                                . '<span class="adm-recipient-bar__label">명 선택됨</span>'
                                . '</div>'
                            );
                        })
                        ->columnSpanFull(),

                    CheckboxList::make('user_ids')
                        ->label('수신자 목록')
                        ->options(fn (Get $get) => $service->recipientOptions(
                            generationId: $get('filter_generation_id') ? (int) $get('filter_generation_id') : null,
                            regionId: $get('filter_region_id') ? (int) $get('filter_region_id') : null,
                            trainingGroup: $get('filter_training_group'),
                        ))
                        ->columns(3)
                        ->searchable()
                        ->bulkToggleable()
                        ->required()
                        ->columnSpanFull()
                        ->extraAttributes(['class' => 'adm-recipient-list']),
                ])
                ->columns(3),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('예약 정보')->schema([
                TextEntry::make('title')->label('제목'),
                TextEntry::make('message_body')->label('문자 내용')->columnSpanFull(),
                TextEntry::make('sender_number')->label('발신번호'),
                TextEntry::make('scheduled_at')->label('예약 시각')->dateTime('Y-m-d H:i'),
                TextEntry::make('status')
                    ->label('상태')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ScheduledSms::statusLabel($state))
                    ->color(fn ($state) => ScheduledSms::statusColor($state)),
                TextEntry::make('test_sent_at')->label('테스트 발송')->dateTime('Y-m-d H:i')->placeholder('-'),
                TextEntry::make('sent_at')->label('본 발송 완료')->dateTime('Y-m-d H:i')->placeholder('-'),
                TextEntry::make('creator.nickname')->label('등록자'),
                TextEntry::make('error_message')->label('오류')->color('danger')->visible(fn ($record) => filled($record->error_message)),
            ])->columns(2),

            Section::make('수신자')->schema([
                TextEntry::make('recipients_count')
                    ->label('수신자 수')
                    ->state(fn (ScheduledSms $record) => $record->recipients()->count()),
                RepeatableEntry::make('recipients')
                    ->label('')
                    ->schema([
                        TextEntry::make('user.nickname')->label('닉네임'),
                        TextEntry::make('user.detail.training_group')->label('훈련조')->placeholder('-'),
                        TextEntry::make('status')->label('상태')->badge(),
                    ])
                    ->columns(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('제목')->searchable()->limit(30),
                TextColumn::make('scheduled_at')->label('예약 시각')->dateTime('Y.m.d H:i')->sortable(),
                TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ScheduledSms::statusLabel($state))
                    ->color(fn ($state) => ScheduledSms::statusColor($state)),
                TextColumn::make('recipients_count')
                    ->label('수신자')
                    ->counts('recipients')
                    ->alignCenter(),
                TextColumn::make('test_sent_at')->label('테스트 발송')->dateTime('m.d H:i')->placeholder('-'),
                TextColumn::make('creator.nickname')->label('등록자'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        ScheduledSms::STATUS_PENDING   => ScheduledSms::statusLabel(ScheduledSms::STATUS_PENDING),
                        ScheduledSms::STATUS_TEST_SENT => ScheduledSms::statusLabel(ScheduledSms::STATUS_TEST_SENT),
                        ScheduledSms::STATUS_SENDING   => ScheduledSms::statusLabel(ScheduledSms::STATUS_SENDING),
                        ScheduledSms::STATUS_SENT      => ScheduledSms::statusLabel(ScheduledSms::STATUS_SENT),
                        ScheduledSms::STATUS_CANCELED  => ScheduledSms::statusLabel(ScheduledSms::STATUS_CANCELED),
                        ScheduledSms::STATUS_FAILED    => ScheduledSms::statusLabel(ScheduledSms::STATUS_FAILED),
                    ]),
                Filter::make('scheduled_range')
                    ->form([
                        DateTimePicker::make('from')->label('예약 시작'),
                        DateTimePicker::make('until')->label('예약 종료'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->where('scheduled_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->where('scheduled_at', '<=', $data['until']));
                    }),
            ])
            ->defaultSort('scheduled_at', 'desc')
            ->recordUrl(fn (ScheduledSms $record) => static::getUrl('view', ['record' => $record]));
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListScheduledSms::route('/'),
            'create' => Pages\CreateScheduledSms::route('/create'),
            'edit'   => Pages\EditScheduledSms::route('/{record}/edit'),
            'view'   => Pages\ViewScheduledSms::route('/{record}'),
        ];
    }

    /** @return array<string, string> */
    private static function senderOptions(): array
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->get(config('services.core_api.url') . '/api/sms/senders');
            $senders = $response->json()['senders'] ?? [];
        } catch (\Throwable) {
            $senders = [];
        }

        if ($senders === []) {
            $default = preg_replace('/[^0-9]/', '', config('services.sms.sender', ''));
            if ($default) {
                $senders = [$default];
            }
        }

        return collect($senders)->mapWithKeys(fn ($s) => [$s => $s])->all();
    }

    private static function messageByteHelper(?string $message): string
    {
        $bytes = strlen($message ?? '');
        $suffix = $bytes > 90 ? ' (LMS로 발송됩니다 — 요금이 SMS와 다릅니다)' : '';

        return "현재 {$bytes} byte{$suffix}";
    }
}
