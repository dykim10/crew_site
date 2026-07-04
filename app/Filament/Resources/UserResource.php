<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\GenerationsRelationManager;
use App\Models\Administrator;
use App\Models\Generation;
use App\Models\User;
use App\Models\UserGeneration;
use App\Services\AdminLogService;
use App\Services\AdministratorService;
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
        return '크루 관리';
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

            Section::make('운영진')
                ->description('공개 운영진 소개 페이지(/administrator)에 노출되는 프로필입니다.')
                ->schema([
                    \Filament\Forms\Components\Placeholder::make('administrator_status')
                        ->label('상태')
                        ->content(function (?User $record) {
                            if (!$record?->administrator) {
                                return '운영진 미등록';
                            }
                            $admin = $record->administrator;
                            $role = Administrator::ROLES[$admin->role] ?? $admin->role;
                            $active = $admin->is_active ? '공개' : '비공개';

                            return "{$role} · {$active}";
                        }),
                ])
                ->visibleOn('edit')
                ->visible(fn (?User $record) => (bool) $record?->administrator),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('administrator'))
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

                TextColumn::make('administrator.role')
                    ->label('운영진')
                    ->badge()
                    ->placeholder('—')
                    ->formatStateUsing(fn (?string $state) => $state ? (Administrator::ROLES[$state] ?? $state) : null)
                    ->color(fn (?string $state) => $state ? match ($state) {
                        'branch_leader' => 'warning',
                        'crew_ops'      => 'success',
                        'photo'         => 'info',
                        default         => 'gray',
                    } : null)
                    ->toggleable(),

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
                Action::make('appoint_administrator')
                    ->label('운영진 임명')
                    ->icon('heroicon-o-user-circle')
                    ->color('success')
                    ->visible(fn (User $record) => in_array(auth()->user()->role, ['super_admin', 'region_admin'])
                        && !$record->administrator)
                    ->modalHeading('운영진 임명')
                    ->modalDescription(fn (User $record) => ($record->name ?? $record->nickname) . ' (@' . $record->nickname . ') 님을 운영진으로 등록합니다.')
                    ->modalSubmitActionLabel('임명')
                    ->form(fn (User $record) => AdministratorResource::staffProfileFormFields(includeUserSelect: false))
                    ->fillForm(fn (User $record) => [
                        'role'      => 'crew_ops',
                        'branch_id' => $record->branch_id,
                        'is_active' => true,
                        'sort_order' => 0,
                    ])
                    ->action(function (User $record, array $data) {
                        app(AdministratorService::class)->appoint($record, $data);
                        AdminLogService::log('administrator_appoint', 'User', $record->id,
                            "운영진 임명 → {$record->nickname}");
                        Notification::make()
                            ->title('운영진 임명 완료')
                            ->body($record->nickname . ' 님이 운영진으로 등록되었습니다.')
                            ->success()
                            ->send();
                    }),
                Action::make('dismiss_administrator')
                    ->label('운영진 해제')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('운영진 해제')
                    ->modalDescription(fn (User $record) => ($record->name ?? $record->nickname) . ' (@' . $record->nickname . ') 님의 운영진 등록을 해제합니다. 공개 페이지에서도 제거됩니다.')
                    ->modalSubmitActionLabel('해제')
                    ->visible(fn (User $record) => in_array(auth()->user()->role, ['super_admin', 'region_admin'])
                        && (bool) $record->administrator)
                    ->action(function (User $record) {
                        app(AdministratorService::class)->dismiss($record);
                        AdminLogService::log('administrator_dismiss', 'User', $record->id,
                            "운영진 해제 → {$record->nickname}");
                        Notification::make()
                            ->title('운영진 해제 완료')
                            ->body($record->nickname . ' 님의 운영진 등록이 해제되었습니다.')
                            ->success()
                            ->send();
                    }),
                Action::make('edit_administrator')
                    ->label('운영진 프로필')
                    ->icon('heroicon-o-pencil-square')
                    ->color('gray')
                    ->url(fn (User $record) => $record->administrator
                        ? AdministratorResource::getUrl('edit', ['record' => $record->administrator])
                        : null)
                    ->visible(fn (User $record) => (bool) $record->administrator),
                Action::make('send_verification_email')
                    ->label('인증 이메일 발송')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('인증 이메일 발송')
                    ->modalDescription(fn ($record) => $record->nickname . ' (' . $record->email . ') 에게 이메일 인증 링크를 발송합니다.')
                    ->modalSubmitActionLabel('발송')
                    ->visible(fn ($record) => is_null($record->email_verified_at))
                    ->action(function ($record) {
                        $record->sendEmailVerificationNotification();
                        AdminLogService::log('email_sent', 'User', $record->id,
                            "인증 이메일 발송 → {$record->name} ({$record->email})");
                        Notification::make()
                            ->title('발송 완료')
                            ->body($record->nickname . ' 회원에게 인증 이메일을 발송했습니다.')
                            ->success()
                            ->send();
                    }),
                Action::make('send_reset_password_email')
                    ->label('비밀번호 재설정 메일')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('비밀번호 재설정 이메일 발송')
                    ->modalDescription(fn ($record) => $record->nickname . ' (' . $record->email . ') 에게 비밀번호 재설정 링크를 발송합니다.')
                    ->modalSubmitActionLabel('발송')
                    ->action(function ($record) {
                        app(\App\Services\PasswordResetService::class)->sendLinkToUser($record);
                        AdminLogService::log('email_sent', 'User', $record->id,
                            "비밀번호 재설정 이메일 발송 → {$record->name} ({$record->email})");
                        Notification::make()
                            ->title('발송 완료')
                            ->body($record->nickname . ' 회원에게 재설정 이메일을 발송했습니다.')
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
