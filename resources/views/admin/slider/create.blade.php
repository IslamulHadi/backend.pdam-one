<x-layouts.auth title="Slider">
    <div class="container-xxl flex-grow-1 container-p-y">
        <flux:breadcrumbs class="py-3 mb-4">
            <flux:breadcrumbs.item href="#">Slider</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ isset($slider) ? 'Edit' : 'Tambah' }} Slider</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <x-admin.card :title="(isset($slider) ? 'Edit' : 'Tambah') . ' Slider Homepage'" :back-route="route('admin.slider.index')">

                @php
                    $urlAction = isset($slider)
                        ? route('admin.slider.update', $slider->id)
                        : route('admin.slider.store');
                    $methodForm = isset($slider) ? 'PUT' : 'POST';
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
                                    value="{{ old('title', $slider->title ?? '') }}"
                                    placeholder="Masukkan judul slider" />
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Subtitle -->
                            <div class="md:col-span-2">
                                <label for="subtitle" class="block text-sm font-medium text-gray-700 mb-2">
                                    Subtitle
                                </label>
                                <textarea
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('subtitle') border-red-500 @enderror"
                                    id="subtitle" name="subtitle" rows="2" placeholder="Deskripsi singkat...">{{ old('subtitle', $slider->subtitle ?? '') }}</textarea>
                                @error('subtitle')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Button Text -->
                            <div>
                                <label for="button_text" class="block text-sm font-medium text-gray-700 mb-2">
                                    Teks Tombol
                                </label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('button_text') border-red-500 @enderror"
                                    id="button_text" name="button_text"
                                    value="{{ old('button_text', $slider->button_text ?? '') }}"
                                    placeholder="Contoh: Selengkapnya" />
                                @error('button_text')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Button URL -->
                            <div>
                                <label for="button_url" class="block text-sm font-medium text-gray-700 mb-2">
                                    URL Tombol
                                </label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('button_url') border-red-500 @enderror"
                                    id="button_url" name="button_url"
                                    value="{{ old('button_url', $slider->button_url ?? '') }}"
                                    placeholder="Contoh: /berita/judul-berita" />
                                @error('button_url')
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
                                    value="{{ old('display_order', $slider->display_order ?? 0) }}"
                                    placeholder="0" min="0" />
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
                                        {{ old('is_active', isset($slider) ? $slider->is_active : true) ? 'checked' : '' }} />
                                    <div
                                        class="relative h-6 w-11 rounded-full bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all">
                                    </div>
                                </label>
                            </div>

                            <!-- Tanggal Mulai -->
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Mulai
                                </label>
                                <input type="date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('start_date') border-red-500 @enderror"
                                    id="start_date" name="start_date"
                                    value="{{ old('start_date', isset($slider) && $slider->start_date ? $slider->start_date->format('Y-m-d') : '') }}" />
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
                                    value="{{ old('end_date', isset($slider) && $slider->end_date ? $slider->end_date->format('Y-m-d') : '') }}" />
                                <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ada batas waktu</p>
                                @error('end_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Gambar Slider -->
                            <div class="md:col-span-2">
                                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                                    Gambar Slider <span class="text-red-500">*</span>
                                </label>
                                <input type="file"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md @error('image') border-red-500 @enderror"
                                    id="image" name="image" accept="image/*" />
                                <p class="mt-1 text-sm text-gray-500">Ukuran yang disarankan: 1920x600 piksel</p>
                                @if (isset($slider) && $slider->getFirstMediaUrl('image'))
                                    <div class="mt-3">
                                        <p class="text-sm text-gray-600 mb-2">Gambar saat ini:</p>
                                        <img src="{{ $slider->getFirstMediaUrl('image') }}" alt="Current image"
                                            class="h-32 w-auto rounded-md border border-gray-300 object-cover">
                                    </div>
                                @endif
                                @error('image')
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
</x-layouts.auth>
