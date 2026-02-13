<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\LoketType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoketPembayaranRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::enum(LoketType::class)],
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'operational_hours' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'display_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama loket harus diisi',
            'name.max' => 'Nama loket maksimal 255 karakter',
            'type.required' => 'Tipe loket harus dipilih',
            'type.Illuminate\Validation\Rules\Enum' => 'Tipe loket tidak valid',
            'latitude.between' => 'Latitude harus antara -90 dan 90',
            'longitude.between' => 'Longitude harus antara -180 dan 180',
            'display_order.integer' => 'Urutan tampil harus berupa angka',
            'display_order.min' => 'Urutan tampil minimal 0',
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
