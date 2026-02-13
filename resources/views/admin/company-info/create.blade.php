<x-layouts.auth title="Info Perusahaan">
    <div class="container-xxl flex-grow-1 container-p-y">
        <flux:breadcrumbs class="py-3 mb-4">
            <flux:breadcrumbs.item href="#">Info Perusahaan</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ request()->routeIs('admin.company-info.create') ? 'Tambah' : 'Ubah' }} Info Perusahaan</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <x-admin.card :title="(request()->routeIs('admin.company-info.create') ? 'Tambah' : 'Ubah') . ' Data Info Perusahaan'" :back-route="route('admin.company-info.index')">

                @php
                    $urlAction = request()->routeIs('admin.company-info.create')
                        ? route('admin.company-info.store')
                        : route('admin.company-info.update', isset($companyInfo) ? $companyInfo : null);
                    $methodForm = request()->routeIs('admin.company-info.create') ? 'POST' : 'PUT';
                @endphp

                <form id="needs-validation" novalidate method="post" data-parsley-validate action="{{ $urlAction }}">
                    @csrf
                    @method($methodForm)

                    <!-- Card Body -->
                    <div class="px-6 py-6">
                        <x-admin.form-errors />

                        <!-- Form Fields Grid -->
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Key -->
                            <div>
                                <label for="key" class="block text-sm font-medium text-gray-700 mb-2">
                                    Key <span class="text-red-500">*</span>
                                </label>
                                
                                <select
                                    class="select2 w-full @error('key') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                    id="key" name="key" data-parsley-required>
                                    <option value="">-- Pilih Key --</option>
                                    @foreach ($availableKeys as $group => $keys)
                                        <optgroup label="{{ ucfirst($group) }}">
                                            @foreach ($keys as $keyValue => $keyLabel)
                                                <option value="{{ $keyValue }}"
                                                    {{ old('key', isset($companyInfo) ? $companyInfo->key : '') == $keyValue ? 'selected' : '' }}>
                                                    {{ $keyLabel }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Group akan otomatis ditentukan berdasarkan key yang dipilih</p>
                                @error('key')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Value -->
                            <div>
                                <label for="value" class="block text-sm font-medium text-gray-700 mb-2">
                                    Value <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('value') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                    id="value" name="value" rows="4" data-parsley-required
                                    placeholder="Masukkan nilai">{{ old('value', isset($companyInfo) ? $companyInfo->value : '') }}</textarea>
                                @error('value')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Info Box -->
                        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Info</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>Key yang tersedia sudah ditentukan oleh sistem. Pilih key dari dropdown, lalu isi nilai yang sesuai. Group akan otomatis terisi berdasarkan key yang dipilih.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-custom text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                Simpan Data
                            </button>
                        </div>
                    </div>
                </form>
        </x-admin.card>
    </div>
    @push('css')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    width: '100%'
                });
            });
        </script>
    @endpush
</x-layouts.auth>
