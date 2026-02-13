<?php

declare(strict_types=1);

use App\Enums\LoketType;
use App\Models\LoketPembayaran;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] #[Title('Lokasi Pembayaran')] class extends Component {
    #[Url]
    public string $type = '';

    #[Url]
    public string $search = '';

    public function with(): array
    {
        $hasFilters = $this->type !== '' || $this->search !== '';

        $query = LoketPembayaran::query()
            ->active()
            ->ordered()
            ->when($this->type, fn ($q) => $q->byType($this->type))
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('address', 'like', '%' . $this->search . '%'));

        $mapCacheKey = 'loket.map' . ($this->type ? '.' . $this->type : '');
        $loketWithCoordinates = Cache::remember($mapCacheKey, 3600, fn () => LoketPembayaran::active()
            ->withCoordinates()
            ->when($this->type, fn ($q) => $q->byType($this->type))
            ->get());

        return [
            'loketList' => $hasFilters ? $query->get() : Cache::remember('page.loket', 1800, fn () => $query->get()),
            'types' => LoketType::toArray(),
            'loketMapData' => $loketWithCoordinates->map(fn ($l) => [
                'id' => $l->id,
                'name' => $l->name,
                'type' => $l->type->getLabel(),
                'address' => $l->address,
                'lat' => (float) $l->latitude,
                'lng' => (float) $l->longitude,
            ])->toArray(),
        ];
    }
}; ?>

