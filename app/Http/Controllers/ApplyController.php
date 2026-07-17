<?php

namespace App\Http\Controllers;

use App\Models\ApplicationForm;
use App\Models\Generation;
use App\Models\GoogleForm;
use App\Services\ApplicationService;
use App\Services\GenerationRecruitmentService;
use App\Services\GenerationVisibilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplyController extends Controller
{
    public function __construct(
        private ApplicationService $service,
        private GenerationRecruitmentService $recruitment,
        private GenerationVisibilityService $visibility,
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        $generation = $this->resolveGeneration($request);

        if ($generation instanceof RedirectResponse) {
            return $generation;
        }

        if (! $generation) {
            return view('apply.index', ['form' => null, 'generation' => null, 'googleForm' => null]);
        }

        // 모집 기간 외·비활성 채널이면 접수 차단 (구글 대체 포함)
        if (! $this->visibility->isRecruiting($generation)) {
            return view('apply.index', [
                'form'       => null,
                'closedForm' => $this->recruitment->resolveApplicationForm($generation),
                'generation' => $generation,
                'googleForm' => null,
            ]);
        }

        if ($generation->apply_method === 'google_form') {
            $googleForm = $generation->google_form_id
                ? GoogleForm::find($generation->google_form_id)
                : null;

            return view('apply.google-redirect', compact('generation', 'googleForm'));
        }

        $form = $this->recruitment->resolveApplicationForm($generation);
        if (! $form || ! $form->isOpen()) {
            return view('apply.index', [
                'form'       => null,
                'closedForm' => $form,
                'generation' => $generation,
                'googleForm' => null,
            ]);
        }

        $branchOptions = app(\App\Services\ApplicationFormBranchService::class)->selectableBranches($form);
        $formImages = app(\App\Services\ApplicationFormBranchService::class)->imageUrls($form);

        return view('apply.index', compact('form', 'generation', 'branchOptions', 'formImages'));
    }

    public function store(Request $request): RedirectResponse
    {
        $generation = null;
        if ($request->filled('generation')) {
            $generation = Generation::find((int) $request->input('generation'));
        }

        $form = $generation
            ? $this->recruitment->resolveApplicationForm($generation)
            : ApplicationForm::getActive();

        if (! $form || ! $form->isOpen()) {
            return back()->with('error', '현재 모집 중인 기수 신청이 없습니다.');
        }

        $branchService = app(\App\Services\ApplicationFormBranchService::class);
        $rules = $this->service->buildValidationRules($form);
        $rules['email'][] = function ($attribute, $value, $fail) use ($form) {
            $email = strtolower(trim((string) $value));
            if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $fail('올바른 이메일 형식을 입력해주세요.');

                return;
            }
            if ($this->service->isDuplicateEmail($email, $form->id)) {
                $fail('이미 신청하신 이메일입니다.');
            }
        };

        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
            'phone' => trim((string) $request->input('phone')),
        ]);

        $request->validate($rules, [
            'name.required'                   => '이름을 입력해주세요.',
            'email.required'                  => '이메일을 입력해주세요.',
            'email.email'                     => '올바른 이메일 형식을 입력해주세요.',
            'email.regex'                     => '올바른 이메일 형식을 입력해주세요. (예: name@example.com)',
            'phone.required'                  => '연락처를 입력해주세요.',
            'preferred_branch_id.required'    => '희망지부를 선택해주세요.',
            'agree_privacy.required'          => '개인정보 수집 및 이용에 동의해주세요.',
            'agree_privacy.accepted'          => '개인정보 수집 및 이용에 동의해주세요.',
        ]);

        if ($request->filled('preferred_branch_id')) {
            $error = $branchService->assertPreferredBranchAllowed($form, (int) $request->input('preferred_branch_id'));
            if ($error) {
                return back()->withInput()->withErrors(['preferred_branch_id' => $error]);
            }
        }

        $this->service->store($request, $form);

        return redirect()->route('apply.done');
    }

    public function done(): View
    {
        return view('apply.done');
    }

    private function resolveGeneration(Request $request): Generation|RedirectResponse|null
    {
        if ($request->filled('generation')) {
            $g = Generation::find((int) $request->query('generation'));
            if (! $g) {
                return redirect()->route('apply')->with('error', '존재하지 않는 기수입니다.');
            }

            return $g;
        }

        $open = $this->visibility->visibleGenerations()
            ->filter(fn (Generation $g) => $this->visibility->isRecruiting($g))
            ->values();

        // 직접 신청서가 실제로 열린 기수를 우선 (구글 대체와 섞여 /apply 가 비는 문제 방지)
        $internalOpen = $open->filter(fn (Generation $g) => $g->apply_method !== 'google_form'
            && $this->recruitment->isFormOpen($g)
        )->values();

        if ($internalOpen->count() === 1) {
            return $internalOpen->first();
        }

        if ($open->count() === 1) {
            return $open->first();
        }

        if ($open->count() > 1) {
            return redirect()->route('generation.show')
                ->with('error', '신청할 기수를 선택해주세요. (기수 카드의 신청 링크를 이용하세요)');
        }

        // 레거시: 활성 신청서 1개
        return null;
    }
}
