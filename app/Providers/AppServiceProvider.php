<?php

namespace App\Providers;

use App\Filament\Resources\MainHeroImageResource;
use App\Support\LiveDatabaseGuard;
use App\Models\PhotoGallery;
use App\Models\Setting;
use App\Models\User;
use App\Observers\PhotoGalleryObserver;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        LiveDatabaseGuard::boot('CREW');

        // Filament FileUpload: 크롭 전 원본 임시 업로드 (메인 배경 최대 20MB)
        config(['livewire.temporary_file_upload.rules' => ['required', 'file', 'max:' . MainHeroImageResource::MAX_UPLOAD_KB]]);

        Paginator::defaultView('pagination.custom');

        PhotoGallery::observe(PhotoGalleryObserver::class);

        DatePicker::configureUsing(fn (DatePicker $c) => $c->locale('ko'));
        DateTimePicker::configureUsing(
            fn (DateTimePicker $c) => $c->locale('ko')->timezone('Asia/Seoul'),
        );
        TextColumn::configureUsing(fn (TextColumn $c) => $c->timezone('Asia/Seoul'));
        TextEntry::configureUsing(fn (TextEntry $c) => $c->timezone('Asia/Seoul'));

        // 모든 뷰에 activeTheme 공유 (crew.settings 테이블 기반)
        // 테스트 환경(SQLite)에서는 crew 스키마 쿼리 불가 → 기본값 사용
        try {
            View::share('activeTheme', Setting::get('active_theme', 'v1'));
        } catch (\Throwable) {
            View::share('activeTheme', 'v1');
        }

        // 게시판 역할별 Gate
        Gate::define('manage-notice',  fn(User $user) => $user->canManageNotice());
        Gate::define('manage-photo',   fn(User $user) => $user->canManagePhoto());
        Gate::define('moderate-board', fn(User $user) => $user->isModerator());
    }
}