<div class="min-h-screen bg-slate-50">
    <!-- Page Header -->
    <section class="pdam-page-header">
        <div class="pdam-container">
            <h1 class="pdam-page-title text-white">Lokasi Pembayaran</h1>
            <p class="pdam-page-subtitle">
                Temukan lokasi pembayaran tagihan PDAM terdekat dari Anda.
            </p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-8 md:py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Map Section -->
            <!-- Map Container -->
            <div class="mb-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg">
                <div id="payment-map" wire:ignore style="height: 400px; width: 100%; background: #e2e8f0;"></div>
            </div>

            @assets
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
                integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
                crossorigin="" />
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
                crossorigin=""></script>
            <style>
                #payment-map .leaflet-tile-pane { z-index: 1; }
                .leaflet-popup-content-wrapper { border-radius: 12px !important; }
                .leaflet-popup-content { margin: 12px 16px !important; font-family: 'Inter', system-ui, sans-serif; }
            </style>
            @endassets

            @script
            <script>
                (function() {
                    const mapContainer = document.getElementById('payment-map');
                    if (!mapContainer || mapContainer._leaflet_id) return;

                    const mapData = @json($loketMapData);

                    // Default center - Indonesia
                    let centerLat = -6.2088;
                    let centerLng = 106.8456;
                    let defaultZoom = 5;

                    if (mapData && mapData.length > 0) {
                        centerLat = mapData[0].lat;
                        centerLng = mapData[0].lng;
                        defaultZoom = 13;
                    }

                    const map = L.map('payment-map').setView([centerLat, centerLng], defaultZoom);

                    // OpenStreetMap tiles
                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);

                    // Add markers
                    if (mapData && mapData.length > 0) {
                        const markers = [];
                        mapData.forEach(function(loket) {
                            const marker = L.marker([loket.lat, loket.lng]).addTo(map);
                            markers.push(marker);
                            marker.bindPopup(`
                                <div style="min-width: 200px; line-height: 1.5;">
                                    <div style="font-weight: 700; font-size: 14px; color: #1e293b; margin-bottom: 4px;">${loket.name}</div>
                                    <div style="display: inline-block; background: #e0f2fe; color: #0369a1; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 9999px; margin-bottom: 8px;">${loket.type}</div>
                                    ${loket.address ? `<p style="font-size: 13px; color: #64748b; margin: 0 0 10px 0;">${loket.address}</p>` : ''}
                                    <a href="https://www.google.com/maps?q=${loket.lat},${loket.lng}" target="_blank"
                                       style="color: #0284c7; font-size: 13px; font-weight: 600; text-decoration: none;">
                                       Buka di Google Maps &rarr;
                                    </a>
                                </div>
                            `);
                        });

                        // Fit bounds if multiple markers
                        if (markers.length > 1) {
                            const group = L.featureGroup(markers);
                            map.fitBounds(group.getBounds().pad(0.1));
                        }
                    }

                    // Force redraw
                    setTimeout(() => map.invalidateSize(), 250);
                })();
            </script>
            @endscript

            <!-- Filter & Search Bar -->
            <div class="mb-8 flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between md:p-5">
                <!-- Type Filter -->
                <div class="flex items-center gap-3">
                    <label for="type-filter" class="text-sm font-medium text-slate-600 whitespace-nowrap">Tipe Lokasi:</label>
                    {{-- <select wire:model.live="type" id="type-filter"
                        class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700 shadow-sm transition focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-400/20">
                        <option value="">Semua Tipe</option>
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select> --}}
                    <x-select2 name="type" wireModel="type" :options="$types" placeholder="Semua Tipe" :value="$type" />
                </div>

                <!-- Search Field -->
                <div class="relative w-full md:max-w-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 placeholder-slate-400 shadow-sm transition focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-400/20"
                        placeholder="Cari nama atau alamat...">
                </div>
            </div>

            <!-- Results Count -->
            @if ($loketList->count() > 0)
                <div class="mb-4 text-sm text-slate-500">
                    Menampilkan <span class="font-semibold text-slate-700">{{ $loketList->count() }}</span> lokasi pembayaran
                    @if ($type || $search)
                        <span class="text-slate-400">
                            @if ($type)
                                &middot; Tipe: <span class="font-medium text-slate-600">{{ $types[$type] ?? $type }}</span>
                            @endif
                            @if ($search)
                                &middot; Pencarian: "<span class="font-medium text-slate-600">{{ $search }}</span>"
                            @endif
                        </span>
                    @endif
                </div>
            @endif

            <!-- Loket Grid -->
            <div wire:loading.class="opacity-50" class="transition-opacity duration-300">
                @if ($loketList->count() > 0)
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($loketList as $loket)
                            @php
                                $typeStyles = [
                                    'bank' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
                                    'minimarket' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'],
                                    'kantor_pdam' => ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-700', 'border' => 'border-cyan-200'],
                                    'online' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'border' => 'border-purple-200'],
                                    'ppob' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'border' => 'border-orange-200'],
                                ];
                                $style = $typeStyles[$loket->type->value] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'border' => 'border-slate-200'];
                            @endphp

                            <article wire:key="loket-{{ $loket->id }}"
                                class="group flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                                <!-- Card Header with Badge -->
                                <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-3">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $style['bg'] }} {{ $style['text'] }}">
                                        {{ $loket->type->getLabel() }}
                                    </span>
                                </div>

                                <!-- Card Body -->
                                <div class="flex flex-1 flex-col p-5">
                                    <h3 class="mb-3 font-display text-lg font-bold text-slate-800 group-hover:text-sky-600 transition-colors">
                                        {{ $loket->name }}
                                    </h3>

                                    <div class="flex-1 space-y-2.5">
                                        @if ($loket->address)
                                            <div class="flex items-start gap-2.5 text-sm text-slate-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="mt-0.5 shrink-0 text-slate-400">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                    <circle cx="12" cy="10" r="3"></circle>
                                                </svg>
                                                <span>{{ $loket->address }}</span>
                                            </div>
                                        @endif

                                        @if ($loket->phone)
                                            <div class="flex items-center gap-2.5 text-sm text-slate-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="shrink-0 text-slate-400">
                                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                                </svg>
                                                <span>{{ $loket->phone }}</span>
                                            </div>
                                        @endif

                                        @if ($loket->operational_hours)
                                            <div class="flex items-center gap-2.5 text-sm text-slate-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="shrink-0 text-slate-400">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                                <span>{{ $loket->operational_hours }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Google Maps Link -->
                                    @if ($loket->hasCoordinates())
                                        <div class="mt-4 border-t border-slate-100 pt-4">
                                            <a href="{{ $loket->google_maps_url }}" target="_blank"
                                                class="inline-flex items-center gap-2 text-sm font-semibold text-sky-600 transition-colors hover:text-sky-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polygon points="3 11 22 2 13 21 11 13 3 11"></polygon>
                                                </svg>
                                                Buka di Google Maps
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                                    <polyline points="15 3 21 3 21 9"></polyline>
                                                    <line x1="10" y1="14" x2="21" y2="3"></line>
                                                </svg>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="rounded-xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                        <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-100">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="text-slate-400">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                        <h3 class="mb-2 font-display text-lg font-semibold text-slate-700">Tidak Ditemukan</h3>
                        <p class="text-sm text-slate-500">
                            @if ($search || $type)
                                Tidak ditemukan loket pembayaran yang sesuai dengan filter Anda.
                                <br>
                                <button wire:click="$set('search', ''); $set('type', '')"
                                    class="mt-3 inline-flex items-center gap-1 text-sky-600 hover:text-sky-700 font-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                        <path d="M3 3v5h5"></path>
                                    </svg>
                                    Reset filter
                                </button>
                            @else
                                Belum ada data loket pembayaran yang tersedia.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>

