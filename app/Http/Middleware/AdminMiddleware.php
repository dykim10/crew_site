<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next, string $minRole = 'operator'): Response
    {
        $user = $request->user();

        if (!$user || !$this->hasRole($user->role, $minRole)) {
            abort(403, '접근 권한이 없습니다.');
        }

        return $next($request);
    }

    private function hasRole(?string $userRole, string $minRole): bool
    {
        $hierarchy = ['operator' => 1, 'region_admin' => 2, 'super_admin' => 3];

        $userLevel = $hierarchy[$userRole] ?? 0;
        $minLevel  = $hierarchy[$minRole] ?? 99;

        return $userLevel >= $minLevel;
    }
}
