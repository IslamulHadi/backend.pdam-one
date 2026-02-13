<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\GalleryType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GalleryRequest extends FormRequest
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
            'description' => 'nullable|string',
            'type' => ['required', Rule::enum(GalleryType::class)],
            'video_url' => 'nullable|url|max:500',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];

        if ($this->isMethod('POST')) {
            $rules['images'] = 'required_if:type,foto|array';
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
            'title.string' => 'Judul harus berupa string',
            'title.max' => 'Judul maksimal 255 karakter',
            'type.required' => 'Tipe galeri harus dipilih',
            'type.Illuminate\Validation\Rules\Enum' => 'Tipe galeri tidak valid',
            'video_url.url' => 'URL video harus berupa URL yang valid',
            'video_url.max' => 'URL video maksimal 500 karakter',
            'display_order.integer' => 'Urutan tampil harus berupa angka',
            'display_order.min' => 'Urutan tampil minimal 0',
            'is_active.boolean' => 'Status aktif harus berupa boolean',
            'images.required_if' => 'Gambar harus diupload untuk galeri foto',
            'images.*.image' => 'File harus berupa gambar',
            'images.*.mimes' => 'Format gambar harus jpeg, png, jpg, gif, atau webp',
            'images.*.max' => 'Ukuran gambar maksimal 5MB',
            'thumbnail.image' => 'Thumbnail harus berupa gambar',
            'thumbnail.mimes' => 'Format thumbnail harus jpeg, png, jpg, gif, atau webp',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 2MB',
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
