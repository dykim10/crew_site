<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SmsResource\Pages;
use App\Models\Generation;
use App\Models\SmsLog;
use App\Services\SmsService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SmsResource extends Resource
{
    protected static ?string $model = SmsLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    protected static ?string $navigationLabel = '단체 문자';
    protected static ?string $modelLabel = '문자 발송';
    protected static ?string $pluralModelLabel = '문자 발송 내역';
    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): ?string
    {
        return '알림 관리';
    }

    public static function canCreate(): bool { return false; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(60),

                TextColumn::make('sender.nickname')
                    ->label('발송자')
                    ->default('-'),

                TextColumn::make('filter_type')
                    ->label('대상')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'generation' => '기수별',
                        'manual'     => '수동입력',
                        default      => '전체',
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'generation' => 'info',
                        'manual'     => 'warning',
                        default      => 'gray',
                    }),

                TextColumn::make('recipient_cnt')
                    ->label('발송 수')
                    ->alignCenter(),

                TextColumn::make('message')
                    ->label('메시지')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->message),

                TextColumn::make('result_data.success_count')
                    ->label('성공')
                    ->alignCenter()
                    ->color('success')
                    ->default(0),

                TextColumn::make('result_data.fail_count')
                    ->label('실패')
                    ->alignCenter()
                    ->color('danger')
                    ->default(0),

                TextColumn::make('created_at')
                    ->label('발송일시')
                    ->dateTime('Y.m.d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginationPageOptions([20, 50, 100]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSmsLogs::route('/'),
        ];
    }
}
