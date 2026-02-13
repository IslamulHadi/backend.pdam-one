<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\FaqCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FaqRequest extends FormRequest
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
        return [
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:65535',
            'category' => ['required', Rule::enum(FaqCategory::class)],
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'question.required' => 'Pertanyaan harus diisi',
            'question.string' => 'Pertanyaan harus berupa string',
            'question.max' => 'Pertanyaan maksimal 500 karakter',
            'answer.required' => 'Jawaban harus diisi',
            'answer.string' => 'Jawaban harus berupa string',
            'category.required' => 'Kategori harus dipilih',
            'category.Illuminate\Validation\Rules\Enum' => 'Kategori tidak valid',
            'display_order.integer' => 'Urutan tampil harus berupa angka',
            'display_order.min' => 'Urutan tampil minimal 0',
            'is_active.boolean' => 'Status aktif harus berupa boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->is_active ? true : false,
            'display_order' => $this->display_order ?? 0,
        ]);
    }
}
