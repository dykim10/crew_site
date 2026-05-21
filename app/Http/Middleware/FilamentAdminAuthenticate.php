<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Model;

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

        // 기본 구현은 abort(403)으로 에러 페이지를 반환하지만,
        // member 권한 유저가 /admin 직접 접근 시 로그인 페이지로 보내도록 변경.
        // unauthenticated() → AuthenticationException → redirectTo() → /admin/login 흐름.
        if (! $canAccess) {
            $this->unauthenticated($request, $guards);
        }
    }
}
