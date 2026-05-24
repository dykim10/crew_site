<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * 프로필 컨트롤러 (app/Http/Controllers/ProfileController.php)
 *
 * 로그인한 사용자 본인의 프로필 조회·수정·탈퇴를 처리한다.
 * Laravel Breeze 자동 생성 파일이며, 별도 Service 없이 User 모델을 직접 사용한다.
 *
 * 라우트:
 *   GET    /profile → edit()    프로필 수정 폼
 *   PATCH  /profile → update()  정보 저장 (이메일 변경 시 email_verified_at 초기화)
 *   DELETE /profile → destroy() 비밀번호 확인 후 계정 삭제 → 로그아웃 → / 리다이렉트
 */
class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
