<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MeetingMinuteResource\Pages;
use App\Models\MeetingMinute;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MeetingMinuteResource extends Resource
{
    protected static ?string $model = MeetingMinute::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = '회의록';
    protected static ?string $modelLabel = '회의록';
    protected static ?string $pluralModelLabel = '회의록 목록';
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return '게시판';
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role === 'super_admin';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('기본 정보')->schema([
                TextInput::make('title')
                    ->label('제목')
                    ->required()
                    ->maxLength(200),

                DatePicker::make('meeting_date')
                    ->label('회의 일자')
                    ->required()
                    ->native(false)
                    ->displayFormat('Y년 m월 d일')
                    ->placeholder('날짜를 선택하세요')
                    ->default(now()),

                Toggle::make('is_pinned')
                    ->label('상단 고정')
                    ->default(false)
                    ->inline(false),
            ])->columns(3),

            Section::make('회의 내용')->schema([
                RichEditor::make('content')
                    ->label('')
                    ->toolbarButtons([
                        'bold', 'italic', 'underline', 'strike',
                        'bulletList', 'orderedList',
                        'h2', 'h3',
                        'link', 'blockquote',
                        'undo', 'redo',
                    ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_pinned')
                    ->label('고정')
                    ->boolean()
                    ->trueIcon('heroicon-s-bookmark')
                    ->falseIcon('')
                    ->trueColor('warning'),

                TextColumn::make('meeting_date')
                    ->label('회의 일자')
                    ->date('Y.m.d')
                    ->sortable(),

                TextColumn::make('title')
                    ->label('제목')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('author.nickname')
                    ->label('작성자')
                    ->placeholder('-'),

                TextColumn::make('views')
                    ->label('조회')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('등록일')
                    ->date('Y.m.d')
                    ->sortable(),
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
            ->defaultSort('is_pinned', 'desc')
            ->defaultSort('meeting_date', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMeetingMinutes::route('/'),
            'create' => Pages\CreateMeetingMinute::route('/create'),
            'edit'   => Pages\EditMeetingMinute::route('/{record}/edit'),
        ];
    }
}
