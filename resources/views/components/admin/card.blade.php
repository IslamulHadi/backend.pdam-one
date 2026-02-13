@props([
    'title' => '',
    'backRoute' => '',
    'backLabel' => 'Kembali',
])

<div class="w-full">
    <div class="mb-6 rounded-lg border border-gray-200 bg-white shadow-sm">
        {{-- Card Header --}}
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <h5 class="mb-0 text-lg font-semibold text-gray-900">{{ $title }}</h5>
            @if ($backRoute)
                <a href="{{ $backRoute }}"
                    class="inline-flex items-center rounded-md bg-red-custom px-4 py-2 text-sm font-medium text-white transition-colors duration-200 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    <i class="bx bx-arrow-back mr-2"></i>
                    {{ $backLabel }}
                </a>
            @endif
        </div>

        {{-- Card Content (form, body, footer, etc.) --}}
        {{ $slot }}
    </div>
</div>
