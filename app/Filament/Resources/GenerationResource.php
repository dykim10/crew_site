<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GenerationResource\Pages;
use App\Models\Branch;
use App\Models\Generation;
use App\Services\GenerationRecruitmentService;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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

    /** @param iterable<int, object|array<string, mixed>> $rows */
    private static function buildRaceOptions(iterable $rows): array
    {
        $options = [];
        foreach ($rows as $race) {
            $row  = is_array($race) ? $race : (array) $race;
            $id   = $row['id'] ?? null;
            $name = $row['name'] ?? null;
            $date = isset($row['race_date']) ? substr((string) $row['race_date'], 0, 10) : '';
            if ($id && $name) {
                $options[$id] = $date ? "[{$date}] {$name}" : $name;
            }
        }

        return $options;
    }

    /** @return array{options: array<int|string, string>, failed: bool, error: ?string, source: ?string} */
    private static function loadRaceOptionsFromDb(): array
    {
        try {
            $rows = DB::select(
                <<<'SQL'
                SELECT r.id, r.name, ed.race_date
                FROM review.races r
                LEFT JOIN LATERAL (
                    SELECT re.race_date
                    FROM review.race_editions re
                    WHERE re.race_id = r.id
                      AND re.is_active = true
                    ORDER BY re.race_date DESC NULLS LAST
                    LIMIT 1
                ) ed ON true
                WHERE r.is_active = true
                ORDER BY r.id DESC
                LIMIT 200
                SQL
            );

            return [
                'options' => static::buildRaceOptions($rows),
                'failed'  => false,
                'error'   => null,
                'source'  => 'db',
            ];
        } catch (\Exception $e) {
            Log::warning('review.races DB 조회 실패: ' . $e->getMessage());

            return ['options' => [], 'failed' => true, 'error' => $e->getMessage(), 'source' => null];
        }
    }

    /** @return array{options: array<int|string, string>, failed: bool, error: ?string, source: ?string} */
    private static function loadRaceOptions(): array
    {
        $cached = Cache::get('core_api_races');
        if (is_array($cached)) {
            return ['options' => $cached, 'failed' => false, 'error' => null, 'source' => 'cache'];
        }

        $url = rtrim(config('services.core_api.url'), '/') . '/api/races/?limit=200';

        try {
            $response = Http::timeout(5)->get($url);

            if ($response->successful()) {
                $options = static::buildRaceOptions($response->json() ?? []);
                Cache::put('core_api_races', $options, 600);

                return ['options' => $options, 'failed' => false, 'error' => null, 'source' => 'api'];
            }

            Log::warning("CORE API 대회 목록 HTTP {$response->status()}: {$url}");
        } catch (\Exception $e) {
            Log::warning('CORE API 대회 목록 조회 실패: ' . $e->getMessage());
        }

        $dbResult = static::loadRaceOptionsFromDb();
        if (!$dbResult['failed']) {
            Cache::put('core_api_races', $dbResult['options'], 600);

            return $dbResult;
        }

        return [
            'options' => [],
            'failed'  => true,
            'error'   => $dbResult['error'] ?? 'CORE API 및 DB 조회 모두 실패',
            'source'  => null,
        ];
    }

    public static function form(Schema $schema): Schema
    {
        $raceLoad      = static::loadRaceOptions();
        $raceOptions   = $raceLoad['options'];
        $raceApiFailed = $raceLoad['failed'];
        $raceApiError  = $raceLoad['error'];
        $coreApiUrl    = rtrim(config('services.core_api.url'), '/');
        $branchOptions = Branch::where('status', 'active')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        return $schema->columns(1)->components([

            Section::make('기수 기본 정보')
                ->schema([
                    TextInput::make('number')
                        ->label('기수')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->suffix('기')
                        ->placeholder('예: 1'),

                    TextInput::make('alias')
                        ->label('기수 별칭')
                        ->maxLength(100)
                        ->placeholder('예: 봄 기수, 친해지길바래 1기'),

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
                ->description('모집 기간·활성화는 «신청서 폼 관리»에서 설정합니다. cohort를 기수 번호와 맞추세요 (예: 7기).')
                ->schema([
                    Placeholder::make('recruitment_status')
                        ->label('신청 현황')
                        ->content(function (?Generation $record) {
                            if (!$record?->exists) {
                                return '저장 후 신청 현황이 표시됩니다.';
                            }

                            $service = app(GenerationRecruitmentService::class);
                            $counts  = $service->applicationStatusCounts($record);
                            $open    = $service->isFormOpen($record);

                            return "{$counts['total']}명 신청 · "
                                . ($open ? '신청서 폼 노출 중' : '신청서 미등록 또는 모집 기간 외');
                        }),

                    Placeholder::make('recruitment_link')
                        ->label('모집 링크')
                        ->content(function (?Generation $record) {
                            if (!$record?->exists) {
                                return '저장 후 모집 링크가 표시됩니다.';
                            }

                            $url     = route('apply', absolute: true);
                            $service = app(GenerationRecruitmentService::class);
                            $form    = $service->resolveApplicationForm($record);
                            $open    = $service->isFormOpen($record);

                            $status = match (true) {
                                $open => '<span class="font-medium text-success-600 dark:text-success-400">현재 /apply 에 이 기수 신청 폼 노출 중</span>',
                                $form && !$form->is_active => '<span class="font-medium text-warning-600 dark:text-warning-400">신청서 폼이 비활성화되어 있습니다</span>',
                                $form => '<span class="text-gray-600 dark:text-gray-400">신청서 모집 기간(open_from/open_until) 외</span>',
                                default => '<span class="font-medium text-warning-600 dark:text-warning-400">cohort «' . e("{$record->number}기") . '» 신청서 폼을 등록해주세요</span>',
                            };

                            return new HtmlString(
                                '<div class="space-y-2">'
                                . '<a href="' . e($url) . '" target="_blank" rel="noopener" class="block rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-mono text-primary-600 hover:underline dark:border-gray-700 dark:bg-gray-800">'
                                . e($url)
                                . '</a>'
                                . '<p class="text-sm">' . $status . '</p>'
                                . '<p class="text-xs text-gray-500 dark:text-gray-400">메인·소개 페이지 「크루 참여하기」 버튼도 동일 링크입니다.</p>'
                                . '</div>'
                            );
                        })
                        ->columnSpanFull(),
                ])
                ->visible(fn (?Generation $record) => (bool) $record?->exists)
                ->columns(2),

            Section::make('메인 대회')
                ->description('대회 목록은 CORE API 또는 DB(review.races)에서 불러옵니다.')
                ->schema(array_filter([
                    $raceApiFailed ? Placeholder::make('api_warning')
                        ->label('')
                        ->content(new HtmlString(
                            '<div class="rounded-md bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800">'
                            . '⚠ CORE API에 연결할 수 없습니다. 아래 직접 입력란에 대회명을 입력하세요.'
                            . '<br><span class="text-xs text-amber-600 mt-1 block">'
                            . 'URL: <code>' . e($coreApiUrl) . '/api/races</code>'
                            . ($raceApiError ? '<br>오류: ' . e($raceApiError) : '')
                            . (app()->environment('local')
                                ? '<br>로컬: CORE API 미연결 시 DB(review.races) fallback 사용 · Supabase 스키마 저장 후 API 재시작'
                                : '<br>EC2: <code>sudo supervisorctl status fastapi</code>')
                            . '<br>복구 후: <code>php artisan cache:forget core_api_races</code>'
                            . '</span></div>'
                        )) : null,

                    !$raceApiFailed ? Select::make('main_race_id')
                        ->label('메인 대회 선택')
                        ->options($raceOptions)
                        ->searchable()
                        ->placeholder('대회를 검색하세요')
                        ->helperText(count($raceOptions) . '개 대회 로드됨')
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) use ($raceOptions) {
                            if ($state && isset($raceOptions[$state])) {
                                $name = preg_replace('/^\[\d{4}-\d{2}-\d{2}\]\s*/', '', $raceOptions[$state]);
                                $set('main_race_name', $name);
                            }
                        }) : null,

                    TextInput::make('main_race_name')
                        ->label($raceApiFailed ? '대회명 (직접 입력)' : '대회명 (자동 채워짐 또는 직접 입력)')
                        ->maxLength(200)
                        ->placeholder($raceApiFailed ? '대회명을 직접 입력하세요' : '대회 선택 시 자동 입력'),
                ])),

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

                TextColumn::make('main_race_name')
                    ->label('메인 대회')
                    ->limit(30)
                    ->default('—'),

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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGenerations::route('/'),
            'create' => Pages\CreateGeneration::route('/create'),
            'edit'   => Pages\EditGeneration::route('/{record}/edit'),
        ];
    }
}
