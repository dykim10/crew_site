<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;

class ThemeSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?string $navigationLabel = '메인 테마 설정';
    protected static ?string $title = '메인 페이지 테마 설정';
    protected static ?int $navigationSort = 99;
    protected string $view = 'filament.pages.theme-settings';

    public string $activeTheme = 'v1';

    public static function getNavigationGroup(): ?string
    {
        return '시스템';
    }

    public static function canAccess(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['super_admin', 'region_admin']);
    }

    public function mount(): void
    {
        $this->activeTheme = Setting::get('active_theme', 'v1');
    }

    public function switchTheme(string $theme): void
    {
        abort_unless(in_array($theme, ['v1', 'v2']), 422);

        Setting::set('active_theme', $theme);
        $this->activeTheme = $theme;

        Notification::make()
            ->title("테마가 {$theme}로 변경되었습니다.")
            ->success()
            ->send();
    }
}
