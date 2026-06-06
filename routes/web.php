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
use App\Http\Controllers\BoardCommentController;
use App\Http\Controllers\BugReportController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventBoardController;
use App\Http\Controllers\EventFixedSubmissionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\PhotoGalleryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\RunningLogController;
use App\Http\Controllers\Admin\EventGroupController;
use App\Http\Controllers\AdminPasswordConfirmController;
use App\Http\Controllers\PlanningFeedbackController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\IntroduceController;
use App\Http\Controllers\SkinController;
use App\Http\Controllers\SmsWebhookController;
use Illuminate\Support\Facades\Route;

// 메인 페이지
Route::get('/', [HomeController::class, 'index'])->name('home');

// PAC 소개 (공개)
Route::get('/introduce', [IntroduceController::class, 'index'])->name('introduce');

// 지부 소개 (공개)
Route::get('/branch', [BranchController::class, 'index'])->name('branch');

// 게시판 (자유·포토·문의) — 목록/상세는 auth, 작성/수정/삭제도 auth
Route::middleware(['auth', 'verified'])->prefix('boards')->name('boards.')->group(function () {
    Route::get('/{type}',            [BoardController::class, 'index'])->name('index');
    Route::get('/{type}/create',     [BoardController::class, 'create'])->name('create');
    Route::post('/{type}',           [BoardController::class, 'store'])->name('store');
    Route::get('/{type}/{board}',    [BoardController::class, 'show'])->name('show');
    Route::get('/{type}/{board}/edit',   [BoardController::class, 'edit'])->name('edit');
    Route::put('/{type}/{board}',        [BoardController::class, 'update'])->name('update');
    Route::delete('/{type}/{board}',     [BoardController::class, 'destroy'])->name('destroy');
});

// boards.free / boards.photo / boards.qna 단축 별칭 (GNB 드롭다운용)
Route::get('/boards/free',  fn() => redirect()->route('boards.index', 'free'))->name('boards.free');
Route::get('/boards/photo', fn() => redirect()->route('boards.index', 'photo'))->name('boards.photo');
Route::get('/boards/qna',   fn() => redirect()->route('boards.index', 'qna'))->name('boards.qna');

// 테마 전환 (super_admin / region_admin 전용)
Route::post('/theme/switch', [HomeController::class, 'switchTheme'])
    ->middleware(['auth'])
    ->name('theme.switch');

// 프로젝트 가이드 (공개)
Route::get('/preview', function () {
    return response(file_get_contents(public_path('preview.html')))->header('Content-Type', 'text/html; charset=utf-8');
})->name('preview');

// 메인 페이지 디자인 시안 (공개)
Route::get('/design/v1', function () {
    return response(file_get_contents(public_path('design/v1.html')))->header('Content-Type', 'text/html; charset=utf-8');
})->name('design.v1');
Route::get('/design/v2', function () {
    return response(file_get_contents(public_path('design/v2.html')))->header('Content-Type', 'text/html; charset=utf-8');
})->name('design.v2');
Route::get('/design/v3', function () {
    return response(file_get_contents(public_path('design/v3.html')))->header('Content-Type', 'text/html; charset=utf-8');
})->name('design.v3');

// 이벤트 A타입 기획초안 + 피드백 설문 (공개)
Route::get('/testevent_a_type', [PlanningFeedbackController::class, 'showEventAType'])->name('testevent_a_type');
Route::post('/testevent_a_type/feedback', [PlanningFeedbackController::class, 'storeEventAType'])->name('testevent_a_type.store');

// 이벤트 목록·상세 (공개 — 비로그인 접근 가능)
Route::get('/events',        [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// 기수 신청서 (공개 — 인증 불필요)
Route::get('/apply', [ApplyController::class, 'index'])->name('apply');
Route::post('/apply', [ApplyController::class, 'store'])->name('apply.store');
Route::get('/apply/done', [ApplyController::class, 'done'])->name('apply.done');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // 스킨 변경 (사용자별 per-user)
    Route::post('/skin/change', [SkinController::class, 'change'])->name('skin.change');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 러닝 기록
    Route::post('/running-logs/parse-image', [RunningLogController::class, 'parseImage'])->name('running-logs.parse-image');
    Route::post('/running-logs/batch-confirm', [RunningLogController::class, 'batchConfirm'])->name('running-logs.batch-confirm');
    Route::post('/running-logs/{runningLog}/confirm', [RunningLogController::class, 'confirm'])->name('running-logs.confirm');
    Route::resource('running-logs', RunningLogController::class);

    // 버그 제보 (제출 전용 — 목록·상세 없음)
    Route::get('/bug-reports',  [BugReportController::class, 'create'])->name('bug-reports.create');
    Route::post('/bug-reports', [BugReportController::class, 'store'])->name('bug-reports.store');

    // 자유게시판 댓글 (2depth 지원)
    Route::prefix('boards/free/{board}/comments')->name('boards.free.comments.')->group(function () {
        Route::post('/',            [BoardCommentController::class, 'store'])->name('store');
        Route::put('/{comment}',    [BoardCommentController::class, 'update'])->name('update');
        Route::delete('/{comment}', [BoardCommentController::class, 'destroy'])->name('destroy');
    });

    // 문의게시판 댓글 (2depth 지원)
    Route::prefix('boards/qna/{board}/comments')->name('boards.qna.comments.')->group(function () {
        Route::post('/',            [BoardCommentController::class, 'store'])->name('store');
        Route::put('/{comment}',    [BoardCommentController::class, 'update'])->name('update');
        Route::delete('/{comment}', [BoardCommentController::class, 'destroy'])->name('destroy');
    });

    // 이벤트 (신청·취소는 로그인 필요)
    Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');
    Route::post('/events/{event}/cancel',   [EventController::class, 'cancel'])->name('events.cancel');

    // A타입 이벤트 현황 보드 (로그인 필요)
    Route::get('/events/{event}/board', [EventBoardController::class, 'index'])->name('events.board');
    Route::get('/events/{event}/board/group-data', [EventBoardController::class, 'groupData'])->name('events.board.group-data');
    Route::get('/events/{event}/board/member-logs', [EventBoardController::class, 'memberLogs'])->name('events.board.member-logs');

    // 고정점수 이벤트 제출물 (로그인 필요)
    Route::post('/events/{event}/submit-fixed', [EventFixedSubmissionController::class, 'store'])->name('events.submit-fixed');
    Route::get('/events/{event}/my-submissions', [EventFixedSubmissionController::class, 'mySubmissions'])->name('events.my-submissions');

    // 공지사항
    Route::get('/notices',      [NoticeController::class, 'index'])->name('notices.index');
    Route::get('/notices/{notice}', [NoticeController::class, 'show'])->name('notices.show');

    // 순위
    Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');

    // 포토 갤러리
    Route::get('/photos', [PhotoGalleryController::class, 'index'])->name('photos.index');
    Route::get('/photos/{photoGallery}', [PhotoGalleryController::class, 'show'])->name('photos.show');
});


// A타입 이벤트 조 편성 (관리자 전용)
Route::middleware(['auth', 'verified'])->prefix('admin/events')->name('admin.events.')->group(function () {
    Route::get('/{event}/groups', [EventGroupController::class, 'index'])->name('groups.index');
    Route::get('/{event}/groups/data', [EventGroupController::class, 'data'])->name('groups.data');
    Route::post('/{event}/groups/save', [EventGroupController::class, 'save'])->name('groups.save');
    Route::get('/{event}/groups/excel', [EventGroupController::class, 'downloadExcel'])->name('groups.excel');
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
