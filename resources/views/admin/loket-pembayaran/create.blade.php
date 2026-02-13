<x-layouts.auth title="Loket Pembayaran">
    <div class="container-xxl flex-grow-1 container-p-y">
        <flux:breadcrumbs class="py-3 mb-4">
            <flux:breadcrumbs.item href="#">Loket Pembayaran</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ isset($loketPembayaran) ? 'Edit' : 'Tambah' }} Loket</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <x-admin.card :title="(isset($loketPembayaran) ? 'Edit' : 'Tambah') . ' Loket Pembayaran'" :back-route="route('admin.loket-pembayaran.index')">

                @php
                    $urlAction = isset($loketPembayaran)
                        ? route('admin.loket-pembayaran.update', $loketPembayaran->id)
                        : route('admin.loket-pembayaran.store');
                    $methodForm = isset($loketPembayaran) ? 'PUT' : 'POST';
                @endphp

                <form id="needs-validation" novalidate method="post" data-parsley-validate
                    action="{{ $urlAction }}">
                    @csrf
                    @method($methodForm)

                    <!-- Card Body -->
                    <div class="px-6 py-6">
                        <x-admin.form-errors />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Loket <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('name') border-red-500 @enderror"
                                    id="name" name="name" data-parsley-required
                                    value="{{ old('name', $loketPembayaran->name ?? '') }}"
                                    placeholder="Contoh: Bank BRI Cabang Utama" />
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tipe -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipe Loket <span class="text-red-500">*</span>
                                </label>
                                <select
                                    class="select2 w-full @error('type') border-red-500 @enderror"
                                    id="type" name="type" data-parsley-required>
                                    <option value="">Pilih Tipe</option>
                                    @foreach ($types as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('type', $loketPembayaran->type->value ?? '') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Alamat -->
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Alamat
                                </label>
                                <textarea
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('address') border-red-500 @enderror"
                                    id="address" name="address" rows="2" placeholder="Alamat lengkap loket...">{{ old('address', $loketPembayaran->address ?? '') }}</textarea>
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Telepon -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Telepon
                                </label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('phone') border-red-500 @enderror"
                                    id="phone" name="phone"
                                    value="{{ old('phone', $loketPembayaran->phone ?? '') }}"
                                    placeholder="Contoh: 021-12345678" />
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jam Operasional -->
                            <div>
                                <label for="operational_hours" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jam Operasional
                                </label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('operational_hours') border-red-500 @enderror"
                                    id="operational_hours" name="operational_hours"
                                    value="{{ old('operational_hours', $loketPembayaran->operational_hours ?? '') }}"
                                    placeholder="Contoh: Senin-Jumat 08:00-16:00" />
                                @error('operational_hours')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Koordinat -->
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">
                                    Latitude
                                </label>
                                <input type="number" step="any"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('latitude') border-red-500 @enderror"
                                    id="latitude" name="latitude"
                                    value="{{ old('latitude', $loketPembayaran->latitude ?? '') }}"
                                    placeholder="-2.9569" />
                                @error('latitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">
                                    Longitude
                                </label>
                                <input type="number" step="any"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('longitude') border-red-500 @enderror"
                                    id="longitude" name="longitude"
                                    value="{{ old('longitude', $loketPembayaran->longitude ?? '') }}"
                                    placeholder="119.9120" />
                                @error('longitude')
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
                                    value="{{ old('display_order', $loketPembayaran->display_order ?? 0) }}"
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
                                        {{ old('is_active', isset($loketPembayaran) ? $loketPembayaran->is_active : true) ? 'checked' : '' }} />
                                    <div
                                        class="relative h-6 w-11 rounded-full bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all">
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Map Preview -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preview Lokasi</label>
                            <div id="map-preview" class="h-64 rounded-lg border border-gray-300 bg-gray-100"></div>
                            <p class="mt-1 text-sm text-gray-500">Klik pada peta untuk mengatur koordinat, atau isi
                                latitude/longitude secara manual.</p>
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
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('js')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
                // Default center (can be customized)
                const defaultLat = {{ old('latitude', $loketPembayaran->latitude ?? -2.9569) }};
                const defaultLng = {{ old('longitude', $loketPembayaran->longitude ?? 119.9120) }};

                const map = L.map('map-preview').setView([defaultLat, defaultLng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                let marker = L.marker([defaultLat, defaultLng], {
                    draggable: true
                }).addTo(map);

                // Update form fields when marker is dragged
                marker.on('dragend', function(e) {
                    const latlng = marker.getLatLng();
                    $('#latitude').val(latlng.lat.toFixed(8));
                    $('#longitude').val(latlng.lng.toFixed(8));
                });

                // Update marker when clicking on map
                map.on('click', function(e) {
                    marker.setLatLng(e.latlng);
                    $('#latitude').val(e.latlng.lat.toFixed(8));
                    $('#longitude').val(e.latlng.lng.toFixed(8));
                });

                // Update marker when form fields change
                $('#latitude, #longitude').on('change', function() {
                    const lat = parseFloat($('#latitude').val()) || defaultLat;
                    const lng = parseFloat($('#longitude').val()) || defaultLng;
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 13);
                });
            });
        </script>
    @endpush
</x-layouts.auth>
