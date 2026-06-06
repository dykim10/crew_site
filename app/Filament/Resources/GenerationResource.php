<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GenerationResource\Pages;
use App\Models\Branch;
use App\Models\Generation;
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

    // CORE API에서 대회 목록 로드 (10분 캐시)
    private static function loadRaceOptions(): array
    {
        return Cache::remember('core_api_races', 600, function () {
            try {
                $response = Http::timeout(5)
                    ->get(config('services.core_api.url') . '/api/races?limit=200');

                if ($response->successful()) {
                    $options = [];
                    foreach ($response->json() as $race) {
                        $id   = $race['id'] ?? null;
                        $name = $race['name'] ?? null;
                        $date = isset($race['race_date']) ? substr($race['race_date'], 0, 10) : '';
                        if ($id && $name) {
                            $options[$id] = $date ? "[{$date}] {$name}" : $name;
                        }
                    }
                    return $options;
                }
            } catch (\Exception $e) {
                Log::warning('CORE API 대회 목록 조회 실패: ' . $e->getMessage());
            }
            return [];
        });
    }

    public static function form(Schema $schema): Schema
    {
        $raceOptions   = static::loadRaceOptions();
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

            Section::make('메인 대회')
                ->description('대회 목록은 CORE API (review.races)에서 불러옵니다.')
                ->schema(array_filter([
                    $raceOptions ? null : Placeholder::make('api_warning')
                        ->label('')
                        ->content(new HtmlString(
                            '<div class="rounded-md bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800">'
                            . '⚠ CORE API에 연결할 수 없습니다. 아래 직접 입력란에 대회명을 입력하세요.'
                            . '<br><span class="text-xs text-amber-600 mt-1 block">EC2: <code>sudo supervisorctl status fastapi</code> 확인</span>'
                            . '</div>'
                        )),

                    $raceOptions ? Select::make('main_race_id')
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
                        ->label($raceOptions ? '대회명 (자동 채워짐 또는 직접 입력)' : '대회명 (직접 입력)')
                        ->maxLength(200)
                        ->placeholder($raceOptions ? '대회 선택 시 자동 입력' : '대회명을 직접 입력하세요'),
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
                    ->default('—'),

                TextColumn::make('end_date')
                    ->label('종료일')
                    ->date('Y.m.d')
                    ->default('—'),

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
