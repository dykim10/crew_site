<?php

namespace App\Http\Controllers;

use App\Models\ApplicationForm;
use App\Services\ApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplyController extends Controller
{
    public function __construct(private ApplicationService $service) {}

    public function index(): View
    {
        $form = ApplicationForm::getActive();
        return view('apply.index', compact('form'));
    }

    public function store(Request $request): RedirectResponse
    {
        $form = ApplicationForm::getActive();

        if (!$form || !$form->isOpen()) {
            return back()->with('error', '현재 모집 중인 기수 신청이 없습니다.');
        }

        $rules = $this->service->buildValidationRules($form);
        $rules['email'][] = function ($attribute, $value, $fail) use ($form) {
            if ($this->service->isDuplicateEmail($value, $form->id)) {
                $fail('이미 신청하신 이메일입니다.');
            }
        };

        $request->validate($rules, [
            'name.required'          => '이름을 입력해주세요.',
            'email.required'         => '이메일을 입력해주세요.',
            'email.email'            => '올바른 이메일 형식을 입력해주세요.',
            'agree_privacy.required' => '개인정보 수집 및 이용에 동의해주세요.',
            'agree_privacy.accepted' => '개인정보 수집 및 이용에 동의해주세요.',
        ]);

        $this->service->store($request, $form);

        return redirect()->route('apply.done');
    }

    public function done(): View
    {
        return view('apply.done');
    }
}
