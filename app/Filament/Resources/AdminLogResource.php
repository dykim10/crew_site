<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminLogResource\Pages;
use App\Models\AdminLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdminLogResource extends Resource
{
    protected static ?string $model = AdminLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = '관리자 로그';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string { return '시스템'; }

    protected static ?string $pluralModelLabel = '관리자 로그';

    protected static ?string $modelLabel = '로그';

    /** 슈퍼어드민만 접근 가능 */
    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }
    public static function canDeleteAny(): bool { return false; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('일시')
                    ->dateTime('Y.m.d H:i:s')
                    ->timezone('Asia/Seoul')
                    ->sortable()
                    ->width('160px'),

                Tables\Columns\TextColumn::make('admin.nickname')
                    ->label('관리자')
                    ->default(fn ($record) => $record->admin?->nickname ?? $record->admin?->name ?? '-')
                    ->searchable(query: fn (Builder $q, string $s) =>
                        $q->whereHas('admin', fn ($q2) => $q2->where('name', 'ilike', "%{$s}%")
                                                              ->orWhere('nickname', 'ilike', "%{$s}%"))
                    )
                    ->width('100px'),

                Tables\Columns\BadgeColumn::make('action')
                    ->label('액션')
                    ->formatStateUsing(fn ($state) => AdminLog::actionLabel($state))
                    ->color(fn ($state) => AdminLog::actionColor($state))
                    ->width('100px'),

                Tables\Columns\TextColumn::make('target_type')
                    ->label('대상 유형')
                    ->badge()
                    ->color('gray')
                    ->width('100px'),

                Tables\Columns\TextColumn::make('target_id')
                    ->label('ID')
                    ->default('-')
                    ->width('60px'),

                Tables\Columns\TextColumn::make('description')
                    ->label('내용')
                    ->searchable()
                    ->limit(80)
                    ->tooltip(fn ($record) => $record->description),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->width('110px'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('action')
                    ->label('액션')
                    ->options([
                        'confirmed'        => '검수 확인',
                        'confirm_canceled' => '검수 취소',
                        'approved'         => '승인',
                        'rejected'         => '반려',
                        'role_changed'     => '권한 변경',
                        'created'          => '생성',
                        'updated'          => '수정',
                        'deleted'          => '삭제',
                        'status_changed'   => '상태 변경',
                        'email_sent'       => '이메일 발송',
                        'pinned'           => '공지 고정',
                        'unpinned'         => '공지 해제',
                    ]),

                SelectFilter::make('target_type')
                    ->label('대상 유형')
                    ->options([
                        'RunningLog'           => '러닝 기록',
                        'User'                 => '사용자',
                        'BugReport'            => '버그 제보',
                        'EventFixedSubmission' => '고정점수 제출물',
                        'Notice'               => '공지사항',
                        'Event'                => '이벤트',
                    ]),

                Filter::make('today')
                    ->label('오늘')
                    ->query(fn (Builder $q) => $q->whereDate('created_at', today())),

                Filter::make('this_week')
                    ->label('이번 주')
                    ->query(fn (Builder $q) => $q->where('created_at', '>=', now()->startOfWeek())),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->striped()
            ->paginated([20, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdminLogs::route('/'),
        ];
    }
}
