<?php

use App\Http\Controllers\ApplyController;
use App\Http\Controllers\BugReportController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PhotoGalleryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RunningLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// 기수 신청서 (공개 — 인증 불필요)
Route::get('/apply', [ApplyController::class, 'index'])->name('apply');
Route::post('/apply', [ApplyController::class, 'store'])->name('apply.store');
Route::get('/apply/done', [ApplyController::class, 'done'])->name('apply.done');

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

    // 버그 제보
    Route::resource('bug-reports', BugReportController::class)->only(['index', 'create', 'store', 'show']);

    // 이벤트 참가 신청 (B타입)
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');

    // 포토 갤러리
    Route::get('/photos', [PhotoGalleryController::class, 'index'])->name('photos.index');
    Route::get('/photos/{photoGallery}', [PhotoGalleryController::class, 'show'])->name('photos.show');
});


require __DIR__ . '/auth.php';
