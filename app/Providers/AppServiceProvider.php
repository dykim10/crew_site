<?php

namespace App\Providers;

use App\Models\Setting;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::defaultView('pagination.custom');

        DatePicker::configureUsing(fn (DatePicker $c) => $c->locale('ko'));
        DateTimePicker::configureUsing(fn (DateTimePicker $c) => $c->locale('ko'));

        // 모든 뷰에 activeTheme 공유 (crew.settings 테이블 기반)
        View::share('activeTheme', Setting::get('active_theme', 'v1'));
    }
}
