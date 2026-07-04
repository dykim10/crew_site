<?php

/**
 * 인증 라우트 (routes/auth.php)
 *
 * Laravel Breeze 가 자동 생성한 인증 관련 라우트 모음.
 * web.php 에서 require 로 포함되어 동작한다.
 *
 * [비로그인 전용 - middleware: guest]
 *   GET/POST  /register              → 회원가입 (이메일 인증 기반 — 가입 후 이메일 클릭으로 완료)
 *   GET/POST  /login                 → 로그인
 *   GET/POST  /forgot-password       → 비밀번호 재설정 링크 요청
 *   GET/POST  /reset-password/{token}→ 비밀번호 재설정
 *
 * [로그인 전용 - middleware: auth]
 *   GET       /verify-email          → 이메일 인증 안내 페이지
 *   GET       /verify-email/{id}/{hash} → 이메일 인증 처리 (서명 URL, 분당 6회 제한)
 *   POST      /email/verification-notification → 인증 메일 재발송 (분당 6회 제한)
 *   GET/POST  /confirm-password      → 민감 작업 전 비밀번호 재확인
 *   POST      /logout                → 로그아웃
 */

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
