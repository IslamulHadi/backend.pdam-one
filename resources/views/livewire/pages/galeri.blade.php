<?php

declare(strict_types=1);

use App\Enums\GalleryType;
use App\Models\Gallery;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.public')] #[Title('Galeri')] class extends Component {
    use WithPagination;

    #[Url]
    public string $type = '';

    #[Url]
    public string $search = '';

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        $hasFilters = $this->type !== '' || $this->search !== '';

        $query = Gallery::query()
            ->active()
            ->ordered()
            ->when($this->type, fn ($q) => $q->byType($this->type))
            ->when($this->search, fn ($q) => $q->where('title', 'like', '%' . $this->search . '%'));

        return [
            'galleries' => $hasFilters ? $query->paginate(12) : Cache::remember('page.galeri.' . request('page', 1), 1800, fn () => $query->paginate(12)),
            'types' => GalleryType::toArray(),
        ];
    }
}; ?>

<div>
    <!-- Hero Section -->
    <section class="pdam-page-hero">
        <div class="pdam-container">
            <h1 class="pdam-page-hero-title">Galeri</h1>
            <p class="pdam-page-hero-subtitle">
                Dokumentasi kegiatan dan aktivitas PDAM dalam melayani masyarakat.
            </p>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="pdam-section" style="padding-top: 2rem; padding-bottom: 1rem;">
        <div class="pdam-container">
            <div class="pdam-filter-bar">
                <div class="pdam-filter-group">
                    <label class="pdam-filter-label">Filter:</label>
                    <select wire:model.live="type" class="pdam-select">
                        <option value="">Semua Tipe</option>
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pdam-search-box">
                    <input type="text" wire:model.live.debounce.300ms="search" class="pdam-input"
                        placeholder="Cari galeri...">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="pdam-search-icon">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Grid -->
    <section class="pdam-section" style="padding-top: 1rem;">
        <div class="pdam-container">
            <div wire:loading.class="opacity-50" class="transition-opacity duration-300">
                @if ($galleries->count() > 0)
                    <div class="pdam-gallery-grid">
                        @foreach ($galleries as $gallery)
                            <div class="pdam-gallery-card" x-data="{ open: false }">
                                <div class="pdam-gallery-card-image" @click="open = true"
                                    style="cursor: pointer;">
                                    @if ($gallery->type === \App\Enums\GalleryType::Video)
                                        <div class="pdam-gallery-video-overlay">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M8 5v14l11-7z" />
                                            </svg>
                                        </div>
                                    @endif
                                    @if ($gallery->getFirstMediaUrl('thumbnail'))
                                        <img src="{{ $gallery->getFirstMediaUrl('thumbnail') }}"
                                            alt="{{ $gallery->title }}" class="pdam-gallery-img">
                                    @elseif($gallery->getFirstMediaUrl('images'))
                                        <img src="{{ $gallery->getFirstMediaUrl('images') }}"
                                            alt="{{ $gallery->title }}" class="pdam-gallery-img">
                                    @else
                                        <div class="pdam-gallery-placeholder">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="3" width="18" height="18" rx="2"
                                                    ry="2"></rect>
                                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                <polyline points="21 15 16 10 5 21"></polyline>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="pdam-gallery-card-content">
                                    <span
                                        class="pdam-gallery-badge {{ $gallery->type === \App\Enums\GalleryType::Video ? 'pdam-badge-purple' : 'pdam-badge-blue' }}">
                                        {{ $gallery->type->getLabel() }}
                                    </span>
                                    <h3 class="pdam-gallery-title">{{ $gallery->title }}</h3>
                                    @if ($gallery->description)
                                        <p class="pdam-gallery-desc">{{ Str::limit($gallery->description, 80) }}</p>
                                    @endif
                                    @if ($gallery->type === \App\Enums\GalleryType::Foto && $gallery->getMedia('images')->count() > 1)
                                        <span class="pdam-gallery-count">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="3" width="18" height="18" rx="2"
                                                    ry="2"></rect>
                                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                <polyline points="21 15 16 10 5 21"></polyline>
                                            </svg>
                                            {{ $gallery->getMedia('images')->count() }} foto
                                        </span>
                                    @endif
                                </div>

                                <!-- Lightbox Modal -->
                                <div x-show="open" x-cloak
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/80"
                                    @click.self="open = false" @keydown.escape.window="open = false">
                                    <button @click="open = false"
                                        class="absolute top-4 right-4 text-white hover:text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                    <div class="max-w-4xl max-h-[90vh] overflow-auto p-4">
                                        @if ($gallery->type === \App\Enums\GalleryType::Video && $gallery->video_url)
                                            @php
                                                $videoId = '';
                                                if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $gallery->video_url, $matches)) {
                                                    $videoId = $matches[1];
                                                } elseif (preg_match('/youtu\.be\/([^?]+)/', $gallery->video_url, $matches)) {
                                                    $videoId = $matches[1];
                                                }
                                            @endphp
                                            @if ($videoId)
                                                <div class="aspect-video">
                                                    <iframe
                                                        src="https://www.youtube.com/embed/{{ $videoId }}?autoplay=1"
                                                        class="w-full h-full rounded-lg" frameborder="0"
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                        allowfullscreen></iframe>
                                                </div>
                                            @endif
                                        @else
                                            <div x-data="{ currentIndex: 0, images: {{ json_encode($gallery->getMedia('images')->map(fn($m) => $m->getUrl())->toArray()) }} }">
                                                <img :src="images[currentIndex]" alt="{{ $gallery->title }}"
                                                    class="max-w-full max-h-[70vh] object-contain mx-auto rounded-lg">
                                                <div class="flex justify-center items-center gap-4 mt-4"
                                                    x-show="images.length > 1">
                                                    <button @click="currentIndex = (currentIndex - 1 + images.length) % images.length"
                                                        class="text-white bg-white/20 hover:bg-white/30 rounded-full p-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="15 18 9 12 15 6"></polyline>
                                                        </svg>
                                                    </button>
                                                    <span class="text-white" x-text="`${currentIndex + 1} / ${images.length}`"></span>
                                                    <button @click="currentIndex = (currentIndex + 1) % images.length"
                                                        class="text-white bg-white/20 hover:bg-white/30 rounded-full p-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="9 18 15 12 9 6"></polyline>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                        <h3 class="text-white text-xl font-semibold mt-4 text-center">
                                            {{ $gallery->title }}</h3>
                                        @if ($gallery->description)
                                            <p class="text-gray-300 text-center mt-2">{{ $gallery->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="pdam-pagination-wrapper mt-8">
                        {{ $galleries->links() }}
                    </div>
                @else
                    <div class="pdam-empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"
                            stroke-linejoin="round" class="pdam-empty-icon">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        <h3 class="pdam-empty-title">Belum Ada Galeri</h3>
                        <p class="pdam-empty-text">
                            @if ($search || $type)
                                Tidak ditemukan galeri yang sesuai dengan filter Anda.
                            @else
                                Galeri foto dan video akan ditampilkan di sini.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
