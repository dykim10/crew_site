<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CryptoService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function __construct(private CryptoService $crypto) {}

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'nickname' => ['required', 'string', 'max:50'],
            'email'    => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $hash = hash('sha256', strtolower(trim($value)));
                    if (User::where('email_hash', $hash)->exists()) {
                        $fail('이미 등록된 이메일입니다.');
                    }
                },
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required'     => '이름을 입력해주세요.',
            'nickname.required' => '닉네임을 입력해주세요.',
            'email.required'    => '이메일을 입력해주세요.',
            'password.required' => '비밀번호를 입력해주세요.',
        ]);

        $user = User::create([
            'nickname'   => $request->nickname,
            'email_hash' => $this->crypto->hashEmail($request->email),
            'email_enc'  => $this->crypto->encrypt($request->email),
            'name_enc'   => $this->crypto->encrypt($request->name),
            'password'   => Hash::make($request->password),
            'role'       => 'member',
            'is_beta'    => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice')
            ->with('status', 'registered');
    }
}
