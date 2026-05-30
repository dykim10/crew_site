<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Model;

/**
 * Filament 관리자 인증 미들웨어 (app/Http/Middleware/FilamentAdminAuthenticate.php)
 *
 * Filament 기본 Authenticate 미들웨어를 확장한 커스텀 구현.
 *
 * [기본 구현과의 차이점]
 *   기본 구현 : 권한 없으면 abort(403) → 에러 페이지 표시
 *   이 구현   : 권한 없으면 unauthenticated() 호출 → /admin/login 으로 리다이렉트
 *              (member 역할 유저가 /admin 에 직접 접근했을 때 403 대신 로그인 화면으로 안내)
 *
 * [접근 허용 조건]
 *   User::canAccessPanel() 이 true 인 경우
 *   → role: super_admin / region_admin / operator
 *   로컬 환경(APP_ENV=local) 에서는 FilamentUser 미구현 유저도 접근 허용
 */
class FilamentAdminAuthenticate extends FilamentAuthenticate
{
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);

            return;
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        /** @var Model $user */
        $user = $guard->user();
        $panel = Filament::getCurrentOrDefaultPanel();

        $canAccess = $user instanceof FilamentUser
            ? $user->canAccessPanel($panel)
            : (config('app.env') === 'local');

        // 관리자 권한 없는 유저(member 등)는 전용 접근 불가 페이지로 안내
        if (! $canAccess) {
            abort(redirect(route('admin.forbidden')));
        }
    }
}
