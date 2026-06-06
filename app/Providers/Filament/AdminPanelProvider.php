<?php

namespace App\Providers\Filament;

use App\Http\Middleware\EnsureAdminPasswordConfirmed;
use App\Http\Middleware\FilamentAdminAuthenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationGroup;
use Filament\View\PanelsRenderHook;
use App\Filament\Widgets\StatsOverview;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('PAC-RUN CREW')
            ->colors([
                'primary' => Color::hex('#E5AD16'),
                'gray'    => Color::Zinc,
                'danger'  => Color::Red,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'info'    => Color::Blue,
            ])
            ->darkMode(false)
            ->navigationGroups([
                NavigationGroup::make('크루 관리'),
                NavigationGroup::make('이벤트 관리'),
                NavigationGroup::make('기수 모집'),
                NavigationGroup::make('게시판 관리'),
                NavigationGroup::make('알림 / 설문'),
                NavigationGroup::make('시스템'),
            ])
            // viteTheme 대신 renderHook으로 직접 주입 — Vite 빌드 의존성 제거
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('filament.admin-theme'),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                StatsOverview::class,
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                FilamentAdminAuthenticate::class,
                EnsureAdminPasswordConfirmed::class,
            ]);
    }
}
