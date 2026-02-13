<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\GangguanStatus;
use App\Enums\SeverityLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GangguanAirRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:65535',
            'affected_areas' => 'nullable|array',
            'affected_areas.*' => 'string|max:255',
            'severity' => ['required', Rule::enum(SeverityLevel::class)],
            'status' => ['required', Rule::enum(GangguanStatus::class)],
            'start_datetime' => 'required|date',
            'estimated_end_datetime' => 'nullable|date|after_or_equal:start_datetime',
            'actual_end_datetime' => 'nullable|date|after_or_equal:start_datetime',
            'resolution_notes' => 'nullable|string|max:65535',
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
            'description.required' => 'Deskripsi harus diisi',
            'severity.required' => 'Tingkat keparahan harus dipilih',
            'severity.Illuminate\Validation\Rules\Enum' => 'Tingkat keparahan tidak valid',
            'status.required' => 'Status harus dipilih',
            'status.Illuminate\Validation\Rules\Enum' => 'Status tidak valid',
            'start_datetime.required' => 'Waktu mulai harus diisi',
            'start_datetime.date' => 'Format waktu mulai tidak valid',
            'estimated_end_datetime.after_or_equal' => 'Perkiraan waktu selesai harus setelah waktu mulai',
            'actual_end_datetime.after_or_equal' => 'Waktu selesai aktual harus setelah waktu mulai',
        ];
    }
}
