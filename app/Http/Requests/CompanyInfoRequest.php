<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\CompanyInfoKey;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class CompanyInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $companyInfoId = $this->route('company_info');

        return [
            'key' => [
                'required',
                'string',
                new Enum(CompanyInfoKey::class),
                Rule::unique('info_perusahaan', 'key')->ignore($companyInfoId),
            ],
            'value' => 'required|string|max:65535',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'key.required' => 'Key harus dipilih',
            'key.string' => 'Key harus berupa string',
            'key.Illuminate\Validation\Rules\Enum' => 'Key yang dipilih tidak valid',
            'key.unique' => 'Key sudah digunakan',
            'value.required' => 'Value harus diisi',
            'value.string' => 'Value harus berupa string',
        ];
    }
}
