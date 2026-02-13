<x-layouts.auth title="Berita">
    <div class="container-xxl flex-grow-1 container-p-y">
        <flux:breadcrumbs class="py-3 mb-4">
            <flux:breadcrumbs.item href="#">Berita</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Input Berita</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <x-admin.card :title="(request()->routeIs('admin.input-berita.create') ? 'Tambah' : 'Ubah') . ' Data'" :back-route="route('admin.input-berita.index')">

                @php  $urlAction = request()->routeIs('admin.input-berita.create') ? route('admin.input-berita.store') : route('admin.input-berita.update', isset($berita) ? $berita : null) @endphp
                @php  $methodForm = request()->routeIs('admin.input-berita.create') ? 'POST' : 'PUT' @endphp
                <form id="needs-validation" novalidate method="post" data-parsley-validate
                    enctype="multipart/form-data" action="{{ $urlAction }}">
                    @csrf
                    @method($methodForm)

                    <!-- Card Body -->
                    <div class="px-6 py-6">
                        <x-admin.form-errors />

                        <!-- Form Fields Grid -->
                        <div class="grid grid-cols-1 gap-6">

                            <!-- Form Fields Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Judul -->
                                <div class="md:col-span-2">
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                        Judul <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('title') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                        id="title" name="title" data-parsley-required
                                        value="{{ old('title', isset($berita) ? $berita->title : '') }}"
                                        placeholder="Masukkan judul berita" />
                                    @error('title')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="kategori_ids" class="block text-sm font-medium text-gray-700 mb-2">
                                        Kategori <span class="text-red-500">*</span>
                                    </label>
                                    <x-select2 name="kategori_ids[]" :options="$kategoris->pluck('nama', 'id')->toArray()" placeholder="Pilih Kategori"
                                        :value="old(
                                            'kategori_ids',
                                            isset($berita) && $berita->kategori
                                                ? $berita->kategori->pluck('id')->toArray()
                                                : [],
                                        )" multiple id="kategori_ids" data-parsley-required
                                        class="w-full" />
                                    @error('kategori_ids')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Tanggal -->
                                <div>
                                    <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('published_at') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                        id="published_at" name="published_at" data-parsley-required
                                        value="{{ old('published_at', isset($berita) && $berita->published_at ? \Illuminate\Support\Carbon::parse($berita->published_at)->format('Y-m-d') : '') }}" />
                                    @error('published_at')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Featured -->
                                <div class="flex items-center gap-2">
                                    <label for="is_featured" class="text-sm font-medium text-gray-700">
                                        Berita Utama <span class="text-red-500">*</span>
                                    </label>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                            id="is_featured" 
                                            name="is_featured" 
                                            value="1"
                                            class="sr-only peer"
                                            {{ old('is_featured', isset($berita) && $berita->is_featured ? 'checked' : '') }} />
                                        <div class="relative h-6 w-11 rounded-full bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600"></div>
                                    </label>
                                </div>
                                @error('is_featured')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <div class="md:col-span-2">
                                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                        Isi Berita <span class="text-red-500">*</span>
                                    </label>
                                    <div class="@error('content') border-red-500 rounded-md @enderror"
                                        id="content-wrapper">
                                        <div id="content" style="min-height: 400px;"></div>
                                        <textarea name="content" id="content_hidden" style="display: none;" data-parsley-required>{{ old('content', isset($berita) ? $berita->content : '') }}</textarea>
                                    </div>
                                    @error('content')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Thumbnail -->
                                <div class="md:col-span-2">
                                    <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-2">
                                        Thumbnail <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1 flex items-center">
                                        <input type="file"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md @error('thumbnail') border-red-500 @enderror"
                                            id="thumbnail" name="thumbnail" />
                                    </div>
                                    @if (isset($berita) && $berita->getFirstMediaUrl('thumbnails'))
                                        <div class="mt-3">
                                            <p class="text-sm text-gray-600 mb-2">Thumbnail saat ini:</p>
                                            <img src="{{ $berita->getFirstMediaUrl('thumbnails') }}"
                                                alt="Current thumbnail"
                                                class="h-32 w-auto rounded-md border border-gray-300 object-cover">
                                        </div>
                                    @endif
                                    @error('thumbnail')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
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
                // Initialize Quill with image upload handler
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
                    ['link', 'image'],
                    ['clean']
                ];

                const quill = new Quill('#content', {
                    theme: 'snow',
                    placeholder: 'Masukkan isi berita...',
                    modules: {
                        toolbar: {
                            container: toolbarOptions,
                            handlers: {
                                image: function() {
                                    const input = document.createElement('input');
                                    input.setAttribute('type', 'file');
                                    input.setAttribute('accept', 'image/*');
                                    input.click();

                                    input.onchange = async function() {
                                        const file = input.files[0];
                                        if (file) {
                                            const formData = new FormData();
                                            formData.append('image', file);

                                            try {
                                                const response = await fetch(
                                                    '{{ route('admin.input-berita.upload-image') }}', {
                                                        method: 'POST',
                                                        headers: {
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        },
                                                        body: formData
                                                    });

                                                const data = await response.json();

                                                if (data.success) {
                                                    const range = quill.getSelection(true);
                                                    quill.insertEmbed(range.index, 'image', data
                                                        .url);
                                                    quill.setSelection(range.index + 1);
                                                } else {
                                                    alert('Gagal mengupload gambar');
                                                }
                                            } catch (error) {
                                                console.error('Error uploading image:', error);
                                                alert('Terjadi kesalahan saat mengupload gambar');
                                            }
                                        }
                                    };
                                }
                            }
                        }
                    }
                });

                // Set existing content if editing
                @if (isset($berita) && $berita->content)
                    quill.root.innerHTML = {!! json_encode(old('content', $berita->content)) !!};
                @elseif (old('content'))
                    quill.root.innerHTML = {!! json_encode(old('content')) !!};
                @endif

                // Update hidden textarea on form submit
                $('form#needs-validation').on('submit', function(e) {
                    const content = quill.root.innerHTML;
                    $('#content_hidden').val(content);
                    return true;
                });
            });
        </script>
    @endpush
</x-layouts.auth>
