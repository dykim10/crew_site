<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SponsorResource\Pages;
use App\Models\Sponsor;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SponsorResource extends Resource
{
    protected static ?string $model = Sponsor::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = '스폰서/협약업체';
    protected static ?string $modelLabel = '스폰서';
    protected static ?string $pluralModelLabel = '스폰서 목록';
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return '크루 관리';
    }

    public static function canCreate(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'region_admin']);
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

            Section::make('기본 정보')
                ->schema([
                    TextInput::make('name')
                        ->label('업체명')
                        ->required()
                        ->maxLength(100)
                        ->placeholder('예: 나이키 코리아, ABC 스포츠'),

                    TextInput::make('link_url')
                        ->label('링크 URL')
                        ->url()
                        ->maxLength(500)
                        ->placeholder('https://example.com')
                        ->helperText('로고 클릭 시 이동할 링크'),

                    TextInput::make('sort_order')
                        ->label('정렬 순서')
                        ->numeric()
                        ->default(0)
                        ->helperText('숫자가 낮을수록 앞에 표시됩니다.'),

                    Toggle::make('is_active')
                        ->label('노출 여부')
                        ->default(true)
                        ->helperText('비활성화 시 사용자 페이지에 노출되지 않습니다.'),
                ])
                ->columns(2),

            Section::make('로고 이미지')
                ->schema([
                    FileUpload::make('logo_url')
                        ->label('로고 이미지')
                        ->image()
                        ->disk('s3')
                        ->directory('sponsors')
                        ->visibility('public')
                        ->maxSize(2048)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                        ->helperText('권장: 투명 배경 PNG/WebP, 최대 2MB'),
                ]),

            Section::make('소개')
                ->schema([
                    Textarea::make('description')
                        ->label('업체 소개')
                        ->maxLength(300)
                        ->rows(3)
                        ->placeholder('업체에 대한 간략한 소개를 입력하세요. (최대 300자)'),
                ]),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('순서')
                    ->sortable()
                    ->alignCenter()
                    ->width(60),

                ImageColumn::make('logo_url')
                    ->label('로고')
                    ->disk('s3')
                    ->visibility('public')
                    ->size(48)
                    ->defaultImageUrl(asset('images/placeholder.png')),

                TextColumn::make('name')
                    ->label('업체명')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Sponsor $record) => $record->description
                        ? mb_strimwidth($record->description, 0, 40, '…')
                        : null),

                TextColumn::make('link_url')
                    ->label('링크')
                    ->limit(40)
                    ->url(fn (Sponsor $record) => $record->link_url, true)
                    ->default('—'),

                IconColumn::make('is_active')
                    ->label('노출')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('created_at')
                    ->label('등록일')
                    ->date('Y.m.d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('노출 여부')
                    ->trueLabel('노출 중')
                    ->falseLabel('비활성화'),
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
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSponsors::route('/'),
            'create' => Pages\CreateSponsor::route('/create'),
            'edit'   => Pages\EditSponsor::route('/{record}/edit'),
        ];
    }
}
