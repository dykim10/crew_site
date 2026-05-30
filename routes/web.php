<?php

/**
 * 웹 라우트 (routes/web.php)
 *
 * 브라우저 세션 기반 요청(웹 미들웨어 그룹)을 처리하는 메인 라우트 파일.
 * 인증 관련 라우트는 auth.php 로 분리되어 하단에서 require 로 포함된다.
 *
 * [공개 - 인증 불필요]
 *   GET       /            → /login 으로 리다이렉트
 *   GET/POST  /apply       → 기수 신청서 (클로즈 베타 가입 전 단계, 누구나 접근 가능)
 *   GET       /apply/done  → 신청 완료 안내
 *
 * [로그인 전용 - middleware: auth]
 *   GET             /dashboard                    → 대시보드 (기록 요약·이벤트·공지)
 *   GET/PATCH/DELETE /profile                     → 프로필 조회·수정·탈퇴
 *
 *   POST  /running-logs/parse-image               → 이미지 AJAX 파싱 (CORE API 호출, JSON 응답)
 *   POST  /running-logs/{id}/confirm              → 파싱 결과 사용자 최종 확정
 *   (Resource) /running-logs                      → 기록 CRUD 전체 (index/create/store/show/edit/update/destroy)
 *
 *   (Resource) /bug-reports                       → 버그 제보 (index/create/store/show 만 허용)
 *   GET   /events/{id}                            → 이벤트 상세
 *   POST  /events/{id}/register                   → 이벤트 신청 (B타입 폼 기반)
 *   GET   /photos                                 → 포토 갤러리 목록
 *   GET   /photos/{id}                            → 포토 갤러리 상세
 */

use App\Http\Controllers\ApplyController;
use App\Http\Controllers\BugReportController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\PhotoGalleryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\RunningLogController;
use App\Http\Controllers\AdminPasswordConfirmController;
use App\Http\Controllers\SmsWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// 프로젝트 가이드 (공개)
Route::get('/preview', function () {
    return response(file_get_contents(public_path('preview.html')))->header('Content-Type', 'text/html; charset=utf-8');
})->name('preview');

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
    Route::post('/running-logs/batch-confirm', [RunningLogController::class, 'batchConfirm'])->name('running-logs.batch-confirm');
    Route::post('/running-logs/{runningLog}/confirm', [RunningLogController::class, 'confirm'])->name('running-logs.confirm');
    Route::resource('running-logs', RunningLogController::class);

    // 버그 제보
    Route::resource('bug-reports', BugReportController::class)->only(['index', 'create', 'store', 'show']);

    // 이벤트
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');

    // 공지사항
    Route::get('/notices', [NoticeController::class, 'index'])->name('notices.index');

    // 순위
    Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');

    // 포토 갤러리
    Route::get('/photos', [PhotoGalleryController::class, 'index'])->name('photos.index');
    Route::get('/photos/{photoGallery}', [PhotoGalleryController::class, 'show'])->name('photos.show');
});


// 관리자 비밀번호 재확인 (로그인 상태 필요, CSRF 적용)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin-confirm', [AdminPasswordConfirmController::class, 'show'])->name('admin.password.confirm');
    Route::post('/admin-confirm', [AdminPasswordConfirmController::class, 'store'])->name('admin.password.confirm.store');
});

// 비 관리자 접근 불가 안내 (로그인 상태 필요)
Route::get('/admin-forbidden', fn () => view('admin.forbidden'))
    ->middleware(['auth'])
    ->name('admin.forbidden');

// Solapi 수신 결과 웹훅 (CSRF 제외는 bootstrap/app.php에서 처리)
Route::post('/webhooks/sms', [SmsWebhookController::class, 'handle'])->name('webhooks.sms');

require __DIR__ . '/auth.php';
