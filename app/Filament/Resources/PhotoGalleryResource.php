<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PhotoGalleryResource\Pages;
use App\Models\Branch;
use App\Models\PhotoGallery;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PhotoGalleryResource extends Resource
{
    protected static ?string $model = PhotoGallery::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = '포토 갤러리';
    protected static ?string $modelLabel = '포토';
    protected static ?string $pluralModelLabel = '포토 갤러리';
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return '게시판 관리';
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
                    ->maxLength(200)
                    ->columnSpan(2),

                Select::make('branch_id')
                    ->label('관련 지부')
                    ->options(fn () => Branch::pluck('name', 'id'))
                    ->placeholder('지부를 선택하세요')
                    ->searchable(),

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

            Section::make('공유 문구 / 태그')->schema([
                Textarea::make('description')
                    ->label('포스트 내용')
                    ->rows(8)
                    ->placeholder(
                        "예시)\n" .
                        "안녕하세요!\n팩 연대지부 여러분!\n\n" .
                        "5월 7일 훈련 사진을 공유드립니다.\n\n" .
                        "💛 @pac_run\n🏃‍♂️ @pac_kyongjj\n🗣 @kwon_taemin\n📸 @class0321\n\n" .
                        "#pacrun #teampac #너울 #리커버리"
                    )
                    ->helperText('SNS 공유 문구, @멘션, #해시태그 등을 자유롭게 입력하세요. 구글 포토 링크는 아래 별도 입력란에 입력하세요.'),

                TextInput::make('google_photos_url')
                    ->label('구글 포토 링크')
                    ->url()
                    ->maxLength(500)
                    ->placeholder('https://photos.app.goo.gl/...')
                    ->helperText('사용자에게 공유되는 구글 포토 앨범 링크를 입력하세요.'),
            ]),

            Section::make('이미지 업로드')->schema([
                FileUpload::make('image_url')
                    ->label('썸네일 이미지')
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
                    ->getStateUsing(function ($record) {
                        $url = $record->thumbnail_url ?: $record->image_url;
                        if (!$url) {
                            return null;
                        }
                        if (str_starts_with($url, 'http')) {
                            return $url;
                        }
                        // S3 key이면 임시 서명 URL 생성 (관리자 전용 — 1시간 유효)
                        try {
                            return Storage::disk('s3')->temporaryUrl($url, now()->addHour());
                        } catch (\Throwable) {
                            return Storage::disk('s3')->url($url);
                        }
                    })
                    ->width(60)
                    ->height(60)
                    ->square(),

                TextColumn::make('title')
                    ->label('제목')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('branch.name')
                    ->label('지부')
                    ->placeholder('-'),

                TextColumn::make('taken_at')
                    ->label('촬영일')
                    ->date('Y.m.d')
                    ->sortable(),

                IconColumn::make('google_photos_url')
                    ->label('구글포토')
                    ->icon(fn ($state) => $state ? 'heroicon-o-link' : null)
                    ->color('success')
                    ->boolean()
                    ->trueIcon('heroicon-o-link')
                    ->falseIcon('heroicon-o-minus')
                    ->tooltip(fn ($record) => $record->google_photos_url ?: '링크 없음'),

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
