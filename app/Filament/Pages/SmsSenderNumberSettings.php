<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Http;

class SmsSenderNumberSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-phone-arrow-up-right';
    protected static ?string $navigationLabel = '발신번호 관리';
    protected static ?string $title = '발신번호 관리';
    protected static ?string $navigationParentItem = '예약 문자';
    protected static ?int $navigationSort = 4;
    protected string $view = 'filament.pages.sms-sender-number-settings';

    public ?array $data = [];

    /** @var array<int, array{phoneNumber: string, status: string}> */
    public array $senders = [];

    public static function getNavigationGroup(): ?string
    {
        return '알림 / 설문';
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->role === 'super_admin';
    }

    public function mount(): void
    {
        $this->form->fill();
        $this->loadSenders();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('새 발신번호 등록')
                ->description('개인 휴대폰 번호만 문자 본인인증(SELF-CERT)으로 등록할 수 있습니다.')
                ->schema([
                    TextInput::make('register_phone')
                        ->label('휴대폰 번호')
                        ->tel()
                        ->placeholder('01012345678')
                        ->required()
                        ->columnSpanFull(),
                ])
                ->footerActions([
                    Action::make('requestCode')
                        ->label('인증코드 요청')
                        ->action('requestRegisterCode'),
                ])
                ->footerActionsAlignment(Alignment::Start),

            Section::make('인증코드 확인')
                ->schema([
                    TextInput::make('verify_phone')
                        ->label('휴대폰 번호')
                        ->tel()
                        ->placeholder('01012345678')
                        ->required()
                        ->columnSpanFull(),
                    TextInput::make('certification_code')
                        ->label('인증코드')
                        ->required()
                        ->maxLength(10)
                        ->columnSpanFull(),
                ])
                ->footerActions([
                    Action::make('verifyCode')
                        ->label('인증 확인')
                        ->action('verifyRegisterCode'),
                ])
                ->footerActionsAlignment(Alignment::Start),
        ])->statePath('data');
    }

    public function loadSenders(): void
    {
        $this->senders = [];

        try {
            $response = Http::timeout(10)->get(
                config('services.core_api.url') . '/api/sms/senders',
                ['all' => 1],
            );
            $payload = $response->json();
            if (!$response->successful() || is_string($payload['error'] ?? null)) {
                return;
            }

            $items = $payload['senders'] ?? [];
            $this->senders = collect($items)
                ->filter(fn ($item) => is_array($item) && filled($item['phoneNumber'] ?? null))
                ->map(fn (array $item) => [
                    'phoneNumber' => (string) $item['phoneNumber'],
                    'status'      => (string) ($item['status'] ?? '-'),
                ])
                ->values()
                ->all();
        } catch (\Throwable) {
            $this->senders = [];
        }
    }

    public function requestRegisterCode(): void
    {
        $phone = $this->normalizePhone((string) ($this->form->getState()['register_phone'] ?? ''));
        if ($phone === null) {
            Notification::make()->title('유효하지 않은 전화번호입니다.')->danger()->send();
            return;
        }

        try {
            $response = Http::timeout(20)->post(
                config('services.core_api.url') . '/api/sms/senders/register',
                ['phone_number' => $phone],
            );
            $payload = $response->json();

            if (!$response->successful() || is_string($payload['error'] ?? null)) {
                Notification::make()
                    ->title('인증코드 요청 실패')
                    ->body(is_string($payload['error'] ?? null) ? $payload['error'] : '알 수 없는 오류')
                    ->danger()
                    ->send();
                return;
            }

            $this->form->fill([
                'register_phone'     => $phone,
                'verify_phone'       => $phone,
                'certification_code' => $this->form->getState()['certification_code'] ?? null,
            ]);

            Notification::make()->title('인증코드가 발송되었습니다.')->success()->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('인증코드 요청 실패')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function verifyRegisterCode(): void
    {
        $state = $this->form->getState();
        $phone = $this->normalizePhone((string) ($state['verify_phone'] ?? ''));
        $code = trim((string) ($state['certification_code'] ?? ''));

        if ($phone === null) {
            Notification::make()->title('유효하지 않은 전화번호입니다.')->danger()->send();
            return;
        }

        if ($code === '') {
            Notification::make()->title('인증코드를 입력해주세요.')->danger()->send();
            return;
        }

        try {
            $response = Http::timeout(20)->post(
                config('services.core_api.url') . '/api/sms/senders/verify',
                [
                    'phone_number'       => $phone,
                    'certification_code' => $code,
                ],
            );
            $payload = $response->json();

            if (!$response->successful() || is_string($payload['error'] ?? null)) {
                Notification::make()
                    ->title('인증 확인 실패')
                    ->body(is_string($payload['error'] ?? null) ? $payload['error'] : '알 수 없는 오류')
                    ->danger()
                    ->send();
                return;
            }

            $this->form->fill([
                'register_phone'     => $this->form->getState()['register_phone'] ?? null,
                'verify_phone'       => $phone,
                'certification_code' => null,
            ]);
            $this->loadSenders();

            Notification::make()->title('발신번호가 등록되었습니다.')->success()->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('인증 확인 실패')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteSender(string $phoneNumber): void
    {
        $phone = $this->normalizePhone($phoneNumber);
        if ($phone === null) {
            Notification::make()->title('유효하지 않은 전화번호입니다.')->danger()->send();
            return;
        }

        try {
            $response = Http::timeout(20)->delete(
                config('services.core_api.url') . '/api/sms/senders/' . rawurlencode($phone),
            );
            $payload = $response->json();

            if (!$response->successful() || is_string($payload['error'] ?? null)) {
                Notification::make()
                    ->title('삭제 실패')
                    ->body(is_string($payload['error'] ?? null) ? $payload['error'] : '알 수 없는 오류')
                    ->danger()
                    ->send();
                return;
            }

            $this->loadSenders();
            Notification::make()->title('발신번호가 삭제되었습니다.')->success()->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('삭제 실패')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function normalizePhone(string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';
        if ($digits === '' || strlen($digits) < 8 || strlen($digits) > 12) {
            return null;
        }

        return $digits;
    }
}
