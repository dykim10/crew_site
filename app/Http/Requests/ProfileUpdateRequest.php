<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nickname' => ['required', 'string', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'nickname.required' => '닉네임을 입력해주세요.',
            'nickname.max'      => '닉네임은 30자 이하여야 합니다.',
        ];
    }
}
