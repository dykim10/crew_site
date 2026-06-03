<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\GenerationsRelationManager;
use App\Models\Generation;
use App\Models\User;
use App\Models\UserGeneration;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = '구성원 관리';
    protected static ?string $modelLabel = '구성원';
    protected static ?string $pluralModelLabel = '구성원 목록';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return '회원';
    }

    /**
     * 관리자가 직접 계정을 생성하는 시나리오는 없으므로 폼은 수정 전용 필드만 노출.
     * email·password 는 노출하지 않고, invite_code 는 히스토리 확인용으로 disabled 처리.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([

            Section::make('기본 계정 정보')
                ->schema([
                    TextInput::make('nickname')
                        ->label('닉네임')
                        ->required()
                        ->maxLength(50),

                    Select::make('role')
                        ->label('권한')
                        ->options([
                            'super_admin'  => '슈퍼관리자',
                            'region_admin' => '지역관리자',
                            'operator'     => '운영자',
                            'member'       => '일반멤버',
                        ])
                        ->required(),

                    Toggle::make('is_beta')
                        ->label('베타 사용자')
                        ->inline(false),

                    TextInput::make('invite_code')
                        ->label('초대 코드')
                        ->maxLength(20)
                        ->disabled(),
                ])
                ->columns(2),

            Section::make('크루 상세 정보')
                ->description('등급·훈련그룹·합류일은 이벤트 점수 산정에 사용됩니다.')
                ->schema([
                    Select::make('detail_grade')
                        ->label('등급')
                        ->options([
                            'A' => 'A등급',
                            'B' => 'B등급',
                            'C' => 'C등급',
                            'D' => 'D등급',
                        ])
                        ->placeholder('등급 미배정'),

                    TextInput::make('detail_training_group')
                        ->label('훈련 그룹')
                        ->maxLength(20)
                        ->placeholder('예: S, 1조, 2조'),

                    DatePicker::make('detail_join_date')
                        ->label('합류일')
                        ->native(false)
                        ->displayFormat('Y년 m월 d일')
                        ->placeholder('날짜를 선택하세요'),

                    Textarea::make('detail_memo')
                        ->label('관리자 메모')
                        ->rows(2)
                        ->maxLength(500)
                        ->columnSpanFull(),
                ])
                ->columns(3)
                ->visibleOn('edit'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nickname')
                    ->label('닉네임')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('이메일')
                    ->getStateUsing(fn ($record) => $record->email)
                    ->searchable(query: fn ($query, $search) => $query->where('email_hash', hash('sha256', strtolower($search))))
                    ->copyable()
                    ->placeholder('-'),

                TextColumn::make('role')
                    ->label('권한')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin'  => 'danger',
                        'region_admin' => 'warning',
                        'operator'     => 'info',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin'  => '슈퍼관리자',
                        'region_admin' => '지역관리자',
                        'operator'     => '운영자',
                        default        => '일반멤버',
                    }),

                IconColumn::make('is_beta')
                    ->label('베타')
                    ->boolean(),

                TextColumn::make('last_login_at')
                    ->label('최근 로그인')
                    ->dateTime('Y.m.d H:i')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('가입일')
                    ->date('Y.m.d')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('권한')
                    ->options([
                        'super_admin'  => '슈퍼관리자',
                        'region_admin' => '지역관리자',
                        'operator'     => '운영자',
                        'member'       => '일반멤버',
                    ]),
            ])
            ->actions([
                EditAction::make()->label('수정'),
                Action::make('reset_password')
                    ->label('비밀번호 초기화')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('비밀번호 초기화')
                    ->modalDescription(fn ($record) => $record->nickname . ' (' . $record->email . ') 회원의 비밀번호를 초기 비밀번호로 초기화합니다.')
                    ->modalSubmitActionLabel('초기화')
                    ->action(function ($record) {
                        $record->update(['password' => '1234qwer!@']);
                        Notification::make()
                            ->title('초기화 완료')
                            ->body($record->nickname . ' 회원의 비밀번호가 초기화되었습니다.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('update_generation')
                        ->label('기수 일괄 설정')
                        ->icon('heroicon-o-user-group')
                        ->color('info')
                        ->visible(fn () => in_array(auth()->user()->role, ['super_admin', 'region_admin']))
                        ->modalHeading('기수 일괄 설정')
                        ->modalDescription('선택한 구성원 전원의 기수 참여 이력을 추가하거나 업데이트합니다.')
                        ->modalSubmitActionLabel('적용')
                        ->form([
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
                                ->required()
                                ->searchable(),

                            DatePicker::make('joined_at')
                                ->label('합류일')
                                ->native(false)
                                ->displayFormat('Y년 m월 d일')
                                ->placeholder('날짜를 선택하세요')
                                ->helperText('비워두면 합류일을 기록하지 않습니다.'),

                            Toggle::make('is_current')
                                ->label('현재 소속 기수로 설정')
                                ->helperText('켜면 해당 구성원들의 기존 현재 기수 표시가 자동으로 해제됩니다.')
                                ->default(true)
                                ->inline(false),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $generationId = (int) $data['generation_id'];
                            $joinedAt     = $data['joined_at'] ?? null;
                            $isCurrent    = (bool) ($data['is_current'] ?? false);

                            foreach ($records as $user) {
                                // 현재 소속 기수 설정 시 기존 current 해제
                                if ($isCurrent) {
                                    UserGeneration::where('user_id', $user->id)
                                        ->where('generation_id', '!=', $generationId)
                                        ->where('is_current', true)
                                        ->update(['is_current' => false]);
                                }

                                UserGeneration::updateOrCreate(
                                    ['user_id' => $user->id, 'generation_id' => $generationId],
                                    ['joined_at' => $joinedAt, 'is_current' => $isCurrent]
                                );
                            }

                            $generation = Generation::find($generationId);
                            $label = $generation
                                ? ($generation->alias ? "{$generation->number}기 ({$generation->alias})" : "{$generation->number}기")
                                : "{$generationId}기";

                            Notification::make()
                                ->title('기수 설정 완료')
                                ->body("{$records->count()}명의 구성원에게 [{$label}] 기수 정보가 적용되었습니다.")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            GenerationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
