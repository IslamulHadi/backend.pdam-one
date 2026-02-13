<x-layouts.auth title="Pengumuman">
    <div class="container-xxl flex-grow-1 container-p-y">
        <flux:breadcrumbs class="py-3 mb-4">
            <flux:breadcrumbs.item href="#">Pengumuman</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ isset($pengumuman) ? 'Edit' : 'Tambah' }} Pengumuman</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <x-admin.card :title="(isset($pengumuman) ? 'Edit' : 'Tambah') . ' Pengumuman'" :back-route="route('admin.pengumuman.index')">

                @php
                    $urlAction = isset($pengumuman)
                        ? route('admin.pengumuman.update', $pengumuman->id)
                        : route('admin.pengumuman.store');
                    $methodForm = isset($pengumuman) ? 'PUT' : 'POST';
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
                                    value="{{ old('title', $pengumuman->title ?? '') }}"
                                    placeholder="Masukkan judul pengumuman" />
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Prioritas -->
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                                    Prioritas <span class="text-red-500">*</span>
                                </label>
                                <select
                                    class="select2 w-full @error('priority') border-red-500 @enderror"
                                    id="priority" name="priority" data-parsley-required>
                                    <option value="">Pilih Prioritas</option>
                                    @foreach ($priorities as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('priority', $pengumuman->priority->value ?? 'sedang') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Slug -->
                            <div>
                                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                    Slug (opsional)
                                </label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('slug') border-red-500 @enderror"
                                    id="slug" name="slug"
                                    value="{{ old('slug', $pengumuman->slug ?? '') }}"
                                    placeholder="Otomatis dari judul jika kosong" />
                                @error('slug')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal Mulai -->
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Mulai
                                </label>
                                <input type="date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('start_date') border-red-500 @enderror"
                                    id="start_date" name="start_date"
                                    value="{{ old('start_date', isset($pengumuman) && $pengumuman->start_date ? $pengumuman->start_date->format('Y-m-d') : '') }}" />
                                <p class="mt-1 text-sm text-gray-500">Kosongkan jika langsung aktif</p>
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal Berakhir -->
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Berakhir
                                </label>
                                <input type="date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('end_date') border-red-500 @enderror"
                                    id="end_date" name="end_date"
                                    value="{{ old('end_date', isset($pengumuman) && $pengumuman->end_date ? $pengumuman->end_date->format('Y-m-d') : '') }}" />
                                <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ada batas waktu</p>
                                @error('end_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Konten -->
                            <div class="md:col-span-2">
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                    Isi Pengumuman <span class="text-red-500">*</span>
                                </label>
                                <div id="content-wrapper">
                                    <div id="content" style="min-height: 300px;"></div>
                                    <textarea name="content" id="content_hidden" style="display: none;" data-parsley-required>{{ old('content', $pengumuman->content ?? '') }}</textarea>
                                </div>
                                @error('content')
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
                                        {{ old('is_active', isset($pengumuman) ? $pengumuman->is_active : true) ? 'checked' : '' }} />
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
        <link rel="stylesheet" href="{{ asset('assets/libs/quill/editor.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/libs/quill/typography.css') }}">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('js')
        <script src="{{ asset('assets/libs/quill/quill.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    width: '100%'
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                const toolbarOptions = [
                    [{
                        'header': [1, 2, 3, false]
                    }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    [{
                        'color': []
                    }, {
                        'background': []
                    }],
                    ['link'],
                    ['clean']
                ];

                const quill = new Quill('#content', {
                    theme: 'snow',
                    placeholder: 'Masukkan isi pengumuman...',
                    modules: {
                        toolbar: toolbarOptions
                    }
                });

                @if (isset($pengumuman) && $pengumuman->content)
                    quill.root.innerHTML = {!! json_encode(old('content', $pengumuman->content)) !!};
                @elseif (old('content'))
                    quill.root.innerHTML = {!! json_encode(old('content')) !!};
                @endif

                $('form#needs-validation').on('submit', function(e) {
                    const content = quill.root.innerHTML;
                    $('#content_hidden').val(content);
                    return true;
                });
            });
        </script>
    @endpush
</x-layouts.auth>
