<x-layouts.auth title="Berita">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="py-3 mb-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <span class="text-gray-500 text-sm font-light">Master Data</span>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <span class="text-gray-500 mx-2">></span>
                            <span class="text-gray-900 text-sm font-medium">Kategori Berita</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <x-admin.card :title="(request()->routeIs('admin.kategori-berita.create') ? 'Tambah' : 'Ubah') . ' Data'" :back-route="route('admin.kategori-berita.index')">

                @php  $urlAction = request()->routeIs('admin.kategori-berita.create') ? route('admin.kategori-berita.store') : route('admin.kategori-berita.update', isset($kategori) ? $kategori : null) @endphp
                @php  $methodForm = request()->routeIs('admin.kategori-berita.create') ? 'POST' : 'PUT' @endphp
                <form id="needs-validation" novalidate method="post" data-parsley-validate
                    action="{{ $urlAction }}">
                    @csrf
                    @method($methodForm)

                    <!-- Card Body -->
                    <div class="px-6 py-6">
                        <!-- Form Fields Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Judul -->
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Kategori <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('title') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                    id="title" name="nama" data-parsley-required
                                    value="{{ old('nama', isset($kategori) ? $kategori->nama : '') }}"
                                    placeholder="Masukkan nama kategori" />
                                @error('nama')
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


