<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * 비밀번호 변경 컨트롤러 (Auth/PasswordController.php)
 *
 * Laravel Breeze 자동 생성. 로그인 상태에서 현재 비밀번호를 알고 있을 때 사용한다.
 * (비밀번호를 잊어버린 경우는 PasswordResetLinkController → NewPasswordController 흐름)
 *
 * update() PUT /password → current_password 확인 후 새 비밀번호로 해시 저장
 *                           validateWithBag('updatePassword') 로 에러 bag 분리
 *                           → 프로필 수정 페이지의 비밀번호 섹션 에러와 구분하기 위함
 *                           성공: back() + status='password-updated' 플래시 메시지
 */
class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
