<?php

namespace App\Filament\Resources;

use App\Exports\BaseExport;
use App\Exports\GenericExport;
use App\Filament\Resources\GoogleFormResource\Pages;
use App\Models\GoogleForm;
use App\Services\GoogleFormService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
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
use Maatwebsite\Excel\Facades\Excel;

class GoogleFormResource extends Resource
{
    protected static ?string $model = GoogleForm::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = '구글 폼 연동';
    protected static ?string $modelLabel = '구글 폼';
    protected static ?string $pluralModelLabel = '구글 폼 목록';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return '알림 / 설문';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('폼 정보')
                ->schema([
                    TextInput::make('title')
                        ->label('폼 제목')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('sheet_id')
                        ->label('Google Sheet ID 또는 URL')
                        ->placeholder('1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgVE2upms  또는  전체 URL 붙여넣기')
                        ->helperText(function (): string {
                            $email = app(GoogleFormService::class)->getServiceAccountEmail();
                            $base = '구글 폼 → 응답 → 스프레드시트에서 보기 → URL의 /d/ 뒤 ID';
                            if (! $email) {
                                return $base . ' · 서비스 계정 키가 없으면 storage/app/google/service-account.json 을 배치하세요.';
                            }

                            return $base . ' · 시트 [공유]에 아래 이메일을 뷰어로 추가: ' . $email;
                        })
                        ->required()
                        ->maxLength(500)
                        ->dehydrateStateUsing(
                            fn (string $state): string => GoogleFormService::extractSheetId($state)
                        ),

                    Placeholder::make('google_share_guide')
                        ->label('시트 공유 안내')
                        ->content(function (): \Illuminate\Support\HtmlString {
                            $email = app(GoogleFormService::class)->getServiceAccountEmail();
                            if (! $email) {
                                return new \Illuminate\Support\HtmlString(
                                    '<span class="text-sm text-gray-500">서비스 계정 키가 없습니다. storage/app/google/service-account.json 배치 후 새로고침하세요.</span>'
                                );
                            }

                            return new \Illuminate\Support\HtmlString(
                                '<span class="text-sm text-gray-600">연결된 Google 시트 → <strong>공유</strong> → '
                                . '<code class="text-xs bg-gray-100 px-1 py-0.5 rounded">' . e($email) . '</code>'
                                . ' 를 <strong>뷰어</strong>로 추가해야 결과 보기/엑셀 다운로드가 됩니다.</span>'
                            );
                        })
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label('설명')
                        ->rows(2),

                    Toggle::make('is_active')
                        ->label('활성화')
                        ->default(true)
                        ->inline(false),
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
                    ->sortable(),

                TextColumn::make('sheet_id')
                    ->label('Sheet ID')
                    ->limit(30)
                    ->tooltip(fn ($state) => $state),

                TextColumn::make('description')
                    ->label('설명')
                    ->limit(40)
                    ->placeholder('—'),

                IconColumn::make('is_active')
                    ->label('활성화')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label('등록일')
                    ->date('Y.m.d')
                    ->sortable(),
            ])
            ->actions([
                Action::make('view_responses')
                    ->label('결과 보기')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn (GoogleForm $record): string => $record->title . ' — 응답 결과')
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('닫기')
                    ->modalContent(function (GoogleForm $record): \Illuminate\Contracts\View\View {
                        try {
                            $data = app(GoogleFormService::class)->getResponses($record->sheet_id);
                        } catch (\RuntimeException $e) {
                            $data = ['headers' => [], 'rows' => [], 'error' => $e->getMessage()];
                        }

                        return view('filament.modals.google-form-responses', [
                            'headers' => $data['headers'],
                            'rows'    => $data['rows'],
                            'error'   => $data['error'] ?? null,
                            'warning' => $data['warning'] ?? null,
                            'count'   => count($data['rows']),
                        ]);
                    }),

                Action::make('download_excel')
                    ->label('엑셀')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (GoogleForm $record) {
                        try {
                            $data = app(GoogleFormService::class)->getResponses($record->sheet_id);
                        } catch (\RuntimeException $e) {
                            Notification::make()
                                ->danger()
                                ->title('다운로드 실패')
                                ->body($e->getMessage())
                                ->send();
                            return;
                        }

                        if (empty($data['headers'])) {
                            Notification::make()->warning()->title('응답 데이터가 없습니다.')->send();
                            return;
                        }

                        if (! empty($data['warning'])) {
                            Notification::make()
                                ->warning()
                                ->title('공개 CSV로 다운로드')
                                ->body($data['warning'])
                                ->send();
                        }

                        $columns = [];
                        foreach ($data['headers'] as $i => $header) {
                            $idx = $i;
                            $columns[$header] = fn ($row) => $row[$idx] ?? '';
                        }

                        $exporter = new GenericExport(
                            collect($data['rows']),
                            $columns,
                            $record->title,
                        );

                        return Excel::download(
                            $exporter,
                            BaseExport::filename($record->title)
                        );
                    }),

                EditAction::make()->label('수정'),
                DeleteAction::make()->label('삭제'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGoogleForms::route('/'),
        ];
    }
}
