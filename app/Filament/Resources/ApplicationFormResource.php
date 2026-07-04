<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationFormResource\Pages;
use App\Models\ApplicationForm;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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

            Section::make('추가 항목 구성')
                ->description('이름·이메일·연락처는 기본 제공됩니다. 추가로 필요한 항목을 자유롭게 구성하세요.')
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
                DeleteAction::make()->label('삭제'),
            ])
            ->defaultSort('created_at', 'desc');
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
