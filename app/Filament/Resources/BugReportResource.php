<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BugReportResource\Pages;
use App\Models\BugReport;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class BugReportResource extends Resource
{
    protected static ?string $model = BugReport::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bug-ant';
    protected static ?string $navigationLabel = '버그 제보';
    protected static ?string $modelLabel = '버그 제보';
    protected static ?string $pluralModelLabel = '버그 제보 목록';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return '시스템';
    }

    public static function canCreate(): bool { return false; }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->isSuperAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([

            Section::make('제보 내용')->schema([
                TextInput::make('title')
                    ->label('제목')
                    ->disabled(),

                TextInput::make('path')
                    ->label('발생 경로')
                    ->disabled(),

                Textarea::make('description')
                    ->label('재현 방법')
                    ->disabled()
                    ->rows(6),

                TextInput::make('user.nickname')
                    ->label('제보자')
                    ->disabled(),

                TextInput::make('severity')
                    ->label('심각도')
                    ->disabled()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'low'    => '낮음',
                        'medium' => '보통',
                        'high'   => '높음',
                        default  => $state,
                    }),
            ]),

            Section::make('스크린샷')
                ->schema([
                    TextInput::make('screenshot_url')
                        ->label('이미지 URL')
                        ->disabled()
                        ->url(),
                ])
                ->visible(fn ($record) => filled($record?->screenshot_url))
                ->collapsed(),

            Section::make('처리')->schema([
                Select::make('status')
                    ->label('처리 상태')
                    ->options([
                        'open'        => '접수 대기',
                        'in_progress' => '처리 중',
                        'resolved'    => '처리 완료',
                    ])
                    ->required(),

                Textarea::make('admin_note')
                    ->label('관리자 답변')
                    ->rows(5),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('#')->sortable()->width(60),

                TextColumn::make('title')
                    ->label('제목')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('user.nickname')
                    ->label('제보자')
                    ->default('-'),

                TextColumn::make('severity')
                    ->label('심각도')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'high'   => 'warning',
                        'medium' => 'info',
                        'low'    => 'gray',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low'    => '낮음',
                        'medium' => '보통',
                        'high'   => '높음',
                        default  => $state,
                    }),

                TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open'        => 'warning',
                        'in_progress' => 'info',
                        'resolved'    => 'success',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open'        => '접수 대기',
                        'in_progress' => '처리 중',
                        'resolved'    => '처리 완료',
                        default       => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('접수일')
                    ->date('Y.m.d')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('처리 상태')
                    ->options([
                        'open'        => '접수 대기',
                        'in_progress' => '처리 중',
                        'resolved'    => '처리 완료',
                    ]),

                SelectFilter::make('severity')
                    ->label('심각도')
                    ->options(['high' => '높음', 'medium' => '보통', 'low' => '낮음']),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->isSuperAdmin()),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBugReports::route('/'),
            'edit'  => Pages\EditBugReport::route('/{record}/edit'),
        ];
    }
}
