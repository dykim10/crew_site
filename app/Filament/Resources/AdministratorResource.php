<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdministratorResource\Pages;
use App\Models\Administrator;
use App\Models\Branch;
use App\Models\User;
use App\Services\AdministratorService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class AdministratorResource extends Resource
{
    protected static ?string $model = Administrator::class;

    protected static ?string $slug = 'administrator';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = '운영진';
    protected static ?string $modelLabel = '운영진';
    protected static ?string $pluralModelLabel = '운영진 목록';
    protected static string|\UnitEnum|null $navigationGroup = '크루 관리';
    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'region_admin', 'operator']);
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

    /** @return array<int, \Filament\Forms\Components\Component> */
    public static function staffProfileFormFields(bool $includeUserSelect = false): array
    {
        $branchOptions = Branch::where('status', 'active')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $fields = [];

        if ($includeUserSelect) {
            $fields[] = Select::make('user_id')
                ->label('구성원')
                ->options(fn () => app(AdministratorService::class)->memberOptionsForSelect())
                ->searchable()
                ->required()
                ->helperText('구성원 관리에 등록된 회원 중 운영진으로 임명할 대상을 선택합니다.');
        }

        $fields = array_merge($fields, [
            Select::make('role')
                ->label('역할')
                ->options(Administrator::ROLES)
                ->default('crew_ops')
                ->required(),

            Select::make('branch_id')
                ->label('소속 지부')
                ->options($branchOptions)
                ->searchable()
                ->placeholder('기타')
                ->nullable()
                ->live()
                ->helperText('등록된 지부를 선택하거나, 비워두고 아래 기타 소속을 입력하세요.'),

            TextInput::make('branch_custom')
                ->label('소속 (기타)')
                ->maxLength(50)
                ->placeholder('예: HQ, 전국')
                ->visible(fn (Get $get) => blank($get('branch_id'))),

            TextInput::make('sort_order')
                ->label('정렬 순서')
                ->numeric()
                ->default(0),

            Toggle::make('is_active')
                ->label('공개 페이지 노출')
                ->default(true),

            TextInput::make('instagram_url')
                ->label('인스타그램')
                ->url()
                ->maxLength(500)
                ->placeholder('https://instagram.com/...'),

            TextInput::make('youtube_url')
                ->label('유튜브')
                ->url()
                ->maxLength(500)
                ->placeholder('https://youtube.com/...'),

            Textarea::make('bio')
                ->label('자기소개')
                ->rows(4)
                ->maxLength(1000)
                ->columnSpanFull(),
        ]);

        return $fields;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([

            Section::make('연결 구성원')
                ->description('구성원 관리에 등록된 회원과 1:1로 연결됩니다. 지부 관리자 권한은 지부 관리 메뉴와 자동 연동되며, 운영진은 지부별로 여러 명 등록할 수 있습니다.')
                ->schema([
                    Placeholder::make('linked_user')
                        ->label('구성원')
                        ->content(fn (?Administrator $record) => $record?->user
                            ? new HtmlString(
                                '<span class="font-medium">' . e($record->display_name) . '</span>'
                                . ' <span class="text-gray-500">@' . e($record->user->nickname) . '</span>'
                            )
                            : '—')
                        ->visible(fn (?Administrator $record) => (bool) $record?->user_id),

                    ...static::staffProfileFormFields(includeUserSelect: true),
                ])
                ->columns(2)
                ->visibleOn('create'),

            Section::make('운영진 정보')
                ->schema(array_merge(
                    [
                        Placeholder::make('linked_user_edit')
                            ->label('연결 구성원')
                            ->content(fn (?Administrator $record) => $record?->user
                                ? new HtmlString(
                                    '<span class="font-medium">' . e($record->display_name) . '</span>'
                                    . ' <span class="text-gray-500">@' . e($record->user->nickname) . '</span>'
                                    . ' · <a class="text-primary-600 underline" href="'
                                    . e(UserResource::getUrl('edit', ['record' => $record->user_id]))
                                    . '">구성원 프로필</a>'
                                )
                                : e($record?->name ?? '—'))
                            ->columnSpanFull(),
                    ],
                    array_filter(
                        static::staffProfileFormFields(includeUserSelect: false),
                        fn ($field) => ! in_array($field->getName(), ['user_id'])
                    )
                ))
                ->columns(2)
                ->visibleOn('edit'),

            Section::make('프로필 이미지')
                ->description('비워두면 구성원 프로필 아바타를 사용합니다. 운영진 전용 이미지가 필요할 때만 업로드하세요.')
                ->schema([
                    Placeholder::make('profile_preview')
                        ->label('미리보기')
                        ->content(function (?Administrator $record) {
                            if (!$record) {
                                return new HtmlString('<span class="text-gray-400 text-sm">저장 후 표시됩니다.</span>');
                            }
                            $url = $record->public_profile_image_url;
                            if (!$url) {
                                return new HtmlString('<span class="text-gray-400 text-sm">이미지 없음 (구성원 아바타도 없음)</span>');
                            }

                            return new HtmlString(
                                '<img src="' . e($url) . '" class="h-24 w-24 rounded-full object-cover" alt="프로필">'
                            );
                        }),

                    FileUpload::make('profile_image')
                        ->label('운영진 전용 이미지 (선택)')
                        ->image()
                        ->avatar()
                        ->disk('s3')
                        ->directory('administrators')
                        ->visibility('public')
                        ->maxSize(2048)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->orientImagesFromExif(false)
                        ->imageEditor()
                        ->imageEditorMode(2)
                        ->imageEditorAspectRatios(['1:1'])
                        ->imageEditorEmptyFillColor('#1a1212')
                        ->imageEditorViewportWidth('400')
                        ->imageEditorViewportHeight('400')
                        ->helperText('업로드 후 연필(✎) 아이콘으로 회전·크롭할 수 있습니다. 원형 프로필에 맞게 1:1 정사각형 크롭을 권장합니다.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['user', 'branch']))
            ->columns([
                TextColumn::make('sort_order')
                    ->label('순서')
                    ->sortable()
                    ->alignCenter()
                    ->width(60),

                ImageColumn::make('profile_image')
                    ->label('프로필')
                    ->circular()
                    ->getStateUsing(fn (Administrator $record) => $record->public_profile_image_url),

                TextColumn::make('display_name')
                    ->label('이름')
                    ->searchable(query: fn ($query, $search) => $query
                        ->where('name', 'ilike', "%{$search}%")
                        ->orWhereHas('user', fn ($q) => $q->where('nickname', 'ilike', "%{$search}%")))
                    ->description(fn (Administrator $record) => $record->user ? '@' . $record->user->nickname : null),

                TextColumn::make('role')
                    ->label('역할')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Administrator::ROLES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'branch_leader' => 'warning',
                        'crew_ops'      => 'success',
                        'photo'         => 'info',
                        default         => 'gray',
                    }),

                TextColumn::make('branch.name')
                    ->label('지부')
                    ->default('—')
                    ->formatStateUsing(fn ($state, Administrator $record) => $record->branch_display),

                IconColumn::make('is_active')
                    ->label('노출')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('등록일')
                    ->date('Y.m.d')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('역할')
                    ->options(Administrator::ROLES),
                TernaryFilter::make('is_active')
                    ->label('노출 여부'),
            ])
            ->actions([
                EditAction::make()->label('수정'),
                DeleteAction::make()->label('해제'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('운영진 해제')
                        ->visible(fn () => auth()->user()->role === 'super_admin'),
                ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAdministrators::route('/'),
            'create' => Pages\CreateAdministrator::route('/create'),
            'edit'   => Pages\EditAdministrator::route('/{record}/edit'),
        ];
    }
}
