<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // DB 에 email 컬럼이 없으므로 email_hash 로 사용자를 찾는다.
        $emailHash = hash('sha256', strtolower(trim($request->email)));
        $user = User::where('email_hash', $emailHash)->first();

        // 계정 존재 여부를 노출하지 않기 위해 항상 성공 응답을 반환한다.
        if (!$user) {
            return back()->with('status', __('passwords.sent'));
        }

        try {
            // 토큰을 email_hash 키로 생성하여 password_reset_tokens 에 저장
            $token = Password::broker()->getRepository()->create($user);

            // 복호화된 실제 이메일로 재설정 링크 발송
            $user->sendPasswordResetNotification($token);
        } catch (\Exception $e) {
            Log::error('비밀번호 재설정 이메일 발송 실패', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => '이메일 발송에 실패했습니다. 잠시 후 다시 시도해주세요.']);
        }

        return back()->with('status', __('passwords.sent'));
    }
}
