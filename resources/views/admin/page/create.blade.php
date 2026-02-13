<x-layouts.auth title="Halaman Statis">
    <div class="container-xxl flex-grow-1 container-p-y">
        <flux:breadcrumbs class="py-3 mb-4">
            <flux:breadcrumbs.item href="#">Konten</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ isset($page) ? 'Edit' : 'Tambah' }} Halaman</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <x-admin.card :title="(isset($page) ? 'Edit' : 'Tambah') . ' Halaman'" :back-route="route('admin.page.index')">

                @php
                    $urlAction = isset($page)
                        ? route('admin.page.update', $page->id)
                        : route('admin.page.store');
                    $methodForm = isset($page) ? 'PUT' : 'POST';
                @endphp

                <form id="needs-validation" novalidate method="post" data-parsley-validate
                    enctype="multipart/form-data" action="{{ $urlAction }}">
                    @csrf
                    @method($methodForm)

                    <!-- Card Body -->
                    <div class="px-6 py-6">
                        <x-admin.form-errors />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Judul -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Judul <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('title') border-red-500 @enderror"
                                    id="title" name="title" data-parsley-required
                                    value="{{ old('title', $page->title ?? '') }}"
                                    placeholder="Contoh: Sejarah PDAM" />
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Slug -->
                            <div>
                                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                    Slug <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('slug') border-red-500 @enderror"
                                    id="slug" name="slug" data-parsley-required
                                    value="{{ old('slug', $page->slug ?? '') }}"
                                    placeholder="Contoh: sejarah" />
                                <p class="mt-1 text-sm text-gray-500">URL: /page/{slug}</p>
                                @error('slug')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Meta Title -->
                            <div>
                                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Meta Title (SEO)
                                </label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('meta_title') border-red-500 @enderror"
                                    id="meta_title" name="meta_title"
                                    value="{{ old('meta_title', $page->meta_title ?? '') }}"
                                    placeholder="Judul untuk SEO" />
                                @error('meta_title')
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
                                    value="{{ old('display_order', $page->display_order ?? 0) }}"
                                    placeholder="0" min="0" />
                                @error('display_order')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Meta Description -->
                            <div class="md:col-span-2">
                                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Meta Description (SEO)
                                </label>
                                <textarea
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('meta_description') border-red-500 @enderror"
                                    id="meta_description" name="meta_description" rows="2" placeholder="Deskripsi untuk SEO">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                                @error('meta_description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Konten -->
                            <div class="md:col-span-2">
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                    Konten
                                </label>
                                <div id="content-editor" class="bg-white rounded border border-gray-300"
                                    style="min-height: 300px;"></div>
                                <input type="hidden" name="content" id="content"
                                    value="{{ old('content', $page->content ?? '') }}">
                                @error('content')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Featured Image -->
                            <div class="md:col-span-2">
                                <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">
                                    Gambar Utama
                                </label>
                                <input type="file"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md @error('featured_image') border-red-500 @enderror"
                                    id="featured_image" name="featured_image" accept="image/*" />
                                @if (isset($page) && $page->getFirstMediaUrl('featured'))
                                    <div class="mt-3">
                                        <img src="{{ $page->getFirstMediaUrl('featured') }}" alt="Featured"
                                            class="w-48 h-32 object-cover rounded-lg">
                                    </div>
                                @endif
                                @error('featured_image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Additional Images -->
                            <div class="md:col-span-2">
                                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                                    Gambar Tambahan
                                </label>
                                <input type="file"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md @error('images') border-red-500 @enderror"
                                    id="images" name="images[]" accept="image/*" multiple />
                                @if (isset($page) && $page->getMedia('images')->count() > 0)
                                    <div class="mt-3 grid grid-cols-4 gap-4">
                                        @foreach ($page->getMedia('images') as $media)
                                            <div class="relative group">
                                                <img src="{{ $media->getUrl() }}" alt="Image"
                                                    class="w-full h-24 object-cover rounded-lg">
                                                <button type="button"
                                                    class="btn-delete-image absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                                                    data-delete-url="{{ route('admin.page.delete-image', [$page->id, $media->id]) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                @error('images')
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
                                        {{ old('is_active', isset($page) ? $page->is_active : true) ? 'checked' : '' }} />
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
    @endpush

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
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
                ['link', 'image'],
                ['clean']
            ];

            const contentEditor = new Quill('#content-editor', {
                theme: 'snow',
                modules: {
                    toolbar: toolbarOptions
                },
                placeholder: 'Masukkan konten halaman...'
            });

            @if (old('content', $page->content ?? ''))
                contentEditor.root.innerHTML = {!! json_encode(old('content', $page->content ?? '')) !!};
            @endif

            document.getElementById('needs-validation').addEventListener('submit', function(e) {
                document.getElementById('content').value = contentEditor.root.innerHTML;
            });

            // Auto-generate slug from title
            document.getElementById('title').addEventListener('input', function() {
                const slugField = document.getElementById('slug');
                if (!slugField.value || slugField.dataset.autoGenerate !== 'false') {
                    slugField.value = this.value
                        .toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-');
                    slugField.dataset.autoGenerate = 'true';
                }
            });

            document.getElementById('slug').addEventListener('input', function() {
                this.dataset.autoGenerate = 'false';
            });

            // Delete image button
            document.querySelectorAll('.btn-delete-image').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('Hapus gambar ini?')) {
                        fetch(this.dataset.deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        }).then(response => {
                            if (response.ok) {
                                this.parentElement.remove();
                            }
                        });
                    }
                });
            });
        </script>
    @endpush
</x-layouts.auth>
