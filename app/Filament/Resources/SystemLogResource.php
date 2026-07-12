<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemLogResource\Pages;
use App\Models\SystemLog;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SystemLogResource extends Resource
{
    protected static ?string $model = SystemLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = '시스템 로그';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = '시스템 로그';

    protected static ?string $modelLabel = '로그';

    public static function getNavigationGroup(): ?string
    {
        return '시스템';
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->role === 'super_admin';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

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

                Tables\Columns\TextColumn::make('source')
                    ->label('출처')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'core'   => 'info',
                        'crew'   => 'success',
                        'review' => 'warning',
                        default  => 'gray',
                    })
                    ->width('80px'),

                Tables\Columns\TextColumn::make('category')
                    ->label('분류')
                    ->formatStateUsing(fn (?string $state) => SystemLog::categoryLabel($state ?? ''))
                    ->badge()
                    ->color('gray')
                    ->width('100px'),

                Tables\Columns\TextColumn::make('level')
                    ->label('수준')
                    ->badge()
                    ->color(fn (?string $state) => SystemLog::levelColor($state ?? 'info'))
                    ->width('80px'),

                Tables\Columns\TextColumn::make('message')
                    ->label('메시지')
                    ->searchable()
                    ->limit(80)
                    ->tooltip(fn (SystemLog $record) => $record->message)
                    ->action(
                        Action::make('viewDetail')
                            ->modalHeading('로그 상세')
                            ->modalContent(fn (SystemLog $record) => view('filament.system-log-detail', ['record' => $record]))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('닫기')
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('source')
                    ->label('출처')
                    ->options([
                        'core'   => 'CORE',
                        'crew'   => 'CREW',
                        'review' => 'REVIEW',
                    ]),

                SelectFilter::make('category')
                    ->label('분류')
                    ->options([
                        'scheduler' => '스케줄러',
                        'backup'    => '백업',
                        'sms'       => 'SMS',
                        'crawler'   => '크롤링',
                        'ai'        => 'AI',
                        'auth'      => '인증',
                        'app_error' => '앱 오류',
                    ]),

                SelectFilter::make('level')
                    ->label('수준')
                    ->options([
                        'info'    => 'info',
                        'warning' => 'warning',
                        'error'   => 'error',
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
            'index' => Pages\ListSystemLogs::route('/'),
        ];
    }
}
