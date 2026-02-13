<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\PriorityLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PengumumanRequest extends FormRequest
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
        $pengumumanId = $this->route('pengumuman');

        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('pengumuman', 'slug')->ignore($pengumumanId),
            ],
            'content' => 'required|string|max:65535',
            'excerpt' => 'nullable|string|max:500',
            'priority' => ['required', Rule::enum(PriorityLevel::class)],
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul harus diisi',
            'title.max' => 'Judul maksimal 255 karakter',
            'slug.unique' => 'Slug sudah digunakan',
            'content.required' => 'Konten harus diisi',
            'priority.required' => 'Prioritas harus dipilih',
            'priority.Illuminate\Validation\Rules\Enum' => 'Prioritas tidak valid',
            'end_date.after_or_equal' => 'Tanggal berakhir harus setelah atau sama dengan tanggal mulai',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->is_active ? true : false,
        ]);
    }
}
