<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PhotoGalleryResource\Pages;
use App\Models\PhotoGallery;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PhotoGalleryResource extends Resource
{
    protected static ?string $model = PhotoGallery::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = '포토 갤러리';
    protected static ?string $modelLabel = '포토';
    protected static ?string $pluralModelLabel = '포토 갤러리';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return '게시판';
    }

    public static function canDelete(Model $record): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'region_admin']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([

            Section::make('기본 정보')->schema([
                TextInput::make('session_number')
                    ->label('회차')
                    ->numeric()
                    ->required()
                    ->suffix('회차'),

                TextInput::make('title')
                    ->label('제목')
                    ->required()
                    ->maxLength(200),

                DatePicker::make('taken_at')
                    ->label('촬영 일자')
                    ->required()
                    ->default(now()),
            ])->columns(3),

            Section::make('설명')->schema([
                Textarea::make('description')
                    ->label('')
                    ->rows(3),
            ]),

            Section::make('대표 이미지 / 외부 앨범')->schema([
                TextInput::make('cover_image_url')
                    ->label('대표 이미지 URL')
                    ->url()
                    ->placeholder('https://...')
                    ->helperText('목록에 썸네일로 표시됩니다.'),

                TextInput::make('album_url')
                    ->label('외부 앨범 링크')
                    ->url()
                    ->placeholder('Google Photos, Naver 앨범 등')
                    ->helperText('입력 시 상세 페이지에 "앨범 바로가기" 버튼이 표시됩니다.'),
            ])->columns(2),

            Section::make('사진 URL 목록')
                ->description('한 줄에 하나씩 사진 URL을 등록합니다. 상세 페이지에서 그리드로 표시됩니다.')
                ->schema([
                    Repeater::make('photo_urls')
                        ->label('')
                        ->schema([
                            TextInput::make('url')
                                ->label('사진 URL')
                                ->url()
                                ->required()
                                ->placeholder('https://...'),
                        ])
                        ->addActionLabel('사진 URL 추가')
                        ->reorderable()
                        ->collapsible()
                        ->defaultItems(0),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('session_number')
                    ->label('회차')
                    ->formatStateUsing(fn ($state) => $state . '회차')
                    ->sortable(),

                TextColumn::make('title')
                    ->label('제목')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('taken_at')
                    ->label('촬영일')
                    ->date('Y.m.d')
                    ->sortable(),

                TextColumn::make('photo_urls')
                    ->label('사진 수')
                    ->formatStateUsing(fn ($state) => count(json_decode($state ?? '[]', true)) . '장'),

                TextColumn::make('author.nickname')
                    ->label('등록자')
                    ->placeholder('-'),

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
                        ->visible(fn () => in_array(auth()->user()->role, ['super_admin', 'region_admin'])),
                ]),
            ])
            ->defaultSort('session_number', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPhotoGalleries::route('/'),
            'create' => Pages\CreatePhotoGallery::route('/create'),
            'edit'   => Pages\EditPhotoGallery::route('/{record}/edit'),
        ];
    }
}
