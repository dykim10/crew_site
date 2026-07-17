<?php

namespace App\Filament\Resources;

use App\Exports\BaseExport;
use App\Exports\GenericExport;
use App\Filament\Resources\GoogleFormResource\Pages;
use App\Models\Event;
use App\Models\Generation;
use App\Models\GoogleForm;
use App\Services\GoogleFormApplicationImportService;
use App\Services\GoogleFormService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
use Maatwebsite\Excel\Facades\Excel;

class GoogleFormResource extends Resource
{
    protected static ?string $model = GoogleForm::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = '구글 폼 연동';
    protected static ?string $modelLabel = '구글 폼';
    protected static ?string $pluralModelLabel = '구글 폼 목록';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return '알림 / 설문';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('폼 정보')
                ->schema([
                    TextInput::make('title')
                        ->label('폼 제목')
                        ->required()
                        ->maxLength(255),

                    Select::make('purpose')
                        ->label('용도')
                        ->options(GoogleForm::PURPOSE_LABELS)
                        ->default(GoogleForm::PURPOSE_GENERAL)
                        ->required()
                        ->live(),

                    Select::make('generation_id')
                        ->label('연결 기수')
                        ->options(fn () => Generation::query()
                            ->orderByDesc('number')
                            ->get()
                            ->mapWithKeys(fn (Generation $g) => [
                                $g->id => $g->alias
                                    ? "{$g->number}기 — {$g->alias}"
                                    : "{$g->number}기",
                            ])
                            ->toArray())
                        ->searchable()
                        ->required(fn (Get $get): bool => $get('purpose') === GoogleForm::PURPOSE_GENERATION_RECRUIT)
                        ->visible(fn (Get $get): bool => $get('purpose') === GoogleForm::PURPOSE_GENERATION_RECRUIT),

                    Select::make('event_id')
                        ->label('연결 이벤트')
                        ->options(fn () => Event::query()
                            ->orderByDesc('start_date')
                            ->pluck('name', 'id')
                            ->toArray())
                        ->searchable()
                        ->visible(fn (Get $get): bool => $get('purpose') === GoogleForm::PURPOSE_EVENT),

                    TextInput::make('form_url')
                        ->label('구글 폼 URL')
                        ->url()
                        ->maxLength(2000)
                        ->placeholder('https://docs.google.com/forms/d/...')
                        ->helperText('공개 신청 안내 페이지에서 외부 구글 폼으로 이동할 때 사용합니다.'),

                    TextInput::make('sheet_id')
                        ->label('Google Sheet ID 또는 URL')
                        ->placeholder('1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgVE2upms  또는  전체 URL 붙여넣기')
                        ->helperText(function (): string {
                            $email = app(GoogleFormService::class)->getServiceAccountEmail();
                            $base = '구글 폼 → 응답 → 스프레드시트에서 보기 → URL의 /d/ 뒤 ID';
                            if (! $email) {
                                return $base . ' · 서비스 계정 키가 없으면 storage/app/google/service-account.json 을 배치하세요.';
                            }

                            return $base . ' · 시트 [공유]에 아래 이메일을 뷰어로 추가: ' . $email;
                        })
                        ->required()
                        ->maxLength(500)
                        ->dehydrateStateUsing(
                            fn (string $state): string => GoogleFormService::extractSheetId($state)
                        ),

                    Placeholder::make('google_share_guide')
                        ->label('시트 공유 안내')
                        ->content(function (): \Illuminate\Support\HtmlString {
                            $email = app(GoogleFormService::class)->getServiceAccountEmail();
                            if (! $email) {
                                return new \Illuminate\Support\HtmlString(
                                    '<span class="text-sm text-gray-500">서비스 계정 키가 없습니다. storage/app/google/service-account.json 배치 후 새로고침하세요.</span>'
                                );
                            }

                            return new \Illuminate\Support\HtmlString(
                                '<span class="text-sm text-gray-600">연결된 Google 시트 → <strong>공유</strong> → '
                                . '<code class="text-xs bg-gray-100 px-1 py-0.5 rounded">' . e($email) . '</code>'
                                . ' 를 <strong>뷰어</strong>로 추가해야 결과 보기/엑셀 다운로드가 됩니다.</span>'
                            );
                        })
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label('설명')
                        ->rows(2),

                    Toggle::make('is_active')
                        ->label('활성화')
                        ->default(true)
                        ->inline(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['generation', 'event']))
            ->columns([
                TextColumn::make('title')
                    ->label('폼 제목')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('purpose')
                    ->label('용도')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => GoogleForm::PURPOSE_LABELS[$state] ?? ($state ?? '-'))
                    ->color(fn (?string $state): string => match ($state) {
                        GoogleForm::PURPOSE_GENERATION_RECRUIT => 'success',
                        GoogleForm::PURPOSE_EVENT => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('linked_target')
                    ->label('연결 대상')
                    ->state(function (GoogleForm $record): string {
                        return match ($record->purpose) {
                            GoogleForm::PURPOSE_GENERATION_RECRUIT => $record->generation
                                ? ($record->generation->alias
                                    ? "{$record->generation->number}기 — {$record->generation->alias}"
                                    : "{$record->generation->number}기")
                                : '—',
                            GoogleForm::PURPOSE_EVENT => $record->event?->name ?? '—',
                            default => '—',
                        };
                    }),

                TextColumn::make('sheet_id')
                    ->label('Sheet ID')
                    ->limit(30)
                    ->tooltip(fn ($state) => $state),

                TextColumn::make('description')
                    ->label('설명')
                    ->limit(40)
                    ->placeholder('—'),

                IconColumn::make('is_active')
                    ->label('활성화')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label('등록일')
                    ->date('Y.m.d')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('purpose')
                    ->label('용도')
                    ->options(GoogleForm::PURPOSE_LABELS),
            ])
            ->actions([
                Action::make('import_applications')
                    ->label('신청 내역으로 가져오기')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->visible(fn (GoogleForm $record): bool => $record->purpose === GoogleForm::PURPOSE_GENERATION_RECRUIT)
                    ->modalHeading('신청 내역 가져오기')
                    ->modalDescription('시트 열을 이름·이메일·연락처에 매핑한 뒤 신청 내역으로 가져옵니다.')
                    ->modalSubmitActionLabel('가져오기')
                    ->form(function (GoogleForm $record): array {
                        try {
                            $data = app(GoogleFormService::class)->getResponses($record->sheet_id);
                            $headers = $data['headers'];
                        } catch (\RuntimeException $e) {
                            return [
                                Placeholder::make('sheet_error')
                                    ->label('')
                                    ->content(new \Illuminate\Support\HtmlString(
                                        '<p class="text-sm text-danger-600">' . e($e->getMessage()) . '</p>'
                                    )),
                            ];
                        }

                        if ($headers === []) {
                            return [
                                Placeholder::make('empty_sheet')
                                    ->label('')
                                    ->content('시트에 응답 데이터가 없습니다.'),
                            ];
                        }

                        $headerOptions = array_combine($headers, $headers) ?: [];
                        $mapping = $record->column_mapping ?? [];

                        $defaultEmail = $mapping['email'] ?? null;
                        if (! filled($defaultEmail)) {
                            $defaultEmail = self::guessHeader($headers, [
                                '이메일', 'email', 'e-mail', '메일', '메일주소', 'email address',
                            ]);
                        }

                        $defaultName = $mapping['name'] ?? null;
                        if (! filled($defaultName)) {
                            $defaultName = self::guessHeader($headers, ['이름', '성명', 'name', '실명']);
                        }

                        $defaultPhone = $mapping['phone'] ?? null;
                        if (! filled($defaultPhone)) {
                            $defaultPhone = self::guessHeader($headers, [
                                '연락처', '전화번호', '휴대폰', '휴대전화', 'phone', 'mobile', 'tel',
                            ]);
                        }

                        return [
                            Select::make('map_name')
                                ->label('이름 열')
                                ->options($headerOptions)
                                ->default($defaultName)
                                ->required()
                                ->searchable(),

                            Select::make('map_email')
                                ->label('이메일 열')
                                ->options($headerOptions)
                                ->default($defaultEmail)
                                ->searchable()
                                ->nullable()
                                ->placeholder('없음 (비움)')
                                ->helperText('설문에 이메일이 있으면 해당 열을 선택하세요 → 저장·자동 회원 연결. 없으면 비워 두고 나중에 수동 연결합니다.'),

                            Select::make('map_phone')
                                ->label('연락처 열')
                                ->options($headerOptions)
                                ->default($defaultPhone)
                                ->searchable()
                                ->nullable()
                                ->placeholder('선택 안 함'),
                        ];
                    })
                    ->action(function (GoogleForm $record, array $data): void {
                        if (! isset($data['map_name'])) {
                            Notification::make()
                                ->danger()
                                ->title('열 매핑 필요')
                                ->body('이름 열을 선택해주세요.')
                                ->send();

                            return;
                        }

                        $columnMapping = [
                            'name'  => $data['map_name'],
                            'email' => filled($data['map_email'] ?? null) ? $data['map_email'] : null,
                            'phone' => filled($data['map_phone'] ?? null) ? $data['map_phone'] : null,
                        ];

                        $record->update(['column_mapping' => $columnMapping]);

                        try {
                            if (function_exists('set_time_limit')) {
                                @set_time_limit(180);
                            }
                            $result = app(GoogleFormApplicationImportService::class)
                                ->import($record, $columnMapping);
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->danger()
                                ->title('가져오기 실패')
                                ->body($e->getMessage())
                                ->persistent()
                                ->send();

                            return;
                        }

                        $body = "신규 {$result['created']}건 · 중복 skip {$result['skipped']}건 · 실패 {$result['failed']}건";
                        if (filled($columnMapping['email'])) {
                            $body .= "\n\n이메일 매핑됨 → 동일 email_hash 회원은 자동 연결됩니다. 미가입자는 신청 내역에서 수동 연결하세요.";
                        } else {
                            $body .= "\n\n이메일 없음 → 자동 연결 없음. 신청 내역에서 회원 수동 연결 후 입단 이관하세요.";
                        }
                        if ($result['form_missing']) {
                            $body .= "\n\n해당 기수 cohort 신청서 폼이 없어 form_id 없이 저장했습니다.";
                        }
                        if ($result['errors'] !== []) {
                            $body .= "\n\n" . implode("\n", array_slice($result['errors'], 0, 5));
                            if (count($result['errors']) > 5) {
                                $body .= "\n… 외 " . (count($result['errors']) - 5) . '건';
                            }
                        }
                        $body .= "\n\n시트 원본은 Google Forms에서 정리·보관하세요.";

                        Notification::make()
                            ->title('가져오기 완료')
                            ->body($body)
                            ->success()
                            ->persistent()
                            ->send();
                    }),

                Action::make('view_responses')
                    ->label('결과 보기')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn (GoogleForm $record): string => $record->title . ' — 응답 결과')
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('닫기')
                    ->modalContent(function (GoogleForm $record): \Illuminate\Contracts\View\View {
                        try {
                            $data = app(GoogleFormService::class)->getResponses($record->sheet_id);
                        } catch (\RuntimeException $e) {
                            $data = ['headers' => [], 'rows' => [], 'error' => $e->getMessage()];
                        }

                        return view('filament.modals.google-form-responses', [
                            'headers' => $data['headers'],
                            'rows'    => $data['rows'],
                            'error'   => $data['error'] ?? null,
                            'warning' => $data['warning'] ?? null,
                            'count'   => count($data['rows']),
                        ]);
                    }),

                Action::make('download_excel')
                    ->label('엑셀')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (GoogleForm $record) {
                        try {
                            $data = app(GoogleFormService::class)->getResponses($record->sheet_id);
                        } catch (\RuntimeException $e) {
                            Notification::make()
                                ->danger()
                                ->title('다운로드 실패')
                                ->body($e->getMessage())
                                ->send();
                            return;
                        }

                        if (empty($data['headers'])) {
                            Notification::make()->warning()->title('응답 데이터가 없습니다.')->send();
                            return;
                        }

                        if (! empty($data['warning'])) {
                            Notification::make()
                                ->warning()
                                ->title('공개 CSV로 다운로드')
                                ->body($data['warning'])
                                ->send();
                        }

                        $columns = [];
                        foreach ($data['headers'] as $i => $header) {
                            $idx = $i;
                            $columns[$header] = fn ($row) => $row[$idx] ?? '';
                        }

                        $exporter = new GenericExport(
                            collect($data['rows']),
                            $columns,
                            $record->title,
                        );

                        return Excel::download(
                            $exporter,
                            BaseExport::filename($record->title)
                        );
                    }),

                EditAction::make()
                    ->label('수정')
                    ->mutateFormDataUsing(fn (array $data): array => Pages\ListGoogleForms::normalizePurposeFields($data)),
                DeleteAction::make()->label('삭제'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGoogleForms::route('/'),
        ];
    }

    /**
     * 시트 헤더에서 후보 라벨과 일치하는 열을 고른다 (대소문자·공백 무시).
     *
     * @param  list<string>  $headers
     * @param  list<string>  $candidates
     */
    public static function guessHeader(array $headers, array $candidates): ?string
    {
        $normalizedCandidates = array_map(
            fn (string $c) => mb_strtolower(preg_replace('/\s+/u', '', $c) ?? $c),
            $candidates
        );

        foreach ($headers as $header) {
            $normalized = mb_strtolower(preg_replace('/\s+/u', '', (string) $header) ?? (string) $header);
            if (in_array($normalized, $normalizedCandidates, true)) {
                return $header;
            }
        }

        return null;
    }
}
