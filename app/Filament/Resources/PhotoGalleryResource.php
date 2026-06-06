<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PhotoGalleryResource\Pages;
use App\Models\PhotoGallery;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
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

    public static function canCreate(): bool
    {
        return auth()->user()->canManagePhoto();
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->canManagePhoto();
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->canManagePhoto();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([

            Section::make('기본 정보')->schema([
                TextInput::make('title')
                    ->label('제목')
                    ->required()
                    ->maxLength(200),

                DatePicker::make('taken_at')
                    ->label('촬영 일자')
                    ->native(false)
                    ->displayFormat('Y년 m월 d일')
                    ->placeholder('날짜를 선택하세요'),

                TextInput::make('sort_order')
                    ->label('정렬 순서')
                    ->numeric()
                    ->default(0)
                    ->helperText('숫자가 클수록 먼저 표시됩니다.'),
            ])->columns(3),

            Section::make('설명')->schema([
                Textarea::make('description')
                    ->label('')
                    ->rows(3),
            ]),

            Section::make('이미지 업로드')->schema([
                FileUpload::make('image_url')
                    ->label('원본 이미지')
                    ->image()
                    ->disk('s3')
                    ->directory('photo-galleries/' . now()->format('Y/m'))
                    ->maxSize(10240)
                    ->required()
                    ->helperText('업로드 후 자동으로 WebP 썸네일이 생성됩니다. (CORE API 연동)'),

                TextInput::make('thumbnail_url')
                    ->label('썸네일 URL')
                    ->disabled()
                    ->helperText('저장 후 자동 생성됩니다.')
                    ->placeholder('저장 후 자동 생성'),
            ]),

            // 등록한 관리자 자동 주입
            Hidden::make('admin_id')
                ->default(fn () => auth()->id()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_url')
                    ->label('썸네일')
                    ->defaultImageUrl(fn ($record) => $record->image_url)
                    ->width(60)
                    ->height(60)
                    ->square(),

                TextColumn::make('title')
                    ->label('제목')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('taken_at')
                    ->label('촬영일')
                    ->date('Y.m.d')
                    ->sortable(),

                TextColumn::make('view_count')
                    ->label('조회')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('순서')
                    ->sortable(),

                TextColumn::make('admin.nickname')
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
                        ->visible(fn () => auth()->user()->canManagePhoto()),
                ]),
            ])
            ->defaultSort('sort_order', 'desc')
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
