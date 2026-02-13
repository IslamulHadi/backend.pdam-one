<x-layouts.auth title="Gangguan Air">
    <div class="container-xxl flex-grow-1 container-p-y">
        <flux:breadcrumbs class="py-3 mb-4">
            <flux:breadcrumbs.item href="#">Info Gangguan</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ isset($gangguanAir) ? 'Edit' : 'Tambah' }} Gangguan Air</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <x-admin.card :title="(isset($gangguanAir) ? 'Edit' : 'Tambah') . ' Data Gangguan Air'" :back-route="route('admin.gangguan-air.index')">

                @php
                    $urlAction = isset($gangguanAir)
                        ? route('admin.gangguan-air.update', $gangguanAir->id)
                        : route('admin.gangguan-air.store');
                    $methodForm = isset($gangguanAir) ? 'PUT' : 'POST';
                @endphp

                <form id="needs-validation" novalidate method="post" data-parsley-validate
                    action="{{ $urlAction }}">
                    @csrf
                    @method($methodForm)

                    <!-- Card Body -->
                    <div class="px-6 py-6">
                        <x-admin.form-errors />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Judul -->
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Judul <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('title') border-red-500 @enderror"
                                    id="title" name="title" data-parsley-required
                                    value="{{ old('title', $gangguanAir->title ?? '') }}"
                                    placeholder="Contoh: Perbaikan Pipa di Jl. Ahmad Yani" />
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tingkat Keparahan -->
                            <div>
                                <label for="severity" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tingkat Keparahan <span class="text-red-500">*</span>
                                </label>
                                <select
                                    class="select2 w-full @error('severity') border-red-500 @enderror"
                                    id="severity" name="severity" data-parsley-required>
                                    <option value="">Pilih Tingkat</option>
                                    @foreach ($severities as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('severity', $gangguanAir->severity->value ?? 'sedang') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('severity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select
                                    class="select2 w-full @error('status') border-red-500 @enderror"
                                    id="status" name="status" data-parsley-required>
                                    <option value="">Pilih Status</option>
                                    @foreach ($statuses as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('status', $gangguanAir->status->value ?? 'active') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Wilayah Terdampak -->
                            <div class="md:col-span-2">
                                <label for="affected_areas" class="block text-sm font-medium text-gray-700 mb-2">
                                    Wilayah Terdampak
                                </label>
                                <x-select2 name="affected_areas[]" :options="$kecamatanList" placeholder="Pilih wilayah terdampak"
                                    :value="old('affected_areas', $gangguanAir->affected_areas ?? [])" multiple id="affected_areas"
                                    class="w-full" />
                                <p class="mt-1 text-sm text-gray-500">Pilih kecamatan/kelurahan yang terdampak</p>
                                @error('affected_areas')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Waktu Mulai -->
                            <div>
                                <label for="start_datetime" class="block text-sm font-medium text-gray-700 mb-2">
                                    Waktu Mulai <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('start_datetime') border-red-500 @enderror"
                                    id="start_datetime" name="start_datetime" data-parsley-required
                                    value="{{ old('start_datetime', isset($gangguanAir) && $gangguanAir->start_datetime ? $gangguanAir->start_datetime->format('Y-m-d\TH:i') : '') }}" />
                                @error('start_datetime')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Perkiraan Waktu Selesai -->
                            <div>
                                <label for="estimated_end_datetime"
                                    class="block text-sm font-medium text-gray-700 mb-2">
                                    Perkiraan Waktu Selesai
                                </label>
                                <input type="datetime-local"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('estimated_end_datetime') border-red-500 @enderror"
                                    id="estimated_end_datetime" name="estimated_end_datetime"
                                    value="{{ old('estimated_end_datetime', isset($gangguanAir) && $gangguanAir->estimated_end_datetime ? $gangguanAir->estimated_end_datetime->format('Y-m-d\TH:i') : '') }}" />
                                @error('estimated_end_datetime')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Waktu Selesai Aktual (untuk edit) -->
                            @if (isset($gangguanAir))
                                <div>
                                    <label for="actual_end_datetime"
                                        class="block text-sm font-medium text-gray-700 mb-2">
                                        Waktu Selesai Aktual
                                    </label>
                                    <input type="datetime-local"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('actual_end_datetime') border-red-500 @enderror"
                                        id="actual_end_datetime" name="actual_end_datetime"
                                        value="{{ old('actual_end_datetime', isset($gangguanAir) && $gangguanAir->actual_end_datetime ? $gangguanAir->actual_end_datetime->format('Y-m-d\TH:i') : '') }}" />
                                    @error('actual_end_datetime')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <!-- Deskripsi -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Deskripsi <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('description') border-red-500 @enderror"
                                    id="description" name="description" rows="4" data-parsley-required
                                    placeholder="Jelaskan detail gangguan air...">{{ old('description', $gangguanAir->description ?? '') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Catatan Penyelesaian (untuk edit) -->
                            @if (isset($gangguanAir))
                                <div class="md:col-span-2">
                                    <label for="resolution_notes"
                                        class="block text-sm font-medium text-gray-700 mb-2">
                                        Catatan Penyelesaian
                                    </label>
                                    <textarea
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('resolution_notes') border-red-500 @enderror"
                                        id="resolution_notes" name="resolution_notes" rows="3"
                                        placeholder="Catatan setelah gangguan selesai ditangani...">{{ old('resolution_notes', $gangguanAir->resolution_notes ?? '') }}</textarea>
                                    @error('resolution_notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
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
