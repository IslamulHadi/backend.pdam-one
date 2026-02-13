<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'no_telp' => ['required', 'string', 'max:20'],
            'avatar' => ['nullable', 'mimes:png,jpg,jpeg', 'image', 'max:2048'],
        ];
    }
}
