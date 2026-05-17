<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    // 유효한 초대 코드 목록 (추후 DB 관리로 전환)
    private const VALID_INVITE_CODES = ['CREW2026', 'PACRUN', 'RUNNING'];

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'nickname'    => ['required', 'string', 'max:50'],
            'email'       => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'    => ['required', 'confirmed', Rules\Password::defaults()],
            'invite_code' => ['required', 'string'],
        ], [
            'name.required'        => '이름을 입력해주세요.',
            'nickname.required'    => '닉네임을 입력해주세요.',
            'email.required'       => '이메일을 입력해주세요.',
            'email.unique'         => '이미 등록된 이메일입니다.',
            'password.required'    => '비밀번호를 입력해주세요.',
            'invite_code.required' => '초대 코드를 입력해주세요.',
        ]);

        if (!in_array(strtoupper($request->invite_code), self::VALID_INVITE_CODES)) {
            return back()->withErrors(['invite_code' => '유효하지 않은 초대 코드입니다.'])->withInput();
        }

        $user = User::create([
            'name'        => $request->name,
            'nickname'    => $request->nickname,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'invite_code' => strtoupper($request->invite_code),
            'role'        => 'member',
            'is_beta'     => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
