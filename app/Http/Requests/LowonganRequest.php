<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\EmploymentType;
use App\Enums\LowonganStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LowonganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $uniqueSlug = Rule::unique('lowongan', 'slug');

        if ($this->route('lowongan')) {
            $uniqueSlug->ignore($this->route('lowongan'));
        }

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $uniqueSlug],
            'description' => ['required', 'string', 'max:65535'],
            'requirements' => ['required', 'string', 'max:65535'],
            'responsibilities' => ['nullable', 'string', 'max:65535'],
            'department' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['required', Rule::enum(EmploymentType::class)],
            'status' => ['required', Rule::enum(LowonganStatus::class)],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
            'is_active' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul lowongan wajib diisi.',
            'description.required' => 'Deskripsi lowongan wajib diisi.',
            'requirements.required' => 'Persyaratan wajib diisi.',
            'employment_type.required' => 'Jenis pekerjaan wajib dipilih.',
            'status.required' => 'Status lowongan wajib dipilih.',
            'deadline.after_or_equal' => 'Batas lamaran harus sama atau setelah hari ini.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'display_order' => $this->display_order ?? 0,
            'location' => $this->location ?? 'Surabaya',
        ]);
    }
}
