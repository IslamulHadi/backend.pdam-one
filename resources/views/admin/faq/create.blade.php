<x-layouts.auth title="FAQ">
    <div class="container-xxl flex-grow-1 container-p-y">
        <flux:breadcrumbs class="py-3 mb-4">
            <flux:breadcrumbs.item href="#">FAQ</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ request()->routeIs('admin.faq.create') ? 'Tambah' : 'Ubah' }} FAQ</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <x-admin.card :title="(request()->routeIs('admin.faq.create') ? 'Tambah' : 'Ubah') . ' Data FAQ'" :back-route="route('admin.faq.index')">

                @php
                    $urlAction = request()->routeIs('admin.faq.create')
                        ? route('admin.faq.store')
                        : route('admin.faq.update', isset($faq) ? $faq : null);
                    $methodForm = request()->routeIs('admin.faq.create') ? 'POST' : 'PUT';
                @endphp

                <form id="needs-validation" novalidate method="post" data-parsley-validate action="{{ $urlAction }}">
                    @csrf
                    @method($methodForm)

                    <!-- Card Body -->
                    <div class="px-6 py-6">
                        <x-admin.form-errors />

                        <!-- Form Fields Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Pertanyaan -->
                            <div class="md:col-span-2">
                                <label for="question" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pertanyaan <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('question') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                    id="question" name="question" rows="3" data-parsley-required
                                    placeholder="Masukkan pertanyaan">{{ old('question', isset($faq) ? $faq->question : '') }}</textarea>
                                @error('question')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jawaban -->
                            <div class="md:col-span-2">
                                <label for="answer" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jawaban <span class="text-red-500">*</span>
                                </label>
                                <div class="@error('answer') border-red-500 rounded-md @enderror" id="answer-wrapper">
                                    <div id="answer-editor" style="min-height: 250px;"></div>
                                    <textarea name="answer" id="answer_hidden" style="display: none;" data-parsley-required>{{ old('answer', isset($faq) ? $faq->answer : '') }}</textarea>
                                </div>
                                @error('answer')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kategori -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <select
                                    class="select2 w-full @error('category') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                    id="category" name="category" data-parsley-required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach (\App\Enums\FaqCategory::cases() as $category)
                                        <option value="{{ $category->value }}"
                                            {{ old('category', isset($faq) ? $faq->category?->value : '') == $category->value ? 'selected' : '' }}>
                                            {{ $category->getLabel() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Urutan Tampil -->
                            <div>
                                <label for="display_order" class="block text-sm font-medium text-gray-700 mb-2">
                                    Urutan Tampil
                                </label>
                                <input type="number"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('display_order') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                    id="display_order" name="display_order" min="0"
                                    value="{{ old('display_order', isset($faq) ? $faq->display_order : '0') }}"
                                    placeholder="0" />
                                @error('display_order')
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
                                        {{ old('is_active', isset($faq) && $faq->is_active ? 'checked' : (request()->routeIs('admin.faq.create') ? 'checked' : '')) }} />
                                    <div
                                        class="relative h-6 w-11 rounded-full bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600">
                                    </div>
                                </label>
                            </div>
                            @error('is_active')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                // Initialize Quill editor for answer field
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

                const quill = new Quill('#answer-editor', {
                    theme: 'snow',
                    placeholder: 'Masukkan jawaban...',
                    modules: {
                        toolbar: toolbarOptions
                    }
                });

                // Set existing content if editing
                @if (isset($faq) && $faq->answer)
                    quill.root.innerHTML = {!! json_encode(old('answer', $faq->answer)) !!};
                @elseif (old('answer'))
                    quill.root.innerHTML = {!! json_encode(old('answer')) !!};
                @endif

                // Update hidden textarea on form submit
                $('form#needs-validation').on('submit', function(e) {
                    const answer = quill.root.innerHTML;
                    $('#answer_hidden').val(answer);
                    return true;
                });
            });
        </script>
    @endpush
</x-layouts.auth>
