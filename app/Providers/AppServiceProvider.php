<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::defaultView('pagination.custom');

        // 모든 뷰에 activeTheme 공유 (crew.settings 테이블 기반)
        View::share('activeTheme', Setting::get('active_theme', 'v1'));
    }
}
