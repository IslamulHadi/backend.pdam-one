<?php

declare(strict_types=1);

use App\Models\Berita;
use App\Models\Kategori;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.public')] #[Title('Berita')] class extends Component {
    use WithPagination;

    public string $search = '';

    public array $selectedCategories = [];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setCategory(string $category): void
    {
        $index = array_search($category, $this->selectedCategories, true);

        if ($index !== false) {
            unset($this->selectedCategories[$index]);
            $this->selectedCategories = array_values($this->selectedCategories);
        } else {
            $this->selectedCategories[] = $category;
        }

        $this->resetPage();
    }

    public function with(): array
    {
        $query = Berita::published()->with('kategori')->latest('published_at');

        if (!empty($this->search)) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->selectedCategories)) {
            $query->whereHas('kategori', function ($query) {
                $query->whereIn('nama', $this->selectedCategories);
            });
        }

        return [
            'featuredNews' => Cache::remember('berita.featured', 600, fn () => Berita::published()->with('kategori')->featured()->latest('published_at')->first()),
            'news' => $query->paginate(6),
            'categories' => Cache::remember('berita.categories', 3600, fn () => Kategori::all()->pluck('nama', 'id')),
        ];
    }
}; ?>

<div class="min-h-screen bg-slate-50">
    <!-- Page Header -->

    <section class="pdam-page-header">
        <div class="pdam-container">
            <h1 class="pdam-page-title text-white">Berita & Informasi</h1>
            <p class="pdam-page-subtitle">
                Dapatkan informasi terkini mengenai layanan air bersih, pemeliharaan jaringan, dan kegiatan perusahaan
                terbaru.
            </p>
        </div>
    </section>

    <!-- News Section -->
    <section class="py-10 md:py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Featured News -->
            @if ($featuredNews && empty($selectedCategories) && empty($search))
                <div class="mb-10 grid gap-6 overflow-hidden rounded-2xl bg-white shadow-lg lg:grid-cols-2">
                    <div class="relative h-64 lg:h-auto">
                        @if ($featuredNews->getFirstMediaUrl('thumbnails'))
                            <img src="{{ $featuredNews->getFirstMediaUrl('thumbnails') }}"
                                alt="{{ $featuredNews->title }}" class="h-full w-full object-cover">
                        @else
                            <div
                                class="flex h-full w-full items-center justify-center bg-primary">
                                <img src="{{ asset('pdam.png') }}" alt="Default Image"
                                    class="h-20 w-20 object-contain opacity-50">
                            </div>
                        @endif
                        <div class="absolute left-4 top-4">
                            <span class="rounded-full bg-amber-400 px-3 py-1 text-xs font-semibold text-amber-900">
                                Featured
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col justify-center p-6 lg:p-8">
                        <div class="mb-3 flex flex-wrap items-center gap-2">
                            @if ($featuredNews->kategori->count() > 0)
                                @foreach ($featuredNews->kategori as $item)
                                    <span
                                        class="rounded-full bg-sky-100 px-3 py-1 text-xs font-medium text-sky-700">{{ $item->nama }}</span>
                                @endforeach
                            @endif
                            <span
                                class="text-sm text-slate-500">{{ $featuredNews->published_at->translatedFormat('d F Y') }}</span>
                        </div>
                        <h2 class="mb-3 font-display text-xl font-bold text-slate-800 lg:text-2xl">
                            <a href="{{ route('berita.show', $featuredNews->slug) }}"
                                class="transition-colors hover:text-sky-600">{{ $featuredNews->title }}</a>
                        </h2>
                        <p class="mb-4 text-slate-600">{{ Str::limit($featuredNews->excerpt, 200) }}</p>
                        <a href="{{ route('berita.show', $featuredNews->slug) }}"
                            class="inline-flex items-center gap-1 font-semibold text-sky-600 transition-colors hover:text-sky-700">
                            Baca Selengkapnya
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Search and Category Filter -->
            <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <!-- Search Field -->
                <div class="relative w-full md:max-w-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-10 pr-4 text-sm text-slate-700 placeholder-slate-400 shadow-sm transition focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-400/20"
                        placeholder="Cari berita berdasarkan judul...">
                </div>
                <!-- Category Filter -->
                <div class="flex flex-wrap gap-2">
                    @foreach ($categories as $key => $label)
                        <button wire:click="setCategory('{{ $label }}')"
                            class="rounded-full border px-4 py-2 text-sm font-medium transition {{ in_array($label, $selectedCategories, true) ? 'border-sky-500 bg-sky-500 text-white shadow-md' : 'border-slate-200 bg-white text-slate-600 hover:border-sky-300 hover:bg-sky-50' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- News Grid -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($news as $key => $item)
                    <article wire:key="news-{{ $item->id }}"
                        class="group overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="relative h-48 overflow-hidden bg-slate-100">
                            @if ($item->getFirstMediaUrl('thumbnails'))
                                <img src="{{ $item->getFirstMediaUrl('thumbnails') }}" alt="{{ $item->title }}"
                                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                            @else
                                <div
                                    class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                                    <img src="{{ asset('pdam.png') }}" alt="Default Image"
                                        class="h-16 w-16 object-contain opacity-30">
                                </div>
                            @endif
                        </div>
                        <div class="p-5">
                            <div class="mb-2 flex flex-wrap items-center gap-2">
                                @if ($item->kategori->count() > 0)
                                    @foreach ($item->kategori as $kategori)
                                        <span
                                            class="rounded-full bg-sky-100 px-2.5 py-0.5 text-xs font-medium text-sky-700">{{ $kategori->nama }}</span>
                                    @endforeach
                                @endif
                            </div>
                            <span
                                class="text-xs text-slate-500">{{ $item->published_at?->translatedFormat('d M Y') }}</span>
                            <h3 class="mb-2 mt-1 line-clamp-2 font-display text-lg font-bold text-slate-800">
                                <a href="{{ route('berita.show', $item->slug) }}"
                                    class="transition-colors hover:text-sky-600">{{ $item->title }}</a>
                            </h3>
                            <p class="mb-4 line-clamp-2 text-sm text-slate-600">{{ Str::limit($item->excerpt, 100) }}
                            </p>
                            <a href="{{ route('berita.show', $item->slug) }}"
                                class="inline-flex items-center gap-1 text-sm font-semibold text-sky-600 transition-colors hover:text-sky-700">
                                Baca Selengkapnya
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" class="mx-auto mb-4 text-slate-300">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        <p class="text-slate-500">
                            @if (!empty($search) || !empty($selectedCategories))
                                Tidak ada berita yang ditemukan untuk pencarian atau filter yang dipilih.
                            @else
                                Belum ada berita untuk kategori ini.
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($news->hasPages())
                <div class="mt-10 flex items-center justify-center gap-2">
                    @if ($news->onFirstPage())
                        <span
                            class="flex h-10 w-10 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                        </span>
                    @else
                        <button wire:click="previousPage"
                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:border-sky-400 hover:text-sky-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                        </button>
                    @endif

                    @foreach ($news->getUrlRange(1, $news->lastPage()) as $page => $url)
                        <button wire:click="gotoPage({{ $page }})"
                            class="flex h-10 w-10 items-center justify-center rounded-lg border text-sm font-medium transition {{ $page === $news->currentPage() ? 'border-sky-500 bg-sky-500 text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-sky-400 hover:text-sky-600' }}">
                            {{ $page }}
                        </button>
                    @endforeach

                    @if ($news->hasMorePages())
                        <button wire:click="nextPage"
                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:border-sky-400 hover:text-sky-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </button>
                    @else
                        <span
                            class="flex h-10 w-10 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </section>
</div>
