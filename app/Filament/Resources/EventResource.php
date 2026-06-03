<?php

namespace App\Filament\Resources;

use App\Exports\GenericExport;
use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers\RegistrationsRelationManager;
use App\Filament\Resources\EventResource\RelationManagers\ScoresRelationManager;
use App\Models\Branch;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Generation;
use App\Services\CryptoService;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;

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
        $branchOptions = Branch::where('status', 'active')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        return $schema->columns(1)->components([

            // ── 기본 정보 ───────────────────────────────────────────
            Section::make('기본 정보')
                ->schema([
                    TextInput::make('name')
                        ->label('이벤트명')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('location')
                        ->label('이벤트 장소')
                        ->maxLength(255)
                        ->placeholder('예: 반포 한강공원'),

                    RichEditor::make('description')
                        ->label('이벤트 설명')
                        ->toolbarButtons(['bold','italic','underline','bulletList','orderedList','link','undo','redo'])
                        ->columnSpan('full'),

                    FileUpload::make('thumbnail_url')
                        ->label('썸네일 이미지')
                        ->disk('s3')
                        ->directory('events/thumbnails')
                        ->visibility('public')
                        ->maxSize(10240)
                        ->image()
                        ->imageEditor()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                        ->helperText('10MB 이하 · webp 권장 (메인 페이지 4×1 카드에 노출)')
                        ->columnSpan('full'),

                    Select::make('event_type')
                        ->label('이벤트 타입')
                        ->options(['A' => 'A타입 — 기수 경쟁', 'B' => 'B타입 — 오픈 참가'])
                        ->default('B')
                        ->required()
                        ->live(),

                    Select::make('status')
                        ->label('상태')
                        ->options(['upcoming' => '예정', 'active' => '진행 중', 'ended' => '종료'])
                        ->default('upcoming')
                        ->required()
                        ->helperText('스케쥴러가 모집·이벤트 기간에 따라 자동 변경합니다.'),
                ])
                ->columns(2),

            // ── 모집 기간 ───────────────────────────────────────────
            Section::make('모집 기간')
                ->description('모집 시작일 00시에 active, 이벤트 종료일 경과 후 ended로 자동 전환됩니다.')
                ->schema([
                    DateTimePicker::make('recruit_start_at')
                        ->label('모집 시작일시')
                        ->seconds(false)
                        ->native(false)
                        ->helperText('이 날짜 포함 → active 전환'),

                    DateTimePicker::make('recruit_end_at')
                        ->label('모집 종료일시')
                        ->seconds(false)
                        ->native(false),
                ])
                ->columns(2),

            // ── 이벤트 기간 / 대상 ──────────────────────────────────
            Section::make('이벤트 기간 및 대상')
                ->schema([
                    DatePicker::make('start_date')
                        ->label('이벤트 시작일')
                        ->required()
                        ->native(false),

                    DatePicker::make('end_date')
                        ->label('이벤트 종료일')
                        ->required()
                        ->native(false)
                        ->helperText('종료일 경과 → ended 자동 전환'),

                    Select::make('target_scope')
                        ->label('참여 대상')
                        ->options([
                            'all'        => '전체 회원',
                            'generation' => '특정 기수',
                            'branch'     => '특정 지부',
                        ])
                        ->default('all')
                        ->live(),

                    Select::make('generation')
                        ->label('기수 선택')
                        ->options(
                            Generation::orderByDesc('number')
                                ->get()
                                ->mapWithKeys(fn ($g) => [
                                    $g->number => $g->alias ? "{$g->number}기 — {$g->alias}" : "{$g->number}기",
                                ])
                                ->toArray()
                        )
                        ->searchable()
                        ->placeholder('기수를 선택하세요')
                        ->visible(fn ($get) => $get('target_scope') === 'generation'),

                    Select::make('branch_id')
                        ->label('지부 선택')
                        ->options($branchOptions)
                        ->searchable()
                        ->placeholder('지부를 선택하세요')
                        ->visible(fn ($get) => $get('target_scope') === 'branch'),

                    TextInput::make('max_participants')
                        ->label('최대 참가 인원')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(999)
                        ->placeholder('0')
                        ->helperText('0 또는 비워두면 제한 없음'),

                    Toggle::make('is_registration_open')
                        ->label('참가 신청 공개')
                        ->default(true)
                        ->inline(false),
                ])
                ->columns(2),

            // ── A타입 전용: 점수 설정 ────────────────────────────────
            Section::make('점수 설정 (A타입 전용)')
                ->schema([
                    Select::make('score_type')
                        ->label('점수 방식')
                        ->options([
                            'mileage_grade' => '마일리지 등급',
                            'fixed'         => '고정 점수',
                        ])
                        ->live(),

                    Repeater::make('score_config')
                        ->label('등급별 목표 거리')
                        ->schema([
                            TextInput::make('grade')->label('등급명')->required()->maxLength(20),
                            TextInput::make('target_km')->label('목표 km')->numeric()->required()->suffix('km'),
                        ])
                        ->columns(2)
                        ->defaultItems(3)
                        ->default([
                            ['grade' => 'A', 'target_km' => 120],
                            ['grade' => 'B', 'target_km' => 100],
                            ['grade' => 'C', 'target_km' => 80],
                        ])
                        ->addActionLabel('등급 추가')
                        ->reorderable()
                        ->visible(fn ($get) => $get('score_type') === 'mileage_grade'),
                ])
                ->visible(fn ($get) => $get('event_type') === 'A')
                ->columns(1),

            // ── B타입 전용: 참가 신청 폼 빌더 ────────────────────────
            Section::make('참가 신청 폼 설계 (B타입 전용)')
                ->description('성명·휴대폰번호는 자동 포함됩니다. 추가 항목만 설계하세요.')
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
                                        ->helperText('연락처·이메일 등 민감 정보에 사용하세요.'),
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
                                        ->schema([TextInput::make('value')->label('항목')->required()])
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
                                        ->schema([TextInput::make('value')->label('항목')->required()])
                                        ->minItems(2)
                                        ->addActionLabel('선택지 추가')
                                        ->reorderable(),
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
                ImageColumn::make('thumbnail_url')
                    ->label('')
                    ->disk('s3')
                    ->width(56)
                    ->height(40)
                    ->defaultImageUrl(null)
                    ->extraImgAttributes(['class' => 'rounded object-cover']),

                TextColumn::make('name')
                    ->label('이벤트명')
                    ->searchable()
                    ->limit(28)
                    ->description(fn (Event $r) => $r->location),

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

                TextColumn::make('recruit_start_at')
                    ->label('모집 시작')
                    ->dateTime('Y.m.d')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('recruit_end_at')
                    ->label('모집 종료')
                    ->dateTime('Y.m.d')
                    ->placeholder('—'),

                TextColumn::make('start_date')->label('이벤트 시작')->date('Y.m.d')->sortable(),
                TextColumn::make('end_date')->label('이벤트 종료')->date('Y.m.d'),

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
                TableAction::make('download_registrations')
                    ->label('신청자 엑셀')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn (Event $record) =>
                        $record->isTypeB() &&
                        in_array(auth()->user()->role, ['super_admin', 'region_admin', 'operator'])
                    )
                    ->action(function (Event $record) {
                        $crypto        = app(CryptoService::class);
                        $registrations = EventRegistration::with('user')
                            ->where('event_id', $record->id)
                            ->orderBy('created_at')
                            ->get();

                        // 기본 컬럼
                        $columns = [
                            '번호'     => fn ($r) => $r->id,
                            '신청일시'  => fn ($r) => $r->created_at->format('Y-m-d H:i'),
                            '성명'     => fn ($r) => $r->form_data['name'] ?? '',
                            '휴대폰번호' => fn ($r) => $r->phone_enc
                                ? $crypto->decrypt($r->phone_enc)
                                : '',
                            '이메일'    => fn ($r) => $r->email ?? '',
                        ];

                        // 동적 추가 컬럼 (name/phone/email 제외)
                        foreach ($record->form_schema ?? [] as $field) {
                            $key   = $field['key'] ?? null;
                            $label = $field['label'] ?? $key;
                            if (!$key || in_array($key, ['name', 'phone', 'email'])) continue;
                            $columns[$label] = function ($r) use ($key) {
                                $val = $r->form_data[$key] ?? null;
                                return is_array($val) ? implode(', ', $val) : ($val ?? '');
                            };
                        }

                        $exporter = new GenericExport(
                            data:    $registrations,
                            columns: $columns,
                            title:   "{$record->name} 신청자",
                        );

                        $filename = "이벤트_{$record->id}_신청자_" . now()->format('Ymd_His') . '.xlsx';

                        return Excel::download($exporter, $filename);
                    }),

                EditAction::make()->label('수정'),
                DeleteAction::make()->label('삭제'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->role === 'super_admin'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            RegistrationsRelationManager::class,
            ScoresRelationManager::class,
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
