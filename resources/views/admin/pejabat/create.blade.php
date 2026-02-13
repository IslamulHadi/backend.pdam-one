<x-layouts.auth title="Pejabat">
    <div class="container-xxl flex-grow-1 container-p-y">
        <flux:breadcrumbs class="py-3 mb-4">
            <flux:breadcrumbs.item href="#">Struktur Organisasi</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ isset($pejabat) ? 'Edit' : 'Tambah' }} Pejabat</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <x-admin.card :title="(isset($pejabat) ? 'Edit' : 'Tambah') . ' Data Pejabat'" :back-route="route('admin.pejabat.index')">

                @php
                    $urlAction = isset($pejabat)
                        ? route('admin.pejabat.update', $pejabat->id)
                        : route('admin.pejabat.store');
                    $methodForm = isset($pejabat) ? 'PUT' : 'POST';
                @endphp

                <form id="needs-validation" novalidate method="post" data-parsley-validate
                    enctype="multipart/form-data" action="{{ $urlAction }}">
                    @csrf
                    @method($methodForm)

                    <!-- Card Body -->
                    <div class="px-6 py-6">
                        <x-admin.form-errors />

                        {{-- Pilih dari Data Pegawai --}}
                        @if (count($pegawaiList) > 0)
                            <div class="mb-6 p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                                <h4 class="text-sm font-semibold text-indigo-800 mb-3">
                                    <i class="bx bx-link mr-1"></i> Ambil dari Data Pegawai (Sigap)
                                </h4>
                                <div>
                                    <select class="select2 w-full" id="pegawai_id" name="pegawai_id">
                                        <option value="">-- Pilih Pegawai (opsional) --</option>
                                        @foreach ($pegawaiList as $p)
                                            <option value="{{ $p['id'] }}"
                                                data-nama="{{ $p['nama'] }}"
                                                data-jabatan="{{ $p['jabatan'] }}"
                                                data-level="{{ $p['level'] }}"
                                                data-bidang="{{ $p['bidang'] }}"
                                                {{ old('pegawai_id', $pejabat->pegawai_id ?? '') == $p['id'] ? 'selected' : '' }}>
                                                {{ $p['nama'] }} - {{ $p['jabatan'] }} ({{ $p['bidang'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-2 text-xs text-indigo-600">
                                        Jika dipilih, nama, jabatan, level, dan bidang akan otomatis terisi dari data pegawai.
                                        Kosongkan untuk input manual.
                                    </p>
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="pegawai_id" value="">
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama -->
                            <div>
                                <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Pejabat <span class="text-red-500 manual-required">*</span>
                                </label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('nama') border-red-500 @enderror"
                                    id="nama" name="nama"
                                    value="{{ old('nama', $pejabat->nama ?? '') }}"
                                    placeholder="Masukkan nama pejabat" />
                                @error('nama')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jabatan -->
                            <div>
                                <label for="jabatan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jabatan <span class="text-red-500 manual-required">*</span>
                                </label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('jabatan') border-red-500 @enderror"
                                    id="jabatan" name="jabatan"
                                    value="{{ old('jabatan', $pejabat->jabatan ?? '') }}"
                                    placeholder="Contoh: Direktur Utama" />
                                @error('jabatan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Level -->
                            <div>
                                <label for="level" class="block text-sm font-medium text-gray-700 mb-2">
                                    Level Jabatan <span class="text-red-500 manual-required">*</span>
                                </label>
                                <select
                                    class="select2 w-full @error('level') border-red-500 @enderror"
                                    id="level" name="level">
                                    <option value="">Pilih Level</option>
                                    @foreach ($levels as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('level', $pejabat->level->value ?? '') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('level')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bidang -->
                            <div>
                                <label for="bidang" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bidang
                                </label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('bidang') border-red-500 @enderror"
                                    id="bidang" name="bidang"
                                    value="{{ old('bidang', $pejabat->bidang ?? '') }}"
                                    placeholder="Contoh: Teknik & Operasional" />
                                @error('bidang')
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
                                    value="{{ old('display_order', $pejabat->display_order ?? 0) }}"
                                    placeholder="0" min="0" />
                                @error('display_order')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status Aktif -->
                            <div class="flex items-end gap-2 pb-1">
                                <label for="is_active" class="text-sm font-medium text-gray-700">
                                    Status Aktif
                                </label>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="is_active" name="is_active" value="1"
                                        class="sr-only peer"
                                        {{ old('is_active', isset($pejabat) ? $pejabat->is_active : true) ? 'checked' : '' }} />
                                    <div
                                        class="relative h-6 w-11 rounded-full bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all">
                                    </div>
                                </label>
                            </div>

                            <!-- Deskripsi -->
                            <div class="md:col-span-2">
                                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                                    Deskripsi
                                </label>
                                <textarea
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('deskripsi') border-red-500 @enderror"
                                    id="deskripsi" name="deskripsi" rows="3"
                                    placeholder="Deskripsi singkat tentang pejabat">{{ old('deskripsi', $pejabat->deskripsi ?? '') }}</textarea>
                                @error('deskripsi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Foto -->
                            <div class="md:col-span-2">
                                <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">
                                    Foto Pejabat
                                </label>
                                <input type="file"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md @error('foto') border-red-500 @enderror"
                                    id="foto" name="foto" accept="image/*" />
                                <p class="mt-1 text-sm text-gray-500">Format: JPEG, PNG, JPG, GIF, WEBP. Maksimal 2MB.</p>
                                @if (isset($pejabat) && $pejabat->getFirstMediaUrl('foto'))
                                    <div class="mt-3">
                                        <p class="text-sm text-gray-600 mb-2">Foto saat ini:</p>
                                        <img src="{{ $pejabat->getFirstMediaUrl('foto') }}"
                                            alt="{{ $pejabat->nama }}"
                                            class="h-32 w-32 rounded-full border border-gray-300 object-cover">
                                    </div>
                                @endif
                                @error('foto')
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
        <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    width: '100%'
                });

                // Auto-fill fields when pegawai is selected
                $('#pegawai_id').on('change', function() {
                    var selected = $(this).find(':selected');
                    var pegawaiId = selected.val();

                    if (pegawaiId) {
                        $('#nama').val(selected.data('nama') || '');
                        $('#jabatan').val(selected.data('jabatan') || '');
                        $('#bidang').val(selected.data('bidang') || '');

                        // Set level via select2
                        var levelValue = selected.data('level') || '';
                        $('#level').val(levelValue).trigger('change');

                        // Mark fields as read-only visual hint
                        $('#nama, #jabatan, #bidang').addClass('bg-gray-100');
                        $('.manual-required').hide();
                    } else {
                        // Clear the visual hint
                        $('#nama, #jabatan, #bidang').removeClass('bg-gray-100');
                        $('.manual-required').show();
                    }
                });

                // Trigger change on load if pegawai is pre-selected
                if ($('#pegawai_id').val()) {
                    $('#nama, #jabatan, #bidang').addClass('bg-gray-100');
                    $('.manual-required').hide();
                }
            });
        </script>
    @endpush
</x-layouts.auth>
