<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MainHeroImageResource\Pages;
use App\Models\MainHeroImage;
use App\Services\MainHeroImageService;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MainHeroImageResource extends Resource
{
    /** 업로드 허용 용량 (KB) — 크롭 전 원본 선택용 */
    public const MAX_UPLOAD_KB = 20480;

    /** 메인 pac-image 영역 비율 (420px 높이 · 2열 배너 왼쪽 칸) */
    public const PAC_IMAGE_ASPECT = '10:7';

    protected static ?string $model = MainHeroImage::class;

    protected static ?string $slug = 'main-page';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = '메인관리';
    protected static ?string $modelLabel = '메인 이미지';
    protected static ?string $pluralModelLabel = '메인 이미지';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return '메인';
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'region_admin']);
    }

    public static function canCreate(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'region_admin'])
            && MainHeroImage::query()->count() === 0;
    }

    public static function canEdit(Model $record): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'region_admin']);
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role === 'super_admin';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('메인 소개 영역 이미지')
                ->description('크롭한 영역을 CORE API로 WebP 변환·S3 저장합니다. 메인 페이지는 저장된 WebP URL을 배경으로 사용합니다.')
                ->schema([
                    Placeholder::make('preview')
                        ->label('저장된 배경 미리보기')
                        ->content(function (?MainHeroImage $record) {
                            $url = $record?->public_image_url ?? asset('images/main_default_img.jpg');

                            return new HtmlString(
                                '<div style="max-width:480px;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;">'
                                . '<div style="aspect-ratio:10/7;background:#1a1212;position:relative;overflow:hidden;">'
                                . '<img src="' . e($url) . '" alt="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center;">'
                                . '<div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(26,18,18,.28),rgba(61,40,0,.18));pointer-events:none;"></div>'
                                . '<div style="position:absolute;bottom:16px;left:16px;background:#E5AD16;color:#1A1212;font-size:10px;font-weight:700;letter-spacing:2px;padding:6px 12px;">Birds fly · Fish swim · Pac run</div>'
                                . '</div></div>'
                            );
                        }),

                    FileUpload::make('image_path')
                        ->label('메인 배경 이미지')
                        ->image()
                        ->maxSize(self::MAX_UPLOAD_KB)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->orientImagesFromExif(false)
                        ->imageEditor()
                        ->imageEditorMode(2)
                        ->imageEditorEmptyFillColor('#1a1212')
                        ->imageEditorViewportWidth('600')
                        ->imageEditorViewportHeight('420')
                        ->imageEditorAspectRatioOptions([
                            self::PAC_IMAGE_ASPECT,
                        ])
                        ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): ?string {
                            $webpUrl = app(MainHeroImageService::class)->convertAndStore($file);

                            return MainHeroImage::normalizeStoragePath($webpUrl);
                        })
                        ->required(fn (string $operation) => $operation === 'create')
                        ->helperText('① 파일 선택(최대 20MB) → ② 연필(✎)로 10:7 크롭 → ③ 저장(CORE API WebP → S3). 크롭 없이 저장하면 오류가 날 수 있습니다.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('저장된 배경')
                    ->getStateUsing(fn (MainHeroImage $record) => $record->public_image_url)
                    ->height(80),

                TextColumn::make('updated_at')
                    ->label('최종 수정')
                    ->dateTime('Y.m.d H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('등록일')
                    ->date('Y.m.d')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                EditAction::make()->label('수정'),
                DeleteAction::make()->label('삭제'),
            ])
            ->emptyStateHeading('등록된 메인 이미지가 없습니다')
            ->emptyStateDescription('메인 페이지에 기본 이미지(main_default_img.jpg)가 표시됩니다. 새 이미지를 등록해 주세요.')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMainHeroImages::route('/'),
            'create' => Pages\CreateMainHeroImage::route('/create'),
            'edit'   => Pages\EditMainHeroImage::route('/{record}/edit'),
        ];
    }
}
