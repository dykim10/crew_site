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

/**
 * 회원가입 컨트롤러 (Auth/RegisteredUserController.php)
 *
 * 초대 코드 기반 클로즈 베타 회원가입을 처리한다.
 *
 * create() GET  /register → 회원가입 폼 표시
 * store()  POST /register → 아래 순서로 처리
 *   1. 입력값 검증 (이름·닉네임·이메일·비밀번호·초대 코드)
 *   2. 초대 코드 확인 (CREW2026 / PACRUN / RUNNING 중 하나)
 *   3. 이메일 중복 확인 — email_hash(SHA-256) 로 조회하여 평문 이메일을 DB에 저장하지 않음
 *   4. CryptoService 를 통해 이메일·이름 AES 암호화 → CORE API POST /api/crypto/encrypt
 *   5. User 생성 (role=member, is_beta=true)
 *   6. Registered 이벤트 발생 → 이메일 인증 메일 자동 발송
 *   7. 자동 로그인 후 /dashboard 리다이렉트
 */
class RegisteredUserController extends Controller
{
    private const VALID_INVITE_CODES = ['CREW2026', 'PACRUN', 'RUNNING'];

    public function __construct(private CryptoService $crypto) {}

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'nickname'    => ['required', 'string', 'max:50'],
            'email'       => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $hash = hash('sha256', strtolower(trim($value)));
                    if (User::where('email_hash', $hash)->exists()) {
                        $fail('이미 등록된 이메일입니다.');
                    }
                },
            ],
            'password'    => ['required', 'confirmed', Rules\Password::defaults()],
            'invite_code' => ['required', 'string'],
        ], [
            'name.required'        => '이름을 입력해주세요.',
            'nickname.required'    => '닉네임을 입력해주세요.',
            'email.required'       => '이메일을 입력해주세요.',
            'password.required'    => '비밀번호를 입력해주세요.',
            'invite_code.required' => '초대 코드를 입력해주세요.',
        ]);

        if (!in_array(strtoupper($request->invite_code), self::VALID_INVITE_CODES)) {
            return back()->withErrors(['invite_code' => '유효하지 않은 초대 코드입니다.'])->withInput();
        }

        $user = User::create([
            'nickname'    => $request->nickname,
            'email_hash'  => $this->crypto->hashEmail($request->email),
            'email_enc'   => $this->crypto->encrypt($request->email),
            'name_enc'    => $this->crypto->encrypt($request->name),
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
