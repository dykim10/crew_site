<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationResource\Pages;
use App\Models\Application;
use App\Models\Branch;
use App\Models\Generation;
use App\Models\User;
use App\Services\ApplicationMatchingService;
use App\Services\ApplicationService;
use App\Services\GenerationEnrollmentService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = '신청 내역';
    protected static ?string $modelLabel = '신청서';
    protected static ?string $pluralModelLabel = '신청 내역';
    protected static ?int $navigationSort = 2;

    public const IMPORT_SOURCE_LABELS = [
        'google_form' => '구글 폼',
    ];

    public static function getNavigationGroup(): ?string
    {
        return '기수 모집';
    }

    public static function canCreate(): bool { return false; }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role === 'super_admin';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([

            Section::make('신청자 정보')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('decrypted_name')
                        ->label('이름')
                        ->disabled()
                        ->dehydrated(false),

                    \Filament\Forms\Components\TextInput::make('decrypted_email')
                        ->label('이메일')
                        ->disabled()
                        ->dehydrated(false),

                    \Filament\Forms\Components\TextInput::make('decrypted_phone')
                        ->label('연락처')
                        ->disabled()
                        ->dehydrated(false),
                ]),

            Section::make('추가 응답')
                ->schema([
                    \Filament\Forms\Components\Textarea::make('field_values_display')
                        ->label('응답 내용')
                        ->disabled()
                        ->dehydrated(false)
                        ->rows(8),
                ]),

            Section::make('처리')
                ->schema([
                    Select::make('status')
                        ->label('상태')
                        ->options(Application::STATUS_LABELS)
                        ->required(),

                    Textarea::make('admin_memo')
                        ->label('관리자 메모')
                        ->rows(3),
                ])
                ->columns(1),
        ]);
    }

    /** @return list<\Filament\Forms\Components\Component> */
    public static function enrollmentTransferFormSchema(): array
    {
        return [
            Select::make('generation_id')
                ->label('대상 기수')
                ->options(fn () => Generation::query()
                    ->orderByDesc('number')
                    ->get()
                    ->mapWithKeys(fn (Generation $g) => [
                        $g->id => $g->alias
                            ? "{$g->number}기 — {$g->alias}"
                            : "{$g->number}기",
                    ])
                    ->toArray())
                ->required()
                ->searchable(),

            Select::make('branch_id')
                ->label('입단 지부')
                ->options(fn () => Branch::query()
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray())
                ->required()
                ->searchable()
                ->helperText('선택한 신청자 전원에 적용할 기본 지부입니다.'),

            Toggle::make('use_individual_branches')
                ->label('개별 지부 지정')
                ->helperText('켜면 아래에 신청 ID별 지부를 입력합니다. 단건 행 액션에서도 사용할 수 있습니다.')
                ->live()
                ->default(false)
                ->inline(false),

            Textarea::make('branch_map')
                ->label('신청 ID → 지부 ID')
                ->placeholder("123=5\n124=7")
                ->helperText('형식: application_id=branch_id (한 줄에 하나). 공통 지부보다 우선 적용됩니다.')
                ->visible(fn (Get $get): bool => (bool) $get('use_individual_branches'))
                ->rows(5),
        ];
    }

    /** @return array<int, int> */
    public static function parseBranchMap(string $text): array
    {
        $map = [];

        foreach (preg_split('/\r\n|\r|\n/', trim($text)) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || ! str_contains($line, '=')) {
                continue;
            }

            [$appId, $branchId] = array_map('trim', explode('=', $line, 2));
            if (is_numeric($appId) && is_numeric($branchId)) {
                $map[(int) $appId] = (int) $branchId;
            }
        }

        return $map;
    }

    public static function notifyEnrollmentResult(array $result, int $selectedCount): void
    {
        $body = "선택 {$selectedCount}건 · 편성 {$result['enrolled']} · 회원동기화 {$result['synced_users']} · skip {$result['skipped']}";
        if ($result['errors'] !== []) {
            $body .= "\n\n" . implode("\n", array_slice($result['errors'], 0, 8));
            if (count($result['errors']) > 8) {
                $body .= "\n… 외 " . (count($result['errors']) - 8) . '건';
            }
        }

        Notification::make()
            ->title('기수·지부 편성 완료')
            ->body($body)
            ->success()
            ->send();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['form', 'matchedUser', 'generation', 'branch']))
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('display_name')
                    ->label('이름')
                    ->getStateUsing(fn (Application $record): string => self::cachedPii($record)['name'])
                    ->searchable(false)
                    ->wrap(),

                TextColumn::make('preferred_branch')
                    ->label('희망지부')
                    ->getStateUsing(fn (Application $record): string => app(ApplicationService::class)->preferredBranch($record))
                    ->limit(40)
                    ->tooltip(fn (Application $record): ?string => ($v = app(ApplicationService::class)->preferredBranch($record)) !== '-' ? $v : null)
                    ->placeholder('-'),

                TextColumn::make('generation.number')
                    ->label('편성 기수')
                    ->formatStateUsing(fn ($state): string => $state ? "{$state}기" : '-')
                    ->placeholder('미편성')
                    ->badge()
                    ->color(fn ($state): string => $state ? 'success' : 'gray'),

                TextColumn::make('branch.name')
                    ->label('편성 지부')
                    ->placeholder('미편성')
                    ->badge()
                    ->color(fn (?string $state): string => $state ? 'warning' : 'gray'),

                TextColumn::make('display_phone')
                    ->label('연락처')
                    ->getStateUsing(function (Application $record): string {
                        $phone = self::cachedPii($record)['phone'];

                        return app(ApplicationService::class)->maskPhone($phone === '-' ? null : $phone);
                    })
                    ->placeholder('-'),

                TextColumn::make('form.title')
                    ->label('폼')
                    ->limit(20)
                    ->placeholder('-'),

                TextColumn::make('form.cohort')
                    ->label('기수')
                    ->placeholder('-'),

                TextColumn::make('matchedUser.nickname')
                    ->label('연결 회원')
                    ->placeholder('미연결')
                    ->searchable(),

                TextColumn::make('import_source')
                    ->label('출처')
                    ->formatStateUsing(fn (?string $state): string => self::IMPORT_SOURCE_LABELS[$state] ?? '직접 신청')
                    ->badge()
                    ->color(fn (?string $state): string => $state === 'google_form' ? 'info' : 'gray'),

                IconColumn::make('matched_user_id')
                    ->label('연결')
                    ->boolean()
                    ->getStateUsing(fn (Application $record): bool => (bool) $record->matched_user_id)
                    ->trueIcon('heroicon-o-link')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved'   => 'success',
                        'rejected'   => 'danger',
                        'waitlisted' => 'warning',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Application::STATUS_LABELS[$state] ?? $state),

                TextColumn::make('created_at')
                    ->label('신청일')
                    ->dateTime('Y.m.d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('상태')
                    ->options(Application::STATUS_LABELS),

                SelectFilter::make('import_source')
                    ->label('출처')
                    ->options(self::IMPORT_SOURCE_LABELS),

                SelectFilter::make('form_id')
                    ->label('폼')
                    ->relationship('form', 'title'),

                SelectFilter::make('matched')
                    ->label('회원 연결')
                    ->options([
                        'yes' => '연결됨',
                        'no'  => '미연결',
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value'] ?? null) {
                            'yes' => $query->whereNotNull('matched_user_id'),
                            'no'  => $query->whereNull('matched_user_id'),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Action::make('view_detail')
                    ->label('상세 보기')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading('신청서 상세')
                    ->modalContent(function (Application $record): \Illuminate\View\View {
                        $crypto = app(\App\Services\CryptoService::class);
                        $pii = [
                            'name'  => $crypto->decrypt($record->name_enc) ?? '-',
                            'email' => $record->email_enc
                                ? ($crypto->decrypt($record->email_enc) ?? '-')
                                : '(없음)',
                            'phone' => $record->phone_enc ? ($crypto->decrypt($record->phone_enc) ?? '-') : '-',
                        ];

                        $fieldLines = [];
                        $formFields = $record->form?->form_fields ?? [];
                        foreach ($formFields as $field) {
                            $key   = $field['key'] ?? null;
                            $label = $field['data']['label'] ?? $key;
                            if (!$key) continue;
                            $value = $record->field_values[$key] ?? '-';
                            if (is_array($value)) $value = implode(', ', $value);
                            $fieldLines[] = ['label' => $label, 'value' => $value ?: '-'];
                        }

                        return view('filament.application-detail', compact('record', 'pii', 'fieldLines'));
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),

                Action::make('link_user')
                    ->label('회원 연결')
                    ->icon('heroicon-o-link')
                    ->color('warning')
                    ->visible(fn (Application $record): bool => ! $record->matched_user_id)
                    ->modalHeading('회원 수동 연결')
                    ->modalDescription('이메일이 없거나 자동 연결되지 않은 신청자를 기존 회원과 연결합니다. 입단 이관 전에 필요합니다.')
                    ->modalSubmitActionLabel('연결')
                    ->form([
                        Select::make('user_id')
                            ->label('회원')
                            ->searchable()
                            ->required()
                            ->getSearchResultsUsing(function (string $search): array {
                                $q = trim($search);
                                if ($q === '') {
                                    return [];
                                }

                                return User::query()
                                    ->where('nickname', 'ilike', "%{$q}%")
                                    ->orderBy('nickname')
                                    ->limit(30)
                                    ->get(['id', 'nickname'])
                                    ->mapWithKeys(fn (User $u) => [
                                        $u->id => "#{$u->id} · {$u->nickname}",
                                    ])
                                    ->toArray();
                            })
                            ->getOptionLabelUsing(function ($value): ?string {
                                $user = User::find($value);

                                return $user ? "#{$user->id} · {$user->nickname}" : null;
                            }),
                    ])
                    ->action(function (Application $record, array $data): void {
                        $user = User::find($data['user_id'] ?? null);
                        if (! $user) {
                            Notification::make()->danger()->title('회원을 찾을 수 없습니다.')->send();

                            return;
                        }
                        app(ApplicationMatchingService::class)->linkManually($record, $user);
                        Notification::make()
                            ->success()
                            ->title('회원 연결 완료')
                            ->body("{$user->nickname} 님과 연결했습니다. 이제 기수 입단 이관이 가능합니다.")
                            ->send();
                    }),

                Action::make('unlink_user')
                    ->label('연결 해제')
                    ->icon('heroicon-o-x-mark')
                    ->color('gray')
                    ->visible(fn (Application $record): bool => (bool) $record->matched_user_id)
                    ->requiresConfirmation()
                    ->modalHeading('회원 연결 해제')
                    ->action(function (Application $record): void {
                        app(ApplicationMatchingService::class)->unlink($record);
                        Notification::make()->success()->title('연결을 해제했습니다.')->send();
                    }),

                Action::make('enroll_generation')
                    ->label('기수 입단 이관')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->modalHeading('기수 입단 이관')
                    ->modalDescription('회원 가입은 선택입니다. 미가입 신청자도 기수·지부로 편성할 수 있습니다. 이미 연결된 회원이 있으면 user_generations에도 반영됩니다.')
                    ->modalSubmitActionLabel('이관')
                    ->form(self::enrollmentTransferFormSchema())
                    ->action(function (Application $record, array $data): void {
                        $branchMap = ($data['use_individual_branches'] ?? false)
                            ? self::parseBranchMap($data['branch_map'] ?? '')
                            : [];

                        $result = app(GenerationEnrollmentService::class)->enrollMany(
                            collect([$record]),
                            (int) $data['generation_id'],
                            (int) $data['branch_id'],
                            $branchMap,
                        );

                        self::notifyEnrollmentResult($result, 1);
                    }),

                EditAction::make()->label('처리'),
                DeleteAction::make()->label('삭제'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('enroll_generation_bulk')
                        ->label('기수 입단 이관')
                        ->icon('heroicon-o-user-plus')
                        ->color('success')
                        ->modalHeading('기수 입단 이관')
                        ->modalDescription('선택한 신청자를 기수·지부로 편성합니다. 회원가입·회원 연결은 필수가 아닙니다.')
                        ->modalSubmitActionLabel('이관')
                        ->form(self::enrollmentTransferFormSchema())
                        ->action(function (Collection $records, array $data): void {
                            $branchMap = ($data['use_individual_branches'] ?? false)
                                ? self::parseBranchMap($data['branch_map'] ?? '')
                                : [];

                            $result = app(GenerationEnrollmentService::class)->enrollMany(
                                $records,
                                (int) $data['generation_id'],
                                (int) $data['branch_id'],
                                $branchMap,
                            );

                            self::notifyEnrollmentResult($result, $records->count());
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /** @return array{name: string, email: string, phone: string} */
    private static function cachedPii(Application $record): array
    {
        static $cache = [];

        if (! isset($cache[$record->id])) {
            $cache[$record->id] = app(ApplicationService::class)->decryptPii($record);
        }

        return $cache[$record->id];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'edit'  => Pages\EditApplication::route('/{record}/edit'),
        ];
    }
}
