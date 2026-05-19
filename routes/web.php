<?php

use App\Http\Controllers\Admin\AdminApplicationController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\AdminGenerationController;
use App\Http\Controllers\Admin\AdminMemberController;
use App\Http\Controllers\Admin\AdminNoticeController;
use App\Http\Controllers\Admin\AdminSmsController;
use App\Http\Controllers\Admin\AdminStatsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RunningLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 러닝 기록
    Route::post('/running-logs/parse-image', [RunningLogController::class, 'parseImage'])->name('running-logs.parse-image');
    Route::post('/running-logs/{runningLog}/confirm', [RunningLogController::class, 'confirm'])->name('running-logs.confirm');
    Route::resource('running-logs', RunningLogController::class);
});

// 관리자 라우트
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',               [AdminDashboardController::class, 'index'])->name('dashboard');

    // 회원
    Route::get('/members',        [AdminMemberController::class, 'index'])->name('members.index');
    Route::get('/applications',   [AdminApplicationController::class, 'index'])->name('applications.index');

    // 기수 (슈퍼 어드민 전용)
    Route::middleware('admin:super_admin')->group(function () {
        Route::get('/generations', [AdminGenerationController::class, 'index'])->name('generations.index');
    });

    // 이벤트
    Route::get('/events',         [AdminEventController::class, 'index'])->name('events.index');

    // 공지사항
    Route::get('/notices',        [AdminNoticeController::class, 'index'])->name('notices.index');
    Route::get('/notices/create', [AdminNoticeController::class, 'create'])->name('notices.create');
    Route::post('/notices',       [AdminNoticeController::class, 'store'])->name('notices.store');
    Route::delete('/notices/{notice}', [AdminNoticeController::class, 'destroy'])->name('notices.destroy');

    // 소통 / 통계 (region_admin 이상)
    Route::middleware('admin:region_admin')->group(function () {
        Route::get('/sms',         [AdminSmsController::class, 'index'])->name('sms.index');
        Route::get('/stats',       [AdminStatsController::class, 'index'])->name('stats.index');
        Route::get('/export',      [AdminStatsController::class, 'export'])->name('export.index');
    });
});

require __DIR__ . '/auth.php';
