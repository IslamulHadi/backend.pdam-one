<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\DownloadCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DownloadRequest extends FormRequest
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
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => ['required', Rule::enum(DownloadCategory::class)],
            'is_active' => 'nullable|boolean',
            'display_order' => 'nullable|integer|min:0',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar|max:20480',
        ];

        if ($this->isMethod('POST')) {
            $rules['file'] = 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar|max:20480';
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul harus diisi',
            'title.max' => 'Judul maksimal 255 karakter',
            'description.max' => 'Deskripsi maksimal 1000 karakter',
            'category.required' => 'Kategori harus dipilih',
            'category.Illuminate\Validation\Rules\Enum' => 'Kategori tidak valid',
            'display_order.integer' => 'Urutan tampil harus berupa angka',
            'display_order.min' => 'Urutan tampil minimal 0',
            'file.required' => 'File harus diupload',
            'file.file' => 'File tidak valid',
            'file.mimes' => 'Format file harus pdf, doc, docx, xls, xlsx, ppt, pptx, zip, atau rar',
            'file.max' => 'Ukuran file maksimal 20MB',
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
