<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationForm;
use Illuminate\Http\Request;

class ApplicationService
{
    public function __construct(
        private CryptoService $crypto,
        private ApplicationFormBranchService $branchService,
    ) {}

    public function isDuplicateEmail(string $email, int $formId): bool
    {
        $hash = $this->crypto->hashEmail($email);
        return Application::where('email_hash', $hash)
            ->where('form_id', $formId)
            ->exists();
    }

    public function buildValidationRules(ApplicationForm $form): array
    {
        $rules = [
            'name'          => ['required', 'string', 'max:100'],
            'email'         => [
                'required',
                'string',
                'max:255',
                'email:filter',
                'regex:/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/u',
            ],
            'phone'         => ['required', 'string', 'max:20'],
            'agree_privacy' => ['required', 'accepted'],
        ];

        $hasBranchSettings = $this->branchService->normalizedSettings($form) !== [];
        if ($hasBranchSettings) {
            $rules['preferred_branch_id'] = ['required', 'integer'];
        }

        foreach ($form->form_fields ?? [] as $field) {
            $key      = $field['key'] ?? null;
            $type     = $field['type'] ?? 'text';
            $required = $field['data']['required'] ?? false;

            if (!$key) continue;

            $rule = $required ? 'required' : 'nullable';
            $rules["field_{$key}"] = match ($type) {
                'checkbox' => $rule . '|array',
                'textarea' => $rule . '|string|max:3000',
                default    => $rule . '|string|max:500',
            };
        }

        return $rules;
    }

    public function store(Request $request, ApplicationForm $form): Application
    {
        $fieldValues = [];
        foreach ($form->form_fields ?? [] as $field) {
            $key  = $field['key'] ?? null;
            $type = $field['type'] ?? 'text';
            if (!$key) continue;

            $value = $request->input("field_{$key}");

            if ($type === 'checkbox') {
                $value = (array) $value;
            }

            $fieldValues[$key] = $value;
        }

        $preferredBranchId = $request->filled('preferred_branch_id')
            ? (int) $request->input('preferred_branch_id')
            : null;

        $email = strtolower(trim((string) $request->input('email')));

        $app = Application::create([
            'form_id'              => $form->id,
            'name_enc'             => $this->crypto->encrypt($request->name),
            'email_hash'           => $this->crypto->hashEmail($email),
            'email_enc'            => $this->crypto->encrypt($email),
            'phone_enc'            => $this->crypto->encrypt(trim((string) $request->phone)),
            'field_values'         => $fieldValues,
            'agree_privacy'        => true,
            'preferred_branch_id'  => $preferredBranchId,
        ]);

        app(ApplicationMatchingService::class)->matchApplication($app);

        return $app->fresh();
    }

    public function decryptPii(Application $app): array
    {
        return [
            'name'  => $this->crypto->decrypt($app->name_enc) ?? '-',
            'email' => $app->email_enc ? ($this->crypto->decrypt($app->email_enc) ?? '-') : '(없음)',
            'phone' => $app->phone_enc ? ($this->crypto->decrypt($app->phone_enc) ?? '-') : '-',
        ];
    }

    /** 목록용 — 010-****-5678 형태 */
    public function maskPhone(?string $phone): string
    {
        if ($phone === null || $phone === '' || $phone === '-') {
            return '-';
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if (strlen($digits) < 10) {
            return '****';
        }

        return substr($digits, 0, 3).'-****-'.substr($digits, -4);
    }

    /** preferred_branch_id 우선, 없으면 field_values 라벨 휴리스틱(레거시) */
    public function preferredBranch(Application $app): string
    {
        if ($app->preferred_branch_id) {
            return (string) ($app->preferredBranch?->name ?? '-');
        }

        $values = $app->field_values ?? [];
        if ($values === []) {
            return '-';
        }

        foreach ($app->form?->form_fields ?? [] as $field) {
            $key = (string) ($field['key'] ?? $field['data']['key'] ?? '');
            $label = (string) ($field['data']['label'] ?? $field['label'] ?? '');
            if ($key === '') {
                continue;
            }

            $normalized = mb_strtolower(preg_replace('/\s+/u', '', $label) ?? $label);
            if (
                str_contains($normalized, '희망지부')
                || str_contains($normalized, '지부선택')
                || in_array($key, ['preferred_branch', 'hope_branch', 'branch', 'field_0'], true)
            ) {
                $value = $values[$key] ?? null;
                if ($value === null || $value === '') {
                    return '-';
                }

                return is_array($value) ? implode(', ', $value) : (string) $value;
            }
        }

        return '-';
    }
}
