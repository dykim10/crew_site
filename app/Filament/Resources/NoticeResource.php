<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoticeResource\Pages;
use App\Models\Notice;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class NoticeResource extends Resource
{
    protected static ?string $model = Notice::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = '공지사항';
    protected static ?string $modelLabel = '공지';
    protected static ?string $pluralModelLabel = '공지사항 목록';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return '게시판 관리';
    }

    // operator 는 조회 전용 — 공지 작성 권한은 region_admin 이상
    public static function canCreate(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'region_admin']);
    }

    public static function canEdit(Model $record): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'region_admin']);
    }

    // 삭제는 되돌릴 수 없으므로 super_admin 단독 처리
    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role === 'super_admin';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make()
                ->schema([
                    TextInput::make('title')
                        ->label('제목')
                        ->required()
                        ->maxLength(200),

                    RichEditor::make('content')
                        ->label('내용')
                        ->required()
                        ->extraAttributes(['style' => 'min-height: 500px'])
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'strike',
                            'bulletList', 'orderedList',
                            'link', 'blockquote', 'undo', 'redo',
                        ]),
                ]),

            Section::make('게시 설정')
                ->schema([
                    Select::make('target_type')
                        ->label('공지 대상')
                        ->options([
                            'all'   => '전체',
                            'crew'  => '크루 전체',
                            'group' => '그룹',
                        ])
                        ->default('all')
                        ->required(),

                    Toggle::make('is_pinned')
                        ->label('상단 고정')
                        ->helperText('고정된 공지는 항상 목록 최상단에 표시됩니다.')
                        ->inline(false),

                    // 폼에 노출하지 않고 세션 유저를 자동 주입 — 작성자 위조 방지
                    Hidden::make('author_id')
                        ->default(fn () => auth()->id()),
                ])
                ->columns(2)
                ->collapsed(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_pinned')
                    ->label('고정')
                    ->boolean()
                    ->trueIcon('heroicon-o-bookmark')
                    ->falseIcon('heroicon-o-bookmark')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable(),

                TextColumn::make('title')
                    ->label('제목')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('target_type')
                    ->label('대상')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'all'   => 'primary',
                        'crew'  => 'info',
                        'group' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'all'   => '전체',
                        'crew'  => '크루',
                        'group' => '그룹',
                        default => $state,
                    }),

                TextColumn::make('author.nickname')
                    ->label('작성자')
                    ->default('-'),

                TextColumn::make('created_at')
                    ->label('작성일')
                    ->date('Y.m.d')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_pinned')
                    ->label('고정 여부')
                    ->trueLabel('고정만')
                    ->falseLabel('일반만'),

                SelectFilter::make('target_type')
                    ->label('대상')
                    ->options([
                        'all'   => '전체',
                        'crew'  => '크루',
                        'group' => '그룹',
                    ]),
            ])
            ->actions([
                EditAction::make()->label('수정'),
                DeleteAction::make()->label('삭제'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // 일괄 삭제는 super_admin 만 노출
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->role === 'super_admin'),
                ]),
            ])
            // 고정 공지 우선, 동순위는 최신순
            ->defaultSort('is_pinned', 'desc')
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListNotices::route('/'),
            'create' => Pages\CreateNotice::route('/create'),
            'edit'   => Pages\EditNotice::route('/{record}/edit'),
        ];
    }
}
