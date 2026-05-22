<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationResource\Pages;
use App\Models\Application;
use App\Models\ApplicationForm;
use App\Services\ApplicationService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = '신청 내역';
    protected static ?string $modelLabel = '신청서';
    protected static ?string $pluralModelLabel = '신청 내역';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return '기수 관리';
    }

    public static function canCreate(): bool { return false; }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role === 'super_admin';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([

            Section::make('신청자 정보')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('decrypted_name')
                        ->label('이름')
                        ->disabled()
                        ->dehydrated(false),

                    \Filament\Forms\Components\TextInput::make('decrypted_email')
                        ->label('이메일')
                        ->disabled()
                        ->dehydrated(false),

                    \Filament\Forms\Components\TextInput::make('decrypted_phone')
                        ->label('연락처')
                        ->disabled()
                        ->dehydrated(false),
                ]),

            Section::make('추가 응답')
                ->schema([
                    \Filament\Forms\Components\Textarea::make('field_values_display')
                        ->label('응답 내용')
                        ->disabled()
                        ->dehydrated(false)
                        ->rows(8),
                ]),

            Section::make('처리')
                ->schema([
                    Select::make('status')
                        ->label('상태')
                        ->options(Application::STATUS_LABELS)
                        ->required(),

                    Textarea::make('admin_memo')
                        ->label('관리자 메모')
                        ->rows(3),
                ])
                ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('form.title')
                    ->label('폼')
                    ->limit(20)
                    ->placeholder('-'),

                TextColumn::make('form.cohort')
                    ->label('기수')
                    ->placeholder('-'),

                TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved'   => 'success',
                        'rejected'   => 'danger',
                        'waitlisted' => 'warning',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Application::STATUS_LABELS[$state] ?? $state),

                TextColumn::make('created_at')
                    ->label('신청일')
                    ->dateTime('Y.m.d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('상태')
                    ->options(Application::STATUS_LABELS),

                SelectFilter::make('form_id')
                    ->label('폼')
                    ->relationship('form', 'title'),
            ])
            ->actions([
                Action::make('view_detail')
                    ->label('상세 보기')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading('신청서 상세')
                    ->modalContent(function (Application $record): \Illuminate\View\View {
                        $crypto = app(\App\Services\CryptoService::class);
                        $pii = [
                            'name'  => $crypto->decrypt($record->name_enc) ?? '-',
                            'email' => $crypto->decrypt($record->email_enc) ?? '-',
                            'phone' => $record->phone_enc ? ($crypto->decrypt($record->phone_enc) ?? '-') : '-',
                        ];

                        $fieldLines = [];
                        $formFields = $record->form?->form_fields ?? [];
                        foreach ($formFields as $field) {
                            $key   = $field['key'] ?? null;
                            $label = $field['data']['label'] ?? $key;
                            if (!$key) continue;
                            $value = $record->field_values[$key] ?? '-';
                            if (is_array($value)) $value = implode(', ', $value);
                            $fieldLines[] = ['label' => $label, 'value' => $value ?: '-'];
                        }

                        return view('filament.application-detail', compact('record', 'pii', 'fieldLines'));
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),

                EditAction::make()->label('처리'),
                DeleteAction::make()->label('삭제'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'edit'  => Pages\EditApplication::route('/{record}/edit'),
        ];
    }
}
