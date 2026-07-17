<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GenerationResource\Pages;
use App\Models\Branch;
use App\Models\Generation;
use App\Models\ApplicationForm;
use App\Services\GenerationRecruitmentService;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use App\Models\GoogleForm;
use App\Services\BugReportService;
use App\Services\GenerationVisibilityService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerationResource extends Resource
{
    protected static ?string $model = Generation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = '기수 관리';
    protected static ?string $modelLabel = '기수';
    protected static ?string $pluralModelLabel = '기수 목록';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return '크루 관리';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->role === 'super_admin';
    }

    public static function canEdit(Model $record): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'region_admin']);
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role === 'super_admin';
    }

    /**
     * edition 목록 → 셀렉트 옵션.
     * @param iterable<int, object|array<string, mixed>> $rows
     * @return array{options: array<int, string>, meta: array<int, array{race_id:?int, name:string}>}
     */
    private static function buildEditionOptions(iterable $rows): array
    {
        $options = [];
        $meta = [];
        foreach ($rows as $edition) {
            $row = is_array($edition) ? $edition : (array) $edition;
            $editionId = $row['id'] ?? null;
            $name = trim((string) ($row['race_name'] ?? $row['name'] ?? ''));
            if (! $editionId || $name === '') {
                continue;
            }
            $editionId = (int) $editionId;
            $date = isset($row['race_date']) ? substr((string) $row['race_date'], 0, 10) : '';
            $year = $row['year'] ?? null;
            $prefix = $date ?: ($year ? (string) $year : '');
            $options[$editionId] = $prefix !== '' ? "[{$prefix}] {$name}" : $name;
            $meta[$editionId] = [
                'race_id' => isset($row['race_id']) && $row['race_id'] !== '' ? (int) $row['race_id'] : null,
                'name'    => $name,
            ];
        }

        return compact('options', 'meta');
    }

    /**
     * race_editions 기준 (오늘 이후·일정 미정 upcoming).
     *
     * @return array{options: array<int|string, string>, meta: array<int, array{race_id:?int, name:string}>, failed: bool, error: ?string, source: ?string}
     */
    private static function loadEditionOptions(): array
    {
        $cached = Cache::get('core_api_editions_upcoming');
        if (is_array($cached) && isset($cached['options'], $cached['meta'])) {
            return [
                'options' => $cached['options'],
                'meta'    => $cached['meta'],
                'failed'  => false,
                'error'   => null,
                'source'  => 'cache',
            ];
        }

        $url = rtrim(config('services.core_api.url'), '/').'/api/races/editions/upcoming?limit=200';

        try {
            $response = Http::timeout(8)->get($url);

            if ($response->successful()) {
                $built = static::buildEditionOptions($response->json() ?? []);
                Cache::put('core_api_editions_upcoming', [
                    'options' => $built['options'],
                    'meta'    => $built['meta'],
                ], 600);

                return [
                    'options' => $built['options'],
                    'meta'    => $built['meta'],
                    'failed'  => false,
                    'error'   => null,
                    'source'  => 'api',
                ];
            }

            Log::warning("CORE API edition 목록 HTTP {$response->status()}: {$url}");

            return [
                'options' => [],
                'meta'    => [],
                'failed'  => true,
                'error'   => "HTTP {$response->status()}",
                'source'  => null,
            ];
        } catch (\Exception $e) {
            Log::warning('CORE API edition 목록 조회 실패: '.$e->getMessage());

            return [
                'options' => [],
                'meta'    => [],
                'failed'  => true,
                'error'   => $e->getMessage(),
                'source'  => null,
            ];
        }
    }

    /**
     * races 마스터 카탈로그 → 셀렉트 옵션 (id => name).
     *
     * @return array<int, string>
     */
    private static function loadRaceCatalogOptions(): array
    {
        $cached = Cache::get('core_api_races_catalog');
        if (is_array($cached) && $cached !== []) {
            return $cached;
        }

        $url = rtrim(config('services.core_api.url'), '/').'/api/races?limit=500';

        try {
            $response = Http::timeout(8)->get($url);
            if (! $response->successful()) {
                Log::warning("CORE API races 카탈로그 HTTP {$response->status()}: {$url}");

                return [];
            }

            $options = [];
            foreach ($response->json() ?? [] as $race) {
                $row = is_array($race) ? $race : (array) $race;
                $id = $row['id'] ?? null;
                $name = trim((string) ($row['name'] ?? ''));
                if (! $id || $name === '') {
                    continue;
                }
                $options[(int) $id] = $name;
            }
            asort($options, SORT_STRING);
            Cache::put('core_api_races_catalog', $options, 600);

            return $options;
        } catch (\Exception $e) {
            Log::warning('CORE API races 카탈로그 조회 실패: '.$e->getMessage());

            return [];
        }
    }

    public static function form(Schema $schema): Schema
    {
        $editionLoad    = static::loadEditionOptions();
        $editionOptions = $editionLoad['options'];
        $editionMeta    = $editionLoad['meta'];
        $editionFailed  = $editionLoad['failed'];
        $editionError   = $editionLoad['error'];
        $coreApiUrl     = rtrim(config('services.core_api.url'), '/');
        $raceCatalogOptions = static::loadRaceCatalogOptions();
        $branchOptions = Branch::where('status', 'active')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        // compose 시 meta 사용을 위해 요청 동안 공유
        Cache::put('core_api_editions_upcoming_meta', $editionMeta, 600);

        return $schema
            ->components([
            Section::make('기본 정보')
                ->schema([
                    TextInput::make('number')
                        ->label('기수 번호')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->unique(ignoreRecord: true)
                        ->helperText('예: 7'),

                    TextInput::make('alias')
                        ->label('별칭')
                        ->maxLength(100)
                        ->placeholder('선택 입력'),

                    Select::make('status')
                        ->label('상태')
                        ->options([
                            'upcoming' => '예정',
                            'active'   => '운영 중',
                            'ended'    => '종료',
                        ])
                        ->default('upcoming')
                        ->required(),
                ])
                ->columns(3),

            Section::make('운영 기간')
                ->description('다른 기수와 운영 기간이 겹치지 않아야 합니다.')
                ->schema([
                    DatePicker::make('start_date')
                        ->label('운영 시작일')
                        ->native(false)
                        ->displayFormat('Y년 m월 d일')
                        ->placeholder('날짜를 선택하세요')
                        ->live(),

                    DatePicker::make('end_date')
                        ->label('운영 종료일')
                        ->native(false)
                        ->displayFormat('Y년 m월 d일')
                        ->placeholder('날짜를 선택하세요')
                        ->live()
                        ->helperText('종료일은 시작일보다 이후여야 합니다.'),
                ])
                ->columns(2),

            Section::make('모집 안내')
                ->description('직접 신청서는 아래에서 폼을 연결하세요. 모집 기간·활성화는 «신청서 폼 관리»에서 설정합니다.')
                ->schema([
                    Placeholder::make('recruitment_status')
                        ->label('신청 현황')
                        ->content(function (?Generation $record) {
                            if (!$record?->exists) {
                                return '저장 후 신청 현황이 표시됩니다.';
                            }

                            $service = app(GenerationRecruitmentService::class);
                            $form    = $service->resolveApplicationForm($record);
                            $counts  = $service->applicationStatusCounts($record);
                            $open    = $service->isFormOpen($record);
                            $linked  = $form
                                ? "연결 폼: #{$form->id} {$form->title}" . ($form->cohort ? " ({$form->cohort})" : '')
                                : '연결 폼 없음';

                            return "{$counts['total']}명 신청 · "
                                . ($open ? '신청서 폼 노출 중' : '신청서 미등록 또는 모집 기간 외')
                                . " · {$linked}";
                        })
                        ->columnSpanFull(),

                    Placeholder::make('recruitment_link')
                        ->label('모집 링크')
                        ->content(function (?Generation $record) {
                            if (!$record?->exists) {
                                return '저장 후 모집 링크가 표시됩니다.';
                            }

                            $url = route('apply', ['generation' => $record->id], absolute: true);

                            $status = match (true) {
                                $record->apply_method === 'google_form' && $record->google_form_id
                                    => '<span class="font-medium text-success-600 dark:text-success-400">구글 폼 신청 방식</span>',
                                app(GenerationRecruitmentService::class)->isFormOpen($record)
                                    => '<span class="font-medium text-success-600 dark:text-success-400">직접 신청서 노출 중</span>',
                                default => '<span class="text-gray-600 dark:text-gray-400">모집 비활성 또는 기간 외</span>',
                            };

                            return new HtmlString(
                                '<div class="space-y-2">'
                                . '<a href="' . e($url) . '" target="_blank" rel="noopener" class="block rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-mono text-primary-600 hover:underline dark:border-gray-700 dark:bg-gray-800">'
                                . e($url)
                                . '</a>'
                                . '<p class="text-sm">' . $status . '</p>'
                                . '<p class="text-xs text-gray-500 dark:text-gray-400">공개 /apply 는 모집 중인 기수가 하나일 때만 자동 연결됩니다. 여러 기수면 이 링크(?generation=)를 쓰세요.</p>'
                                . '</div>'
                            );
                        })
                        ->columnSpanFull(),

                    Radio::make('apply_method')
                        ->label('신청 방식')
                        ->options([
                            'internal'    => '직접 신청서 폼',
                            'google_form' => '구글 폼으로 대체',
                        ])
                        ->default('internal')
                        ->live()
                        ->required()
                        ->columnSpanFull(),

                    Select::make('application_form_id')
                        ->label('연결 신청서 폼')
                        ->options(fn () => ApplicationForm::query()
                            ->orderByDesc('id')
                            ->get()
                            ->mapWithKeys(fn (ApplicationForm $f) => [
                                $f->id => "#{$f->id} {$f->title}"
                                    . ($f->cohort ? " · {$f->cohort}" : '')
                                    . ($f->is_active ? ' · 활성' : ' · 비활성'),
                            ])
                            ->all())
                        ->searchable()
                        ->nullable()
                        ->visible(fn (Get $get) => $get('apply_method') === 'internal')
                        ->required(fn (Get $get) => $get('apply_method') === 'internal')
                        ->helperText('이 기수 모집에 쓸 신청서 폼을 선택하세요. cohort 문자열만으로는 더 이상 의존하지 않습니다.')
                        ->columnSpanFull(),

                    Select::make('google_form_id')
                        ->label('연결 구글 폼')
                        ->options(fn (?Generation $record) => GoogleForm::query()
                            ->where('purpose', GoogleForm::PURPOSE_GENERATION_RECRUIT)
                            ->where(function ($q) use ($record) {
                                $q->whereNull('generation_id');
                                if ($record?->id) {
                                    $q->orWhere('generation_id', $record->id);
                                }
                            })
                            ->orderBy('title')
                            ->pluck('title', 'id')
                            ->all())
                        ->searchable()
                        ->visible(fn (Get $get) => $get('apply_method') === 'google_form')
                        ->required(fn (Get $get) => $get('apply_method') === 'google_form')
                        ->helperText('먼저 «구글 폼 연동»에서 용도=기수 모집으로 등록하세요.')
                        ->columnSpanFull(),
                ])
                ->visible(fn (?Generation $record) => (bool) $record?->exists)
                ->columns(2),

            Section::make('목표 대회')
                ->description('CORE upcoming edition만 선택합니다. 목록에 없거나 더 이상 고를 항목이 없으면 대회 등록을 요청하세요.')
                ->schema(array_filter([
                    $editionFailed ? Placeholder::make('api_warning')
                        ->label('')
                        ->content(new HtmlString(
                            '<div class="rounded-md bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800">'
                            . 'edition 목록을 불러오지 못했습니다. CORE 연결 후 다시 열어주세요.'
                            . '<br><span class="text-xs text-amber-600 mt-1 block">'
                            . 'URL: <code>' . e($coreApiUrl) . '/api/races/editions/upcoming</code>'
                            . ($editionError ? '<br>오류: ' . e($editionError) : '')
                            . '<br>복구 후: <code>php artisan cache:forget core_api_editions_upcoming</code>'
                            . '</span></div>'
                        )) : null,

                    Select::make('main_edition_ids')
                        ->label('목표 대회 (edition 다중 선택)')
                        ->options($editionOptions)
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->disabled($editionFailed)
                        ->noSearchResultsMessage('검색된 edition이 없습니다.')
                        ->noOptionsMessage('선택할 edition이 없습니다. 아래 「대회 등록 요청」으로 알려 주세요.')
                        ->helperText($editionFailed
                            ? 'CORE 연결 후 선택 가능'
                            : '오늘 이후·일정 미정 edition만 표시됩니다. 이미 고른 항목은 목록에서 빠집니다.'),

                    Placeholder::make('legacy_custom_races')
                        ->label('edition 미연결(레거시)')
                        ->content(function (?Generation $record): string {
                            if (! $record) {
                                return '';
                            }
                            $orphans = collect($record->mainRacesList())
                                ->filter(fn (array $r) => empty($r['edition_id']) && trim((string) ($r['name'] ?? '')) !== '')
                                ->pluck('name')
                                ->values();

                            if ($orphans->isEmpty()) {
                                return '없음';
                            }

                            return $orphans->join(', ').' — 저장 시 제거됩니다. edition을 다시 선택하거나 대회 등록을 요청하세요.';
                        })
                        ->visible(function (?Generation $record): bool {
                            if (! $record) {
                                return false;
                            }

                            return collect($record->mainRacesList())
                                ->contains(fn (array $r) => empty($r['edition_id']) && trim((string) ($r['name'] ?? '')) !== '');
                        })
                        ->columnSpanFull(),
                ]))
                ->footerActions([
                    Action::make('requestRaceRegistration')
                        ->label('대회 등록 요청')
                        ->icon('heroicon-o-plus-circle')
                        ->color('warning')
                        ->modalHeading('대회 등록 요청')
                        ->modalDescription('races 카탈로그에서 대회를 고르고, 등록이 필요한 edition(연도·일정)을 관리자에게 요청합니다. 직접 목표 대회로 저장되지 않습니다.')
                        ->modalSubmitActionLabel('요청 보내기')
                        ->form([
                            Select::make('race_id')
                                ->label('대회 (races 전체)')
                                ->options($raceCatalogOptions)
                                ->searchable()
                                ->preload()
                                ->required(fn (Get $get) => blank($get('new_race_name')))
                                ->helperText($raceCatalogOptions === []
                                    ? 'races 목록을 불러오지 못했습니다. 아래 신규 대회명으로 요청하세요.'
                                    : 'upcoming edition이 아닌 전체 races 마스터입니다.')
                                ->live(),

                            TextInput::make('new_race_name')
                                ->label('목록에 없는 신규 대회명')
                                ->maxLength(200)
                                ->required(fn (Get $get) => blank($get('race_id')))
                                ->helperText('카탈로그에 없을 때만 입력'),

                            DatePicker::make('requested_race_date')
                                ->label('희망 개최일')
                                ->native(false)
                                ->displayFormat('Y년 m월 d일')
                                ->placeholder('알고 있으면 선택'),

                            Textarea::make('note')
                                ->label('요청 메모')
                                ->rows(3)
                                ->maxLength(1000)
                                ->placeholder('예: 2026년 edition 등록 필요'),
                        ])
                        ->action(function (array $data) use ($raceCatalogOptions): void {
                            $user = auth()->user();
                            if (! $user) {
                                Notification::make()->danger()->title('로그인이 필요합니다.')->send();

                                return;
                            }

                            $raceId = $data['race_id'] ?? null;
                            $raceName = $raceId
                                ? ($raceCatalogOptions[(int) $raceId] ?? '')
                                : trim((string) ($data['new_race_name'] ?? ''));
                            if ($raceName === '') {
                                Notification::make()->danger()->title('대회를 선택하거나 신규 대회명을 입력하세요.')->send();

                                return;
                            }

                            $date = $data['requested_race_date'] ?? null;
                            $dateStr = $date ? (string) $date : '(미정)';
                            $note = trim((string) ($data['note'] ?? ''));
                            $lines = [
                                '기수 목표 대회용 edition 등록 요청입니다.',
                                'race_id: '.($raceId ?: '(신규)'),
                                '대회명: '.$raceName,
                                '희망 개최일: '.$dateStr,
                            ];
                            if ($note !== '') {
                                $lines[] = '메모: '.$note;
                            }

                            app(BugReportService::class)->create([
                                'title'       => '대회 등록 요청: '.$raceName,
                                'path'        => '/admin/generations',
                                'description' => implode("\n", $lines),
                                'severity'    => 'low',
                            ], $user);

                            Notification::make()
                                ->success()
                                ->title('대회 등록 요청을 보냈습니다')
                                ->body('버그제보로 접수되었습니다. 관리자가 races/edition을 등록하면 목표 대회에서 선택할 수 있습니다.')
                                ->send();
                        }),
                ])
                ->footerActionsAlignment(Alignment::Start),

            Section::make('활성화 지부')
                ->description('이 기수에 참여하는 지부를 선택하세요.')
                ->schema([
                    Select::make('active_branch_ids')
                        ->label('활성화 지부')
                        ->options($branchOptions)
                        ->multiple()
                        ->searchable()
                        ->placeholder('지부 선택')
                        ->noSearchResultsMessage('검색된 지부가 없습니다.')
                        ->noOptionsMessage('등록된 지부가 없습니다.')
                        ->helperText('복수 선택 가능'),
                ]),

            Section::make('기타사항')
                ->schema([
                    Textarea::make('notes')
                        ->label('기타사항')
                        ->rows(4)
                        ->maxLength(2000),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('기수')
                    ->formatStateUsing(fn (int $state): string => "{$state}기")
                    ->sortable(),

                TextColumn::make('alias')
                    ->label('별칭')
                    ->default('—'),

                TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'   => 'success',
                        'upcoming' => 'warning',
                        'ended'    => 'gray',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active'   => '운영 중',
                        'upcoming' => '예정',
                        'ended'    => '종료',
                        default    => $state,
                    }),

                TextColumn::make('start_date')
                    ->label('시작일')
                    ->date('Y.m.d')
                    ->placeholder('—'),

                TextColumn::make('end_date')
                    ->label('운영 종료')
                    ->date('Y.m.d')
                    ->placeholder('—'),

                TextColumn::make('main_races')
                    ->label('목표 대회')
                    ->getStateUsing(function (Generation $record): string {
                        $names = collect($record->mainRacesList())->pluck('name')->filter()->values();

                        return $names->isEmpty() ? '—' : $names->take(3)->join(', ').($names->count() > 3 ? '…' : '');
                    }),

                TextColumn::make('created_at')
                    ->label('생성일')
                    ->date('Y.m.d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        'active'   => '운영 중',
                        'upcoming' => '예정',
                        'ended'    => '종료',
                    ]),
            ])
            ->actions([
                EditAction::make()->label('수정'),
                DeleteAction::make()->label('삭제'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->role === 'super_admin'),
                ]),
            ])
            ->defaultSort('number', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    /**
     * 폼 가상 필드 → main_races JSON + 레거시 단건.
     * edition만 저장. 직접입력(이름만)은 지원하지 않음.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, array{race_id:?int, name:string}>  $editionMeta
     * @return array<string, mixed>
     */
    public static function composeMainRaces(array $data, array $editionMeta = []): array
    {
        $rows = [];
        $seen = [];

        foreach ($data['main_edition_ids'] ?? [] as $id) {
            if ($id === null || $id === '') {
                continue;
            }
            $editionId = (int) $id;
            $meta = $editionMeta[$editionId] ?? null;
            $name = trim((string) ($meta['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $key = 'edition:'.$editionId;
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $rows[] = [
                'edition_id' => $editionId,
                'race_id'    => $meta['race_id'] ?? null,
                'name'       => $name,
            ];
        }

        $data['main_races'] = array_values($rows);
        $first = $rows[0] ?? null;
        $data['main_race_id'] = $first['race_id'] ?? null;
        $data['main_race_name'] = $first['name'] ?? null;
        unset($data['main_edition_ids'], $data['main_race_custom']);

        return $data;
    }

    /**
     * main_races → 폼 가상 필드 (edition_id만).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function splitMainRacesForForm(array $data, ?Generation $record = null): array
    {
        $list = [];
        if ($record) {
            $list = $record->mainRacesList();
        } elseif (is_array($data['main_races'] ?? null)) {
            $list = $data['main_races'];
        }

        $ids = [];
        foreach ($list as $row) {
            $editionId = $row['edition_id'] ?? null;
            if ($editionId) {
                $ids[] = (int) $editionId;
            }
        }

        $data['main_edition_ids'] = $ids;
        unset($data['main_race_custom']);

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGenerations::route('/'),
            'create' => Pages\CreateGeneration::route('/create'),
            'edit'   => Pages\EditGeneration::route('/{record}/edit'),
        ];
    }
}
