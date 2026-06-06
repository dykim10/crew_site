<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RunningLogResource\Pages;
use App\Models\RunningLog;
use App\Services\AdminLogService;
use App\Services\RunningLogService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\HtmlString;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RunningLogResource extends Resource
{
    protected static ?string $model = RunningLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = '러닝 기록';
    protected static ?string $modelLabel = '러닝 기록';
    protected static ?string $pluralModelLabel = '러닝 기록 목록';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return '러닝 기록';
    }

    // 관리자는 조회·확인만, 수정/삭제는 super_admin
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->role === 'super_admin';
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role === 'super_admin';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([

            Section::make('업로드 이미지')
                ->schema([
                    Placeholder::make('image_preview')
                        ->label('')
                        ->content(fn ($record): HtmlString => new HtmlString(
                            '<div style="background:#1A1212;border-radius:0.75rem;display:flex;align-items:center;justify-content:center;overflow:hidden;max-height:480px;">'
                            . '<img src="' . e($record->image_url) . '" style="max-width:100%;object-fit:contain;max-height:480px;" alt="러닝 기록 이미지">'
                            . '</div>'
                        )),
                ])
                ->visible(fn ($record): bool => (bool) $record?->image_url),

            Section::make('기록 정보')
                ->schema([
                    TextInput::make('user.nickname')
                        ->label('회원')
                        ->disabled(),

                    DatePicker::make('run_date')
                        ->label('러닝 날짜')
                        ->required()
                        ->native(false)
                        ->displayFormat('Y년 m월 d일')
                        ->placeholder('날짜를 선택하세요'),

                    TextInput::make('distance_km')
                        ->label('거리 (km)')
                        ->numeric()
                        ->required(),

                    TextInput::make('duration_seconds')
                        ->label('시간 (H:MM:SS)')
                        ->placeholder('1:23:45')
                        ->formatStateUsing(function ($state): string {
                            if (!$state) return '';
                            $h = intdiv((int)$state, 3600);
                            $m = intdiv((int)$state % 3600, 60);
                            $s = (int)$state % 60;
                            return $h > 0
                                ? sprintf('%d:%02d:%02d', $h, $m, $s)
                                : sprintf('%d:%02d', $m, $s);
                        })
                        ->dehydrateStateUsing(function ($state): ?int {
                            if (!$state) return null;
                            $parts = explode(':', trim((string)$state));
                            if (count($parts) === 3) {
                                return (int)$parts[0] * 3600 + (int)$parts[1] * 60 + (int)$parts[2];
                            }
                            return (int)$parts[0] * 60 + (int)($parts[1] ?? 0);
                        }),

                    TextInput::make('avg_pace_seconds')
                        ->label("평균 페이스 (M'SS\")")
                        ->placeholder("5'30\"")
                        ->formatStateUsing(function ($state): string {
                            if (!$state) return '';
                            $m = intdiv((int)$state, 60);
                            $s = (int)$state % 60;
                            return sprintf("%d'%02d\"", $m, $s);
                        })
                        ->dehydrateStateUsing(function ($state): ?int {
                            if (!$state) return null;
                            if (preg_match("/^(\d+)'(\d{2})\"?$/", trim((string)$state), $mt)) {
                                return (int)$mt[1] * 60 + (int)$mt[2];
                            }
                            return is_numeric($state) ? (int)$state : null;
                        }),

                    TextInput::make('best_pace_seconds')
                        ->label("최고 페이스 (M'SS\")")
                        ->placeholder("4'30\"")
                        ->formatStateUsing(function ($state): string {
                            if (!$state) return '';
                            $m = intdiv((int)$state, 60);
                            $s = (int)$state % 60;
                            return sprintf("%d'%02d\"", $m, $s);
                        })
                        ->dehydrateStateUsing(function ($state): ?int {
                            if (!$state) return null;
                            if (preg_match("/^(\d+)'(\d{2})\"?$/", trim((string)$state), $mt)) {
                                return (int)$mt[1] * 60 + (int)$mt[2];
                            }
                            return is_numeric($state) ? (int)$state : null;
                        }),

                    TextInput::make('calories')
                        ->label('칼로리 (kcal)')
                        ->numeric(),

                    TextInput::make('avg_heart_rate')
                        ->label('평균 심박수')
                        ->numeric(),

                    TextInput::make('elevation_m')
                        ->label('고도 (m)')
                        ->numeric(),
                ]),

            Section::make('설정')
                ->schema([
                    Toggle::make('is_indoor')
                        ->label('실내 러닝')
                        ->inline(false),

                    Toggle::make('is_confirmed')
                        ->label('관리자 확인')
                        ->helperText('확인된 기록은 이벤트 점수 산정에 포함됩니다.')
                        ->inline(false),

                    Textarea::make('memo')
                        ->label('메모')
                        ->rows(3),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.nickname')
                    ->label('회원')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('run_date')
                    ->label('날짜')
                    ->date('Y.m.d')
                    ->sortable(),

                TextColumn::make('distance_km')
                    ->label('거리')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . ' km')
                    ->sortable(),

                TextColumn::make('avg_pace_seconds')
                    ->label('평균 페이스')
                    ->formatStateUsing(function ($state): string {
                        if (!$state) return '-';
                        $m = intdiv($state, 60);
                        $s = $state % 60;
                        return sprintf("%d'%02d\"", $m, $s);
                    }),

                TextColumn::make('duration_seconds')
                    ->label('시간')
                    ->formatStateUsing(function ($state): string {
                        if (!$state) return '-';
                        $h = intdiv($state, 3600);
                        $m = intdiv($state % 3600, 60);
                        $s = $state % 60;
                        return $h > 0
                            ? sprintf('%d:%02d:%02d', $h, $m, $s)
                            : sprintf('%d:%02d', $m, $s);
                    }),

                IconColumn::make('is_indoor')
                    ->label('실내')
                    ->boolean()
                    ->trueIcon('heroicon-o-building-office')
                    ->falseIcon('heroicon-o-sun')
                    ->trueColor('info')
                    ->falseColor('success'),

                IconColumn::make('is_confirmed')
                    ->label('확인')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),
            ])
            ->filters([
                TernaryFilter::make('is_confirmed')
                    ->label('확인 여부')
                    ->trueLabel('확인 완료')
                    ->falseLabel('미확인'),

                TernaryFilter::make('is_indoor')
                    ->label('실내/실외')
                    ->trueLabel('실내')
                    ->falseLabel('실외'),

                Filter::make('run_date')
                    ->form([
                        DatePicker::make('from')->label('시작일')->native(false)->displayFormat('Y년 m월 d일')->placeholder('시작일'),
                        DatePicker::make('until')->label('종료일')->native(false)->displayFormat('Y년 m월 d일')->placeholder('종료일'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'],  fn ($q, $d) => $q->whereDate('run_date', '>=', $d))
                            ->when($data['until'], fn ($q, $d) => $q->whereDate('run_date', '<=', $d));
                    })
                    ->label('날짜 범위'),
            ])
            ->actions([
                // 검수 확인/취소 토글 — 미확인이면 확인, 확인이면 취소
                Action::make('toggle_confirm')
                    ->label(fn (RunningLog $record): string => $record->is_confirmed ? '확인 취소' : '검수 확인')
                    ->icon(fn (RunningLog $record): string => $record->is_confirmed
                        ? 'heroicon-o-x-circle'
                        : 'heroicon-o-check-circle')
                    ->color(fn (RunningLog $record): string => $record->is_confirmed ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (RunningLog $record): string => $record->is_confirmed ? '검수 확인을 취소하시겠습니까?' : '검수를 확인하시겠습니까?')
                    ->modalDescription(fn (RunningLog $record): string => $record->is_confirmed
                        ? '확인 취소 시 마일리지에서 제외됩니다.'
                        : '확인 시 유저의 마일리지에 즉시 반영됩니다.')
                    ->action(function (RunningLog $record): void {
                        $service = app(RunningLogService::class);
                        if ($record->is_confirmed) {
                            $service->adminUnconfirm($record);
                            AdminLogService::log('confirm_canceled', 'RunningLog', $record->id,
                                "러닝 기록 #{$record->id} 검수 취소 (사용자: {$record->user?->name})");
                        } else {
                            $service->adminConfirm($record);
                            AdminLogService::log('confirmed', 'RunningLog', $record->id,
                                "러닝 기록 #{$record->id} 검수 확인 (사용자: {$record->user?->name}, {$record->distance_km}km)");
                        }
                    }),

                EditAction::make()->label('수정'),
                DeleteAction::make()->label('삭제'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // 일괄 검수 확인 — 미확인 건만 처리
                    BulkAction::make('bulk_confirm')
                        ->label('검수 확인')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('선택한 기록을 검수 확인하시겠습니까?')
                        ->modalDescription('확인된 기록은 각 유저의 마일리지에 즉시 반영됩니다.')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records): void {
                            $service = app(RunningLogService::class);
                            foreach ($records as $record) {
                                $service->adminConfirm($record);
                                AdminLogService::log('confirmed', 'RunningLog', $record->id,
                                    "러닝 기록 #{$record->id} 일괄 검수 확인 (사용자: {$record->user?->name}, {$record->distance_km}km)");
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    // 일괄 검수 취소
                    BulkAction::make('bulk_unconfirm')
                        ->label('확인 취소')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('선택한 기록의 검수 확인을 취소하시겠습니까?')
                        ->modalDescription('취소 시 해당 기록이 마일리지에서 제외됩니다.')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records): void {
                            $service = app(RunningLogService::class);
                            foreach ($records as $record) {
                                $service->adminUnconfirm($record);
                                AdminLogService::log('confirm_canceled', 'RunningLog', $record->id,
                                    "러닝 기록 #{$record->id} 일괄 검수 취소 (사용자: {$record->user?->name})");
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->role === 'super_admin'),
                ]),
            ])
            ->defaultSort('run_date', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRunningLogs::route('/'),
            'edit'  => Pages\EditRunningLog::route('/{record}/edit'),
        ];
    }
}
