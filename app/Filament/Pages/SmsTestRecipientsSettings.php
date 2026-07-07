<?php

namespace App\Filament\Pages;

use App\Models\SmsTestRecipient;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class SmsTestRecipientsSettings extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-device-phone-mobile';
    protected static ?string $navigationLabel = 'SMS 테스트 수신자';
    protected static ?string $title = 'SMS 테스트 수신자';
    protected static ?string $navigationParentItem = '예약 문자';
    protected static ?int $navigationSort = 3;
    protected string $view = 'filament.pages.sms-test-recipients-settings';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return '알림 / 설문';
    }

    public static function canAccess(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['super_admin', 'region_admin']);
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('테스트 수신 관리자 추가')
                ->description('예약 10분 전 [테스트] 접두어 문자를 받을 관리자를 등록합니다.')
                ->schema([
                    Select::make('user_id')
                        ->label('관리자')
                        ->options(fn () => User::whereIn('role', ['super_admin', 'region_admin', 'operator'])
                            ->orderBy('nickname')
                            ->pluck('nickname', 'id')
                            ->all())
                        ->searchable()
                        ->required()
                        ->columnSpanFull(),
                ])
                ->footerActions([
                    Action::make('add')
                        ->label('추가')
                        ->action('addRecipient'),
                ])
                ->footerActionsAlignment(Alignment::Start),
        ])->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(SmsTestRecipient::query()->with('user'))
            ->heading('등록된 수신자')
            ->description(fn () => $this->activeRecipientCount() . '명 활성')
            ->columns([
                TextColumn::make('user.nickname')
                    ->label('닉네임')
                    ->placeholder('-'),
                TextColumn::make('user.role')
                    ->label('권한')
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'super_admin'  => '슈퍼관리자',
                        'region_admin' => '지역관리자',
                        'operator'     => '운영자',
                        default        => $state ?? '-',
                    })
                    ->badge()
                    ->color('gray'),
                IconColumn::make('is_active')
                    ->label('활성')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('갱신')
                    ->dateTime('Y.m.d H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('toggle')
                    ->label(fn (SmsTestRecipient $record) => $record->is_active ? '비활성' : '활성')
                    ->icon(fn (SmsTestRecipient $record) => $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color('gray')
                    ->action(fn (SmsTestRecipient $record) => $this->toggleRecipient($record->id)),
                Action::make('delete')
                    ->label('삭제')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (SmsTestRecipient $record) => $this->removeRecipient($record->id)),
            ])
            ->paginated(false)
            ->emptyStateHeading('등록된 테스트 수신자가 없습니다')
            ->emptyStateDescription('위에서 관리자를 추가하면 예약 문자 발송 10분 전에 테스트 문자를 받습니다.');
    }

    public function activeRecipientCount(): int
    {
        return SmsTestRecipient::where('is_active', true)->count();
    }

    public function addRecipient(): void
    {
        $userId = (int) ($this->form->getState()['user_id'] ?? 0);
        $user = User::whereIn('role', ['super_admin', 'region_admin', 'operator'])->find($userId);

        if (!$user) {
            Notification::make()->title('관리자 회원만 추가할 수 있습니다.')->danger()->send();
            return;
        }

        SmsTestRecipient::updateOrCreate(['user_id' => $userId], ['is_active' => true]);
        $this->form->fill();
        $this->resetTable();

        Notification::make()->title('테스트 수신자가 추가되었습니다.')->success()->send();
    }

    public function removeRecipient(int $id): void
    {
        SmsTestRecipient::whereKey($id)->delete();
        $this->resetTable();
        Notification::make()->title('삭제되었습니다.')->success()->send();
    }

    public function toggleRecipient(int $id): void
    {
        $row = SmsTestRecipient::find($id);
        if (!$row) {
            return;
        }

        $row->update(['is_active' => !$row->is_active]);
        $this->resetTable();
        Notification::make()->title('상태가 변경되었습니다.')->success()->send();
    }
}
