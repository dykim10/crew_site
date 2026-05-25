<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BugReportResource\Pages;
use App\Models\BugReport;
use App\Services\BugReportService;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
        return '고객지원';
    }

    public static function canCreate(): bool { return false; }
    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role === 'super_admin';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([

            // 제보 내용 (읽기 전용)
            Section::make('제보 내용')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('title')
                        ->label('제목')
                        ->disabled(),

                    Textarea::make('description')
                        ->label('상세 내용')
                        ->disabled()
                        ->rows(6),

                    \Filament\Forms\Components\TextInput::make('user.nickname')
                        ->label('제보자')
                        ->disabled(),

                    \Filament\Forms\Components\TextInput::make('priority')
                        ->label('심각도')
                        ->disabled()
                        ->formatStateUsing(fn ($state) => match($state) {
                            'low'      => '낮음',
                            'medium'   => '보통',
                            'high'     => '높음',
                            'critical' => '긴급',
                            default    => $state,
                        }),
                ]),

            // 첨부파일 링크 (있을 때만)
            Section::make('첨부파일')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('attachment_url')
                        ->label('파일 URL')
                        ->disabled(),
                ])
                ->visible(fn ($record) => filled($record?->attachment_url))
                ->collapsed(),

            // 관리자 처리 영역
            Section::make('처리 내용')
                ->schema([
                    Select::make('status')
                        ->label('처리 상태')
                        ->options([
                            'pending'     => '접수 대기',
                            'in_progress' => '처리 중',
                            'resolved'    => '처리 완료',
                            'closed'      => '종료',
                        ])
                        ->required(),

                    Textarea::make('admin_note')
                        ->label('관리자 답변')
                        ->helperText('작성한 내용이 제보자에게 표시됩니다.')
                        ->rows(5),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(60),

                TextColumn::make('title')
                    ->label('제목')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('user.nickname')
                    ->label('제보자')
                    ->default('-'),

                TextColumn::make('priority')
                    ->label('심각도')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'critical' => 'danger',
                        'high'     => 'warning',
                        'medium'   => 'info',
                        'low'      => 'gray',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low'      => '낮음',
                        'medium'   => '보통',
                        'high'     => '높음',
                        'critical' => '긴급',
                        default    => $state,
                    }),

                TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'     => 'warning',
                        'in_progress' => 'info',
                        'resolved'    => 'success',
                        'closed'      => 'gray',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'     => '접수 대기',
                        'in_progress' => '처리 중',
                        'resolved'    => '처리 완료',
                        'closed'      => '종료',
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
                        'pending'     => '접수 대기',
                        'in_progress' => '처리 중',
                        'resolved'    => '처리 완료',
                        'closed'      => '종료',
                    ]),

                SelectFilter::make('priority')
                    ->label('심각도')
                    ->options([
                        'critical' => '긴급',
                        'high'     => '높음',
                        'medium'   => '보통',
                        'low'      => '낮음',
                    ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->role === 'super_admin'),
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
