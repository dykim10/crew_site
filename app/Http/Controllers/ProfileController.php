<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        $request->user()->update(['nickname' => $request->validated('nickname')]);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * 프로필 대표 이미지 업로드 → S3 저장 → users_detail.avatar_url 업데이트
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'avatar.required' => '이미지를 선택해주세요.',
            'avatar.image'    => '이미지 파일만 업로드 가능합니다.',
            'avatar.mimes'    => 'JPG, PNG, WEBP 파일만 가능합니다.',
            'avatar.max'      => '파일 크기는 5MB 이하여야 합니다.',
        ]);

        $user    = $request->user();
        $detail  = $user->detail;
        $file    = $request->file('avatar');
        $coreUrl = rtrim(config('services.core_api.url', 'http://localhost:8100'), '/');

        try {
            // 1. CORE API WebP 변환 시도 (avatars/{user_id} 경로로 저장)
            $url = null;
            try {
                $response = Http::timeout(30)
                    ->attach('file', $file->get(), $file->getClientOriginalName())
                    ->post("{$coreUrl}/api/photo/resize-webp", [
                        'folder' => 'avatars/' . $user->id,
                    ]);

                if ($response->successful()) {
                    $url = $response->json('thumbnail_url');
                }
            } catch (ConnectionException) {
                // CORE API 불통 시 원본 S3 저장으로 폴백
            }

            // 2. WebP 변환 실패 시 원본 S3 저장
            if (!$url) {
                $path = 'avatars/' . $user->id . '/' . Str::uuid() . '.' . $file->extension();
                Storage::disk('s3')->put($path, $file->get(), 'public');
                $url  = Storage::disk('s3')->url($path);
            }

            // 3. 기존 아바타 S3 삭제
            if ($detail?->avatar_url) {
                $oldKey = ltrim(parse_url($detail->avatar_url, PHP_URL_PATH), '/');
                try { Storage::disk('s3')->delete($oldKey); } catch (\Throwable) {}
            }

            // 4. DB 저장
            if ($detail) {
                $detail->update(['avatar_url' => $url]);
            } else {
                \App\Models\UsersDetail::create(['user_id' => $user->id, 'avatar_url' => $url]);
            }
        } catch (\Exception $e) {
            Log::error('아바타 업로드 실패', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['avatar' => '이미지 업로드에 실패했습니다. 다시 시도해주세요.']);
        }

        return Redirect::route('profile.edit')->with('status', 'avatar-updated');
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
