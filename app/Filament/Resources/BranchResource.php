<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use App\Models\User;
use Filament\Actions\Action as TableAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class BranchResource extends Resource
{
    private const IMAGE_THUMB_WIDTH = 48;
    private const IMAGE_THUMB_HEIGHT = 36;
    private const IMAGE_PREVIEW_SCALE = '50%';

    protected static ?string $model = Branch::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = '지부 관리';
    protected static ?string $modelLabel = '지부';
    protected static ?string $pluralModelLabel = '지부 목록';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return '크루 관리';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->role === 'super_admin';
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
        $adminOptions = User::whereIn('role', ['super_admin', 'region_admin'])
            ->orderBy('nickname')
            ->pluck('nickname', 'id')
            ->toArray();

        $operatorOptions = User::whereIn('role', ['super_admin', 'region_admin', 'operator'])
            ->orderBy('nickname')
            ->pluck('nickname', 'id')
            ->toArray();

        return $schema->columns(1)->components([
            Section::make('기본 정보')
                ->schema([
                    TextInput::make('name')
                        ->label('지부/지역명')
                        ->required()
                        ->maxLength(50)
                        ->placeholder('예: 반포, 연대, 인천, 군포'),

                    Select::make('status')
                        ->label('상태')
                        ->options(['active' => '활성', 'inactive' => '비활성'])
                        ->default('active')
                        ->required(),
                ])
                ->columns(2),

            Section::make('지부 소개')
                ->schema([
                    Placeholder::make('image_preview')
                        ->label('현재 이미지')
                        ->content(fn (?Branch $record) => filled($record?->getAttributes()['image_url'] ?? null)
                            ? new HtmlString(
                                '<img src="' . e(Branch::resolveImageUrl($record->getAttributes()['image_url'])) . '" '
                                . 'class="rounded-lg shadow block" style="width:' . self::IMAGE_PREVIEW_SCALE . ';height:auto;max-width:100%;" alt="지부 대표 이미지">'
                            )
                            : new HtmlString('<span class="text-gray-400 text-sm">등록된 이미지가 없습니다.</span>')
                        )
                        ->visible(fn (?Branch $record) => filled($record?->getAttributes()['image_url'] ?? null)),

                    FileUpload::make('image_url')
                        ->label('대표 이미지 변경')
                        ->image()
                        ->disk('s3')
                        ->directory('branches')
                        ->visibility('public')
                        ->maxSize(5120)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->helperText('새 이미지를 선택하면 기존 이미지가 교체됩니다. 권장 크기: 800×500px, 최대 5MB.'),

                    Textarea::make('branch_desc')
                        ->label('간략 소개')
                        ->maxLength(300)
                        ->rows(3)
                        ->placeholder('지부를 소개하는 한두 문장을 입력하세요. (최대 300자)')
                        ->helperText('사용자 지부소개 페이지에 노출됩니다.'),
                ]),

            Section::make('담당자')
                ->schema([
                    Select::make('admin_id')
                        ->label('지부 관리자')
                        ->options($adminOptions)
                        ->searchable()
                        ->placeholder('회원 선택')
                        ->helperText('구성원 관리에서 지부 관리자 권한을 부여하면 자동 지정됩니다. 수동 변경 시 기존 관리자를 대체합니다.'),

                    Select::make('operator_id')
                        ->label('지부 운영자')
                        ->options($operatorOptions)
                        ->searchable()
                        ->placeholder('회원 선택'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('이미지')
                    ->getStateUsing(fn (Branch $record) => Branch::resolveImageUrl($record->getAttributes()['image_url'] ?? null))
                    ->width(self::IMAGE_THUMB_WIDTH)
                    ->height(self::IMAGE_THUMB_HEIGHT)
                    ->extraImgAttributes([
                        'style' => 'width:' . self::IMAGE_THUMB_WIDTH . 'px;height:' . self::IMAGE_THUMB_HEIGHT . 'px;object-fit:cover;border-radius:4px;',
                    ]),

                TextColumn::make('name')
                    ->label('지부명')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('admin.nickname')
                    ->label('관리자')
                    ->default('—'),

                TextColumn::make('operator.nickname')
                    ->label('운영자')
                    ->default('—'),

                TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'active' ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state): string => $state === 'active' ? '활성' : '비활성'),

                TextColumn::make('created_at')
                    ->label('생성일')
                    ->date('Y.m.d')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('상태')
                    ->options(['active' => '활성', 'inactive' => '비활성']),
            ])
            ->actions([
                TableAction::make('view_image')
                    ->label('이미지 보기')
                    ->icon('heroicon-o-magnifying-glass-plus')
                    ->color('gray')
                    ->tooltip('이미지 크게 보기')
                    ->modalHeading(fn (Branch $record) => $record->name . ' — 대표 이미지')
                    ->modalContent(function (Branch $record) {
                        $url = Branch::resolveImageUrl($record->getAttributes()['image_url'] ?? null);
                        if (! filled($url)) {
                            return new HtmlString('<span class="text-gray-400 text-sm">등록된 이미지가 없습니다.</span>');
                        }

                        return new HtmlString(
                            '<img src="' . e($url) . '" alt="' . e($record->name) . '" '
                            . 'style="width:100%;max-width:640px;height:auto;border-radius:8px;object-fit:contain;display:block;margin:0 auto;">'
                        );
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('닫기')
                    ->visible(fn (Branch $record) => filled($record->getAttributes()['image_url'] ?? null)),

                EditAction::make()->label('수정'),
                DeleteAction::make()->label('삭제'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->role === 'super_admin'),
                ]),
            ])
            ->defaultSort('name')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit'   => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
