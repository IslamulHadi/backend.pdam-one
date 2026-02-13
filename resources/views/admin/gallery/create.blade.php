<x-layouts.auth title="Galeri">
    <div class="container-xxl flex-grow-1 container-p-y">
        <flux:breadcrumbs class="py-3 mb-4">
            <flux:breadcrumbs.item href="#">Galeri</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ isset($gallery) ? 'Edit' : 'Tambah' }} Galeri</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <x-admin.card :title="(isset($gallery) ? 'Edit' : 'Tambah') . ' Data Galeri'" :back-route="route('admin.galeri.index')">

                @php
                    $urlAction = isset($gallery)
                        ? route('admin.galeri.update', $gallery->id)
                        : route('admin.galeri.store');
                    $methodForm = isset($gallery) ? 'PUT' : 'POST';
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
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Judul <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('title') border-red-500 @enderror"
                                    id="title" name="title" data-parsley-required
                                    value="{{ old('title', $gallery->title ?? '') }}"
                                    placeholder="Masukkan judul galeri" />
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tipe -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipe Galeri <span class="text-red-500">*</span>
                                </label>
                                <select
                                    class="select2 w-full @error('type') border-red-500 @enderror"
                                    id="type" name="type" data-parsley-required>
                                    <option value="">Pilih Tipe</option>
                                    @foreach ($types as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('type', $gallery->type->value ?? '') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
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
                                    value="{{ old('display_order', $gallery->display_order ?? 0) }}"
                                    placeholder="0" min="0" />
                                @error('display_order')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Video URL (hanya untuk tipe video) -->
                            <div class="md:col-span-2" id="video-url-wrapper" style="display: none;">
                                <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">
                                    URL Video (YouTube/Vimeo)
                                </label>
                                <input type="url"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('video_url') border-red-500 @enderror"
                                    id="video_url" name="video_url"
                                    value="{{ old('video_url', $gallery->video_url ?? '') }}"
                                    placeholder="https://www.youtube.com/watch?v=..." />
                                @error('video_url')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Deskripsi
                                </label>
                                <textarea
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('description') border-red-500 @enderror"
                                    id="description" name="description" rows="3"
                                    placeholder="Masukkan deskripsi galeri">{{ old('description', $gallery->description ?? '') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Thumbnail -->
                            <div class="md:col-span-2">
                                <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-2">
                                    Thumbnail
                                </label>
                                <input type="file"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md @error('thumbnail') border-red-500 @enderror"
                                    id="thumbnail" name="thumbnail" accept="image/*" />
                                @if (isset($gallery) && $gallery->getFirstMediaUrl('thumbnail'))
                                    <div class="mt-3">
                                        <p class="text-sm text-gray-600 mb-2">Thumbnail saat ini:</p>
                                        <img src="{{ $gallery->getFirstMediaUrl('thumbnail') }}"
                                            alt="Current thumbnail"
                                            class="h-32 w-auto rounded-md border border-gray-300 object-cover">
                                    </div>
                                @endif
                                @error('thumbnail')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Upload Gambar (hanya untuk tipe foto) -->
                            <div class="md:col-span-2" id="images-wrapper">
                                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload Gambar <span class="text-red-500" id="images-required">*</span>
                                </label>
                                <input type="file"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md @error('images') border-red-500 @enderror"
                                    id="images" name="images[]" accept="image/*" multiple />
                                <p class="mt-1 text-sm text-gray-500">Anda dapat memilih beberapa gambar sekaligus.</p>
                                @error('images')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('images.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                @if (isset($gallery) && $gallery->getMedia('images')->count() > 0)
                                    <div class="mt-4">
                                        <p class="text-sm text-gray-600 mb-2">Gambar yang sudah diupload:</p>
                                        <div class="grid grid-cols-4 md:grid-cols-6 gap-3">
                                            @foreach ($gallery->getMedia('images') as $media)
                                                <div class="relative group" data-media-id="{{ $media->id }}">
                                                    <img src="{{ $media->getUrl() }}" alt="{{ $media->name }}"
                                                        class="h-24 w-full object-cover rounded-md border border-gray-300">
                                                    <button type="button"
                                                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 btn-delete-image"
                                                        data-media-id="{{ $media->id }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Status Aktif -->
                            <div class="flex items-center gap-2">
                                <label for="is_active" class="text-sm font-medium text-gray-700">
                                    Status Aktif
                                </label>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="is_active" name="is_active" value="1"
                                        class="sr-only peer"
                                        {{ old('is_active', isset($gallery) ? $gallery->is_active : true) ? 'checked' : '' }} />
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
        <script>
            $(document).ready(function() {
                function toggleFields() {
                    const type = $('#type').val();
                    if (type === 'video') {
                        $('#video-url-wrapper').show();
                        $('#images-wrapper').hide();
                        $('#images-required').hide();
                    } else {
                        $('#video-url-wrapper').hide();
                        $('#images-wrapper').show();
                        $('#images-required').show();
                    }
                }

                toggleFields();
                $('#type').on('change', toggleFields);

                // Delete image handler
                $('.btn-delete-image').on('click', function() {
                    const mediaId = $(this).data('media-id');
                    const galleryId = '{{ $gallery->id ?? '' }}';
                    const $parent = $(this).closest('[data-media-id]');

                    if (confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
                        $.ajax({
                            url: `/admin/galeri/${galleryId}/delete-image/${mediaId}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    $parent.fadeOut(300, function() {
                                        $(this).remove();
                                    });
                                }
                            },
                            error: function() {
                                alert('Gagal menghapus gambar');
                            }
                        });
                    }
                });
            });
        </script>
    @endpush
</x-layouts.auth>
