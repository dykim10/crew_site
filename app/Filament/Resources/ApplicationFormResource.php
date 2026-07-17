<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationFormResource\Pages;
use App\Models\Application;
use App\Models\ApplicationForm;
use App\Models\Branch;
use App\Services\GenerationVisibilityService;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApplicationFormResource extends Resource
{
    protected static ?string $model = ApplicationForm::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = '신청서 폼 관리';
    protected static ?string $modelLabel = '신청서 폼';
    protected static ?string $pluralModelLabel = '신청서 폼 목록';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return '기수 모집';
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
                    TextInput::make('title')
                        ->label('폼 제목')
                        ->required()
                        ->maxLength(100)
                        ->placeholder('예: 2026 하반기 기수 신청서'),

                    TextInput::make('cohort')
                        ->label('기수')
                        ->maxLength(20)
                        ->placeholder('예: 7기'),

                    Textarea::make('subtitle')
                        ->label('안내 문구')
                        ->rows(3)
                        ->maxLength(500)
                        ->placeholder('/apply 페이지 상단에 표시되는 안내 문구'),

                    Toggle::make('is_active')
                        ->label('활성화 (신청 받기)')
                        ->helperText('활성화 + 모집 기간(open_from/open_until) 충족 시 /apply 에 노출됩니다. cohort는 기수 번호와 맞추세요 (예: 7기).')
                        ->default(false)
                        ->inline(false),
                ])
                ->columns(2),

            Section::make('모집 기간')
                ->schema([
                    DatePicker::make('open_from')
                        ->label('모집 시작일')
                        ->native(false)
                        ->displayFormat('Y년 m월 d일')
                        ->placeholder('날짜를 선택하세요')
                        ->nullable(),

                    DatePicker::make('open_until')
                        ->label('모집 종료일')
                        ->native(false)
                        ->displayFormat('Y년 m월 d일')
                        ->placeholder('날짜를 선택하세요')
                        ->nullable(),
                ])
                ->columns(2),

            Section::make('희망지부 모집')
                ->description('신청서에 필수「희망지부」로 노출됩니다. 비활성 지부는 목록에서 숨기고, 정원이 찬 지부는 마감 처리됩니다. (구글폼의 희망지부 라디오와 동일 역할)')
                ->schema([
                    Repeater::make('branch_settings')
                        ->label('지부별 모집 설정')
                        ->schema([
                            Select::make('branch_id')
                                ->label('지부')
                                ->options(fn () => Branch::query()
                                    ->where('status', 'active')
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all())
                                ->required()
                                ->searchable()
                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                            Toggle::make('is_active')
                                ->label('모집 활성')
                                ->default(true)
                                ->inline(false),

                            TextInput::make('max_applicants')
                                ->label('최대 인원')
                                ->numeric()
                                ->minValue(1)
                                ->nullable()
                                ->placeholder('비우면 무제한')
                                ->helperText('거절 제외 신청 수 기준'),
                        ])
                        ->columns(3)
                        ->defaultItems(0)
                        ->addActionLabel('지부 추가')
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => isset($state['branch_id'])
                            ? (Branch::find($state['branch_id'])?->name ?? '지부')
                            : null),
                ]),

            Section::make('안내 이미지')
                ->description('신청 페이지 상단에 표시됩니다. 최대 10장, 장당 2MB(합계 약 20MB). jpg/png/webp')
                ->schema([
                    FileUpload::make('images')
                        ->label('이미지')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->maxFiles(10)
                        ->maxSize(2048)
                        ->disk('s3')
                        ->directory('application-forms')
                        ->visibility('public')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->helperText('구글폼 상단 배너처럼 굿즈·안내 이미지를 올릴 수 있습니다.'),
                ]),

            Section::make('추가 항목 구성')
                ->description('이름·이메일·연락처·희망지부(위 설정)는 기본 제공됩니다. 「희망지부」커스텀 항목은 만들지 마세요. 상단「구글 시트 항목 가져오기」로 응답 시트 헤더(기본항목 제외)를 merge할 수 있습니다.')
                ->schema([
                    Builder::make('form_fields')
                        ->label('')
                        ->blocks([

                            Builder\Block::make('text')
                                ->label('단답 텍스트')
                                ->icon('heroicon-o-minus')
                                ->schema([
                                    TextInput::make('label')->label('항목명')->required(),
                                    Toggle::make('required')->label('필수 입력')->default(true)->inline(false),
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

                            Builder\Block::make('select')
                                ->label('드롭다운 선택')
                                ->icon('heroicon-o-chevron-down')
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

                        ])
                        ->addActionLabel('항목 추가')
                        ->reorderable()
                        ->collapsible(),
                ]),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('폼 제목')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('cohort')
                    ->label('기수')
                    ->placeholder('-'),

                IconColumn::make('is_active')
                    ->label('활성')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('open_from')
                    ->label('시작일')
                    ->date('Y.m.d')
                    ->placeholder('-'),

                TextColumn::make('open_until')
                    ->label('종료일')
                    ->date('Y.m.d')
                    ->placeholder('-'),

                TextColumn::make('applications_count')
                    ->label('신청 수')
                    ->counts('applications')
                    ->suffix('건'),

                TextColumn::make('created_at')
                    ->label('생성일')
                    ->date('Y.m.d')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make()->label('수정'),
                self::makeDeleteAction(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * 폼 삭제 + 관련 신청 내역 하드 삭제(개인정보 보호).
     * 「정말 삭제」확인 + 신청 내역 동시 삭제 체크 필수.
     */
    public static function makeDeleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->label('삭제')
            ->modalHeading('정말 삭제 하시겠습니까?')
            ->modalDescription(fn (ApplicationForm $record): string => sprintf(
                "「%s」신청서 폼을 삭제합니다.\n아래 체크박스에 동의하면 관련 신청 내역 %d건도 함께 영구 삭제됩니다. 되돌릴 수 없습니다.",
                $record->title,
                $record->applications()->count()
            ))
            ->modalSubmitActionLabel('삭제')
            ->form(fn (ApplicationForm $record): array => [
                Checkbox::make('delete_applications')
                    ->label(sprintf(
                        '관련 신청 내역 %d건도 모두 영구 삭제합니다 (개인정보 보호)',
                        $record->applications()->count()
                    ))
                    ->helperText('개인정보 보호를 위해 신청 내역도 함께 삭제해야 합니다. 체크 후 삭제를 진행하세요.')
                    ->accepted('관련 신청 내역 삭제에 동의해주세요.')
                    ->dehydrated(),
            ])
            ->using(function (ApplicationForm $record, array $data): void {
                if (! ($data['delete_applications'] ?? false)) {
                    throw ValidationException::withMessages([
                        'delete_applications' => '관련 신청 내역 삭제에 동의해주세요.',
                    ]);
                }

                $count = 0;
                DB::transaction(function () use ($record, &$count) {
                    $count = Application::query()
                        ->where('form_id', $record->id)
                        ->delete();
                    $record->delete();
                });

                GenerationVisibilityService::forgetCache();

                Notification::make()
                    ->success()
                    ->title('삭제 완료')
                    ->body("신청서 폼과 관련 신청 내역 {$count}건을 영구 삭제했습니다.")
                    ->send();
            });
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListApplicationForms::route('/'),
            'create' => Pages\CreateApplicationForm::route('/create'),
            'edit'   => Pages\EditApplicationForm::route('/{record}/edit'),
        ];
    }
}
