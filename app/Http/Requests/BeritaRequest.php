<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class BeritaRequest extends FormRequest
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
        $isUpdate = $this->route()->parameter('berita') !== null;

        return [
            'title' => 'required|string|max:255',
            'published_at' => 'required|date',
            'content' => 'required|string|max:65535',
            'thumbnail' => $isUpdate ? 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kategori_ids' => 'required|array|min:1',
            'kategori_ids.*' => 'exists:kategori,id',
            'is_featured' => 'required|boolean',
            'slug' => $isUpdate
                ? 'required|string|max:255|unique:berita,slug,'.$this->route()->parameter('berita')->id
                : 'required|string|max:255|unique:berita,slug',
        ];
    }
    // ... existing code ...

    public function messages(): array
    {
        return [
            'title.required' => 'Judul harus diisi',
            'title.string' => 'Judul harus berupa string',
            'title.max' => 'Judul maksimal 255 karakter',
            'published_at.required' => 'Tanggal harus diisi',
            'published_at.date' => 'Tanggal harus berupa tanggal',
            'content.required' => 'Isi harus diisi',
            'content.string' => 'Isi harus berupa string',
            'kategori_ids.required' => 'Kategori harus diisi',
            'kategori_ids.array' => 'Kategori harus berupa array',
            'kategori_ids.min' => 'Kategori harus memilih minimal 1',
            'kategori_ids.*.exists' => 'Kategori tidak valid',
            'thumbnail.required' => 'Thumbnail harus diisi',
            'thumbnail.image' => 'Thumbnail harus berupa gambar',
            'thumbnail.mimes' => 'Thumbnail harus berupa gambar (jpeg, png, jpg, gif)',
            'thumbnail.max' => 'Thumbnail maksimal 2MB',
            'slug.required' => 'Slug harus diisi',
            'slug.string' => 'Slug harus berupa string',
            'slug.max' => 'Slug maksimal 255 karakter',
            'slug.unique' => 'Slug sudah ada',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->title).'_'.time(),
            'is_featured' => $this->is_featured ? true : false,
        ]);
    }
}
