<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\PejabatLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PejabatRequest extends FormRequest
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
        $isLinkedToSigap = ! empty($this->pegawai_id);

        return [
            'pegawai_id' => 'nullable|string|max:36',
            'nama' => $isLinkedToSigap ? 'nullable|string|max:255' : 'required|string|max:255',
            'jabatan' => $isLinkedToSigap ? 'nullable|string|max:255' : 'required|string|max:255',
            'level' => $isLinkedToSigap
                ? ['nullable', Rule::enum(PejabatLevel::class)]
                : ['required', Rule::enum(PejabatLevel::class)],
            'bidang' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama pejabat harus diisi (jika tidak memilih dari data pegawai)',
            'nama.string' => 'Nama harus berupa teks',
            'nama.max' => 'Nama maksimal 255 karakter',
            'jabatan.required' => 'Jabatan harus diisi (jika tidak memilih dari data pegawai)',
            'jabatan.string' => 'Jabatan harus berupa teks',
            'jabatan.max' => 'Jabatan maksimal 255 karakter',
            'level.required' => 'Level jabatan harus dipilih (jika tidak memilih dari data pegawai)',
            'level.Illuminate\Validation\Rules\Enum' => 'Level jabatan tidak valid',
            'bidang.string' => 'Bidang harus berupa teks',
            'bidang.max' => 'Bidang maksimal 255 karakter',
            'deskripsi.string' => 'Deskripsi harus berupa teks',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter',
            'display_order.integer' => 'Urutan tampil harus berupa angka',
            'display_order.min' => 'Urutan tampil minimal 0',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format foto harus jpeg, png, jpg, gif, atau webp',
            'foto.max' => 'Ukuran foto maksimal 2MB',
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
