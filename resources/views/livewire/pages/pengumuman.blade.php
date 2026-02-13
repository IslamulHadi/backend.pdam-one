<?php

declare(strict_types=1);

use App\Enums\PriorityLevel;
use App\Models\Pengumuman;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.public')] #[Title('Pengumuman')] class extends Component {
    use WithPagination;

    #[Url]
    public string $priority = '';

    #[Url]
    public string $search = '';

    public function updatedPriority(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        $hasFilters = $this->priority !== '' || $this->search !== '';

        $query = Pengumuman::query()
            ->published()
            ->ordered()
            ->when($this->priority, fn ($q) => $q->byPriority($this->priority))
            ->when($this->search, fn ($q) => $q->where('title', 'like', '%' . $this->search . '%'));

        return [
            'pengumumanList' => $hasFilters ? $query->paginate(10) : Cache::remember('page.pengumuman.' . request('page', 1), 1800, fn () => $query->paginate(10)),
            'priorities' => PriorityLevel::toArray(),
        ];
    }
}; ?>

<div>
    <!-- Hero Section -->
    <section class="pdam-page-hero">
        <div class="pdam-container">
            <h1 class="pdam-page-hero-title">Pengumuman</h1>
            <p class="pdam-page-hero-subtitle">
                Informasi penting dan pengumuman resmi dari PDAM untuk masyarakat.
            </p>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="pdam-section" style="padding-top: 2rem; padding-bottom: 1rem;">
        <div class="pdam-container">
            <div class="pdam-filter-bar">
                <div class="pdam-filter-group">
                    <label class="pdam-filter-label">Prioritas:</label>
                    <select wire:model.live="priority" class="pdam-select">
                        <option value="">Semua Prioritas</option>
                        @foreach ($priorities as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pdam-search-box">
                    <input type="text" wire:model.live.debounce.300ms="search" class="pdam-input"
                        placeholder="Cari pengumuman...">
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

    <!-- Pengumuman List -->
    <section class="pdam-section" style="padding-top: 1rem;">
        <div class="pdam-container">
            <div wire:loading.class="opacity-50" class="transition-opacity duration-300">
                @if ($pengumumanList->count() > 0)
                    <div class="pdam-announcement-list">
                        @foreach ($pengumumanList as $pengumuman)
                            <article class="pdam-announcement-card">
                                <div class="pdam-announcement-header">
                                    <div class="pdam-announcement-meta">
                                        @php
                                            $priorityColors = [
                                                'tinggi' => 'pdam-badge-red',
                                                'sedang' => 'pdam-badge-yellow',
                                                'rendah' => 'pdam-badge-blue',
                                            ];
                                        @endphp
                                        <span
                                            class="pdam-announcement-badge {{ $priorityColors[$pengumuman->priority->value] ?? 'pdam-badge-gray' }}">
                                            {{ $pengumuman->priority->getLabel() }}
                                        </span>
                                        <span class="pdam-announcement-date">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="4" width="18" height="18" rx="2"
                                                    ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                            {{ $pengumuman->created_at->translatedFormat('d F Y') }}
                                        </span>
                                    </div>
                                </div>
                                <h2 class="pdam-announcement-title">
                                    <a href="{{ route('pengumuman.show', $pengumuman->slug) }}">
                                        {{ $pengumuman->title }}
                                    </a>
                                </h2>
                                <p class="pdam-announcement-excerpt">
                                    {{ $pengumuman->excerpt ?? Str::limit(strip_tags($pengumuman->content), 200) }}
                                </p>
                                <a href="{{ route('pengumuman.show', $pengumuman->slug) }}"
                                    class="pdam-announcement-link">
                                    Baca Selengkapnya
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>
                            </article>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="pdam-pagination-wrapper mt-8">
                        {{ $pengumumanList->links() }}
                    </div>
                @else
                    <div class="pdam-empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"
                            stroke-linejoin="round" class="pdam-empty-icon">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                        <h3 class="pdam-empty-title">Tidak Ada Pengumuman</h3>
                        <p class="pdam-empty-text">
                            @if ($search || $priority)
                                Tidak ditemukan pengumuman yang sesuai dengan filter Anda.
                            @else
                                Belum ada pengumuman yang dipublikasikan.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
