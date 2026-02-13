<x-layouts.auth title="Detail Pesan Kontak">
    <div class="container-xxl flex-grow-1 container-p-y">
        <flux:breadcrumbs class="py-3 mb-4">
            <flux:breadcrumbs.item href="{{ route('admin.pesan-kontak.index') }}">Pesan Kontak</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Detail Pesan</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="w-full">
            <!-- Message Detail Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h5 class="text-lg font-semibold text-gray-900 mb-0">Detail Pesan</h5>
                    <a href="{{ route('admin.pesan-kontak.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-red-custom text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                        <i class="bx bx-arrow-back mr-2"></i>
                        Kembali
                    </a>
                </div>

                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Pengirim -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Nama Pengirim</label>
                            <p class="text-gray-900 font-medium">{{ $contactMessage->name }}</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                            <a href="mailto:{{ $contactMessage->email }}" class="text-blue-600 hover:underline">
                                {{ $contactMessage->email }}
                            </a>
                        </div>

                        <!-- Telepon -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">No. Telepon / WA</label>
                            <p class="text-gray-900">{{ $contactMessage->phone ?: '-' }}</p>
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Kategori</label>
                            @php
                                $categoryColors = [
                                    'umum' => 'bg-blue-500',
                                    'layanan' => 'bg-green-500',
                                    'tagihan' => 'bg-yellow-500',
                                    'kerjasama' => 'bg-purple-500',
                                    'lainnya' => 'bg-gray-500',
                                ];
                                $color = $categoryColors[$contactMessage->category] ?? 'bg-gray-500';
                            @endphp
                            <span class="badge {{ $color }} text-white px-2 py-1 rounded">
                                {{ ucfirst($contactMessage->category) }}
                            </span>
                        </div>

                        <!-- Tanggal -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Dikirim</label>
                            <p class="text-gray-900">{{ $contactMessage->created_at->format('d F Y, H:i') }}</p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                            @if ($contactMessage->responded_at)
                                <span class="badge bg-green-500 text-white px-2 py-1 rounded">Direspon</span>
                            @elseif ($contactMessage->is_read)
                                <span class="badge bg-yellow-500 text-white px-2 py-1 rounded">Dibaca</span>
                            @else
                                <span class="badge bg-red-500 text-white px-2 py-1 rounded">Baru</span>
                            @endif
                        </div>
                    </div>

                    <!-- Isi Pesan -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-500 mb-2">Isi Pesan</label>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $contactMessage->message }}</p>
                        </div>
                    </div>

                    <!-- Existing Response -->
                    @if ($contactMessage->responded_at)
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-500 mb-2">Balasan</label>
                            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $contactMessage->response }}</p>
                                <div class="mt-3 text-sm text-gray-500">
                                    Direspon oleh
                                    <strong>{{ $contactMessage->respondedByUser?->name ?? 'Unknown' }}</strong>
                                    pada {{ $contactMessage->responded_at->format('d F Y, H:i') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Response Form -->
            @if (! $contactMessage->responded_at)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-semibold text-gray-900 mb-0">Kirim Balasan</h5>
                    </div>

                    <form method="post" action="{{ route('admin.pesan-kontak.respond', $contactMessage->id) }}">
                        @csrf

                        <div class="px-6 py-6">
                            @if ($errors->any())
                                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div>
                                <label for="response" class="block text-sm font-medium text-gray-700 mb-2">
                                    Balasan <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('response') border-red-500 @enderror"
                                    id="response" name="response" rows="5"
                                    placeholder="Tulis balasan untuk pesan ini...">{{ old('response') }}</textarea>
                                @error('response')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                            <div class="flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-custom text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    <i class="bx bx-send mr-2"></i>
                                    Kirim Balasan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-layouts.auth>
