<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationForm;
use Illuminate\Http\Request;

class ApplicationService
{
    public function __construct(private CryptoService $crypto) {}

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
            'email'         => ['required', 'email', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'agree_privacy' => ['required', 'accepted'],
        ];

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

        return Application::create([
            'form_id'       => $form->id,
            'name_enc'      => $this->crypto->encrypt($request->name),
            'email_hash'    => $this->crypto->hashEmail($request->email),
            'email_enc'     => $this->crypto->encrypt($request->email),
            'phone_enc'     => $request->phone ? $this->crypto->encrypt($request->phone) : null,
            'field_values'  => $fieldValues,
            'agree_privacy' => true,
        ]);
    }

    public function decryptPii(Application $app): array
    {
        return [
            'name'  => $this->crypto->decrypt($app->name_enc) ?? '-',
            'email' => $this->crypto->decrypt($app->email_enc) ?? '-',
            'phone' => $app->phone_enc ? ($this->crypto->decrypt($app->phone_enc) ?? '-') : '-',
        ];
    }
}
