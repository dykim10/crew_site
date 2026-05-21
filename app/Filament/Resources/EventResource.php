<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers\RegistrationsRelationManager;
use App\Models\Event;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationLabel = '이벤트 관리';
    protected static ?string $modelLabel = '이벤트';
    protected static ?string $pluralModelLabel = '이벤트 목록';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return '러닝 기록';
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

            // ── 기본 정보 ───────────────────────────────────────
            Section::make('기본 정보')
                ->schema([
                    TextInput::make('name')
                        ->label('이벤트명')
                        ->required()
                        ->maxLength(100),

                    RichEditor::make('description')
                        ->label('이벤트 설명')
                        ->toolbarButtons(['bold','italic','underline','bulletList','orderedList','link','undo','redo']),

                    Select::make('event_type')
                        ->label('이벤트 타입')
                        ->options(['A' => 'A타입 — 기수 경쟁', 'B' => 'B타입 — 오픈 참가'])
                        ->default('B')
                        ->required()
                        ->live(),

                    Select::make('status')
                        ->label('상태')
                        ->options(['active' => '진행 중', 'upcoming' => '예정', 'ended' => '종료'])
                        ->default('upcoming')
                        ->required(),
                ])
                ->columns(1),

            // ── 기간 / 대상 ─────────────────────────────────────
            Section::make('기간 및 대상')
                ->schema([
                    DatePicker::make('start_date')->label('시작일')->required(),
                    DatePicker::make('end_date')->label('종료일')->required(),

                    Select::make('target_scope')
                        ->label('참여 대상')
                        ->options([
                            'all'        => '전체 회원',
                            'generation' => '특정 기수',
                            'crew'       => '크루',
                            'branch'     => '지부',
                            'group'      => '그룹',
                        ])
                        ->default('all')
                        ->live(),

                    TextInput::make('generation')
                        ->label('기수')
                        ->numeric()
                        ->visible(fn ($get) => $get('target_scope') === 'generation'),

                    TextInput::make('max_participants')
                        ->label('최대 참가 인원')
                        ->numeric()
                        ->helperText('비워두면 제한 없음'),

                    Toggle::make('is_registration_open')
                        ->label('참가 신청 오픈')
                        ->default(true)
                        ->inline(false),
                ])
                ->columns(2),

            // ── B타입 전용: 참가 신청 폼 빌더 ──────────────────
            Section::make('참가 신청 폼 설계')
                ->description('참가자가 입력할 항목을 자유롭게 추가하세요.')
                ->schema([
                    Builder::make('form_schema')
                        ->label('')
                        ->blocks([

                            Builder\Block::make('text')
                                ->label('단답 텍스트')
                                ->icon('heroicon-o-minus')
                                ->schema([
                                    TextInput::make('label')->label('항목명')->required(),
                                    Toggle::make('required')->label('필수 입력')->default(true)->inline(false),
                                    Toggle::make('encrypted')->label('개인정보 암호화 저장')->default(false)->inline(false)
                                        ->helperText('이름·연락처 등 개인정보는 암호화를 켜주세요.'),
                                ]),

                            Builder\Block::make('textarea')
                                ->label('장문 텍스트')
                                ->icon('heroicon-o-bars-3-bottom-left')
                                ->schema([
                                    TextInput::make('label')->label('항목명')->required(),
                                    Toggle::make('required')->label('필수 입력')->default(false)->inline(false),
                                ]),

                            Builder\Block::make('radio')
                                ->label('라디오 (단일 선택)')
                                ->icon('heroicon-o-check-circle')
                                ->schema([
                                    TextInput::make('label')->label('항목명')->required(),
                                    Toggle::make('required')->label('필수 입력')->default(true)->inline(false),
                                    Repeater::make('options')
                                        ->label('선택지')
                                        ->schema([
                                            TextInput::make('value')->label('항목')->required(),
                                        ])
                                        ->minItems(2)
                                        ->addActionLabel('선택지 추가')
                                        ->reorderable(),
                                ]),

                            Builder\Block::make('checkbox')
                                ->label('체크박스 (복수 선택)')
                                ->icon('heroicon-o-check')
                                ->schema([
                                    TextInput::make('label')->label('항목명')->required(),
                                    Toggle::make('required')->label('필수 입력')->default(false)->inline(false),
                                    Repeater::make('options')
                                        ->label('선택지')
                                        ->schema([
                                            TextInput::make('value')->label('항목')->required(),
                                        ])
                                        ->minItems(2)
                                        ->addActionLabel('선택지 추가')
                                        ->reorderable(),
                                ]),

                            Builder\Block::make('image')
                                ->label('이미지 업로드')
                                ->icon('heroicon-o-photo')
                                ->schema([
                                    TextInput::make('label')->label('항목명')->required(),
                                    Toggle::make('required')->label('필수 입력')->default(false)->inline(false),
                                ]),

                        ])
                        ->addActionLabel('항목 추가')
                        ->reorderable()
                        ->collapsible(),
                ])
                ->visible(fn ($get) => $get('event_type') === 'B'),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('이벤트명')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('event_type')
                    ->label('타입')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'A' ? 'warning' : 'info')
                    ->formatStateUsing(fn (string $state): string => $state === 'A' ? 'A타입' : 'B타입'),

                TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'   => 'success',
                        'upcoming' => 'warning',
                        'ended'    => 'gray',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active'   => '진행 중',
                        'upcoming' => '예정',
                        'ended'    => '종료',
                        default    => $state,
                    }),

                TextColumn::make('start_date')->label('시작일')->date('Y.m.d')->sortable(),
                TextColumn::make('end_date')->label('종료일')->date('Y.m.d'),

                TextColumn::make('registrations_count')
                    ->label('신청자')
                    ->counts('registrations')
                    ->suffix('명'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('상태')
                    ->options(['active' => '진행 중', 'upcoming' => '예정', 'ended' => '종료']),
                SelectFilter::make('event_type')
                    ->label('타입')
                    ->options(['A' => 'A타입', 'B' => 'B타입']),
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
            ->defaultSort('start_date', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            RegistrationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit'   => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
