<x-layouts.auth title="Lowongan Karir">
    <div class="container-xxl flex-grow-1 container-p-y">
        <flux:breadcrumbs class="py-3 mb-4">
            <flux:breadcrumbs.item href="#">Karir</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ isset($lowongan) ? 'Edit' : 'Tambah' }} Lowongan</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <x-admin.card :title="(isset($lowongan) ? 'Edit' : 'Tambah') . ' Lowongan Karir'" :back-route="route('admin.lowongan.index')">

                @php
                    $urlAction = isset($lowongan)
                        ? route('admin.lowongan.update', $lowongan->id)
                        : route('admin.lowongan.store');
                    $methodForm = isset($lowongan) ? 'PUT' : 'POST';
                @endphp

                <form id="needs-validation" novalidate method="post" data-parsley-validate
                    action="{{ $urlAction }}">
                    @csrf
                    @method($methodForm)

                    <!-- Card Body -->
                    <div class="px-6 py-6">
                        <x-admin.form-errors />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Judul Posisi -->
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Judul Posisi <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('title') border-red-500 @enderror"
                                    id="title" name="title" data-parsley-required
                                    value="{{ old('title', $lowongan->title ?? '') }}"
                                    placeholder="Contoh: Staff IT" />
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jenis Pekerjaan -->
                            <div>
                                <label for="employment_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis Pekerjaan <span class="text-red-500">*</span>
                                </label>
                                <select
                                    class="select2 w-full @error('employment_type') border-red-500 @enderror"
                                    id="employment_type" name="employment_type" data-parsley-required>
                                    @foreach ($employmentTypes as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('employment_type', $lowongan->employment_type->value ?? 'full_time') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employment_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status Lowongan <span class="text-red-500">*</span>
                                </label>
                                <select
                                    class="select2 w-full @error('status') border-red-500 @enderror"
                                    id="status" name="status" data-parsley-required>
                                    @foreach ($statuses as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('status', $lowongan->status->value ?? 'open') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Department -->
                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                                    Departemen
                                </label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('department') border-red-500 @enderror"
                                    id="department" name="department"
                                    value="{{ old('department', $lowongan->department ?? '') }}"
                                    placeholder="Contoh: Divisi IT" />
                                @error('department')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Location -->
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                    Lokasi
                                </label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('location') border-red-500 @enderror"
                                    id="location" name="location"
                                    value="{{ old('location', $lowongan->location ?? 'Surabaya') }}"
                                    placeholder="Contoh: Surabaya" />
                                @error('location')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Deadline -->
                            <div>
                                <label for="deadline" class="block text-sm font-medium text-gray-700 mb-2">
                                    Batas Lamaran
                                </label>
                                <input type="date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('deadline') border-red-500 @enderror"
                                    id="deadline" name="deadline"
                                    value="{{ old('deadline', isset($lowongan->deadline) ? $lowongan->deadline->format('Y-m-d') : '') }}" />
                                @error('deadline')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Urutan Tampil -->
                            <div>
                                <label for="display_order" class="block text-sm font-medium text-gray-700 mb-2">
                                    Urutan Tampil
                                </label>
                                <input type="number"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('display_order') border-red-500 @enderror"
                                    id="display_order" name="display_order"
                                    value="{{ old('display_order', $lowongan->display_order ?? 0) }}"
                                    placeholder="0" min="0" />
                                @error('display_order')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Deskripsi <span class="text-red-500">*</span>
                                </label>
                                <div id="description-editor" class="bg-white rounded border border-gray-300"
                                    style="min-height: 150px;"></div>
                                <input type="hidden" name="description" id="description"
                                    value="{{ old('description', $lowongan->description ?? '') }}">
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Persyaratan -->
                            <div class="md:col-span-2">
                                <label for="requirements" class="block text-sm font-medium text-gray-700 mb-2">
                                    Persyaratan <span class="text-red-500">*</span>
                                </label>
                                <div id="requirements-editor" class="bg-white rounded border border-gray-300"
                                    style="min-height: 150px;"></div>
                                <input type="hidden" name="requirements" id="requirements"
                                    value="{{ old('requirements', $lowongan->requirements ?? '') }}">
                                @error('requirements')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggung Jawab -->
                            <div class="md:col-span-2">
                                <label for="responsibilities" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggung Jawab
                                </label>
                                <div id="responsibilities-editor" class="bg-white rounded border border-gray-300"
                                    style="min-height: 150px;"></div>
                                <input type="hidden" name="responsibilities" id="responsibilities"
                                    value="{{ old('responsibilities', $lowongan->responsibilities ?? '') }}">
                                @error('responsibilities')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status Aktif -->
                            <div class="flex items-center gap-2">
                                <label for="is_active" class="text-sm font-medium text-gray-700">
                                    Status Aktif
                                </label>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="is_active" name="is_active" value="1"
                                        class="sr-only peer"
                                        {{ old('is_active', isset($lowongan) ? $lowongan->is_active : true) ? 'checked' : '' }} />
                                    <div
                                        class="relative h-6 w-11 rounded-full bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all">
                                    </div>
                                </label>
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
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    width: '100%'
                });
            });
        </script>
        <script>
            const toolbarOptions = [
                ['bold', 'italic', 'underline'],
                [{
                    'list': 'ordered'
                }, {
                    'list': 'bullet'
                }],
                [{
                    'header': [1, 2, 3, false]
                }],
                ['clean']
            ];

            // Description Editor
            const descriptionEditor = new Quill('#description-editor', {
                theme: 'snow',
                modules: {
                    toolbar: toolbarOptions
                },
                placeholder: 'Masukkan deskripsi lowongan...'
            });

            // Requirements Editor
            const requirementsEditor = new Quill('#requirements-editor', {
                theme: 'snow',
                modules: {
                    toolbar: toolbarOptions
                },
                placeholder: 'Masukkan persyaratan lowongan...'
            });

            // Responsibilities Editor
            const responsibilitiesEditor = new Quill('#responsibilities-editor', {
                theme: 'snow',
                modules: {
                    toolbar: toolbarOptions
                },
                placeholder: 'Masukkan tanggung jawab posisi...'
            });

            // Load existing content
            @if (old('description', $lowongan->description ?? ''))
                descriptionEditor.root.innerHTML = {!! json_encode(old('description', $lowongan->description ?? '')) !!};
            @endif

            @if (old('requirements', $lowongan->requirements ?? ''))
                requirementsEditor.root.innerHTML = {!! json_encode(old('requirements', $lowongan->requirements ?? '')) !!};
            @endif

            @if (old('responsibilities', $lowongan->responsibilities ?? ''))
                responsibilitiesEditor.root.innerHTML = {!! json_encode(old('responsibilities', $lowongan->responsibilities ?? '')) !!};
            @endif

            // Update hidden inputs on form submit
            document.getElementById('needs-validation').addEventListener('submit', function(e) {
                document.getElementById('description').value = descriptionEditor.root.innerHTML;
                document.getElementById('requirements').value = requirementsEditor.root.innerHTML;
                document.getElementById('responsibilities').value = responsibilitiesEditor.root.innerHTML;
            });
        </script>
    @endpush
</x-layouts.auth>
