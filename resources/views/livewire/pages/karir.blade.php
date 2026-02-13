<?php

declare(strict_types=1);

use App\Enums\EmploymentType;
use App\Enums\LowonganStatus;
use App\Models\Lowongan;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] #[Title('Karir')] class extends Component {
    #[Url]
    public string $type = '';

    #[Url]
    public string $search = '';

    public function with(): array
    {
        $hasFilters = $this->type !== '' || $this->search !== '';

        $query = Lowongan::query()
            ->active()
            ->open()
            ->notExpired()
            ->ordered()
            ->when($this->type, fn ($q) => $q->byType($this->type))
            ->when($this->search, fn ($q) => $q->where('title', 'like', '%' . $this->search . '%')
                ->orWhere('department', 'like', '%' . $this->search . '%'));

        return [
            'lowongan' => $hasFilters ? $query->get() : Cache::remember('page.karir', 1800, fn () => $query->get()),
            'employmentTypes' => EmploymentType::toArray(),
        ];
    }
}; ?>

<div>
    <!-- Hero Section -->
    <section class="pdam-page-hero">
        <div class="pdam-container">
            <h1 class="pdam-page-hero-title text-white">Karir</h1>
            <p class="pdam-page-hero-subtitle">
                Bergabunglah bersama kami dalam memberikan pelayanan terbaik bagi masyarakat.
            </p>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="pdam-section" style="padding-top: 2rem; padding-bottom: 1rem;">
        <div class="pdam-container">
            <div class="pdam-filter-bar">
                <div class="pdam-filter-group">
                    <label class="pdam-filter-label">Jenis:</label>
                    <x-select2 name="type" wireModel="type" :options="$employmentTypes" placeholder="Semua Jenis" :value="$type" />
                </div>
                <div class="pdam-search-box">
                    <input type="text" wire:model.live.debounce.300ms="search" class="pdam-input"
                        placeholder="Cari posisi...">
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

    <!-- Job Listings -->
    <section class="pdam-section" style="padding-top: 1rem;">
        <div class="pdam-container">
            <div wire:loading.class="opacity-50" class="transition-opacity duration-300">
                @if ($lowongan->count() > 0)
                    <div class="pdam-job-list">
                        @foreach ($lowongan as $job)
                            <div class="pdam-job-card">
                                <div class="pdam-job-header">
                                    <div class="pdam-job-info">
                                        <h3 class="pdam-job-title">{{ $job->title }}</h3>
                                        @if ($job->department)
                                            <p class="pdam-job-department">{{ $job->department }}</p>
                                        @endif
                                    </div>
                                    @php
                                        $typeColors = [
                                            'full_time' => 'pdam-badge-blue',
                                            'contract' => 'pdam-badge-orange',
                                            'internship' => 'pdam-badge-green',
                                        ];
                                    @endphp
                                    <span
                                        class="pdam-job-badge {{ $typeColors[$job->employment_type->value] ?? 'pdam-badge-gray' }}">
                                        {{ $job->employment_type->getLabel() }}
                                    </span>
                                </div>

                                <div class="pdam-job-meta">
                                    <span class="pdam-job-meta-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                            <circle cx="12" cy="10" r="3"></circle>
                                        </svg>
                                        {{ $job->location }}
                                    </span>
                                    @if ($job->deadline)
                                        <span class="pdam-job-meta-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="4" width="18" height="18" rx="2"
                                                    ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                            Deadline: {{ $job->deadline->translatedFormat('d M Y') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="pdam-job-description">
                                    {!! Str::limit(strip_tags($job->description), 150) !!}
                                </div>

                                <div class="pdam-job-actions">
                                    <a href="{{ route('karir.detail', $job->slug) }}" class="p-1 pdam-btn-primary rounded-3">
                                        Lihat Detail
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14"></path>
                                            <path d="m12 5 7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="pdam-empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"
                            stroke-linejoin="round" class="pdam-empty-icon">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                        <h3 class="pdam-empty-title">Tidak Ada Lowongan</h3>
                        <p class="pdam-empty-text">
                            @if ($search || $type)
                                Tidak ditemukan lowongan yang sesuai dengan filter Anda.
                            @else
                                Saat ini belum ada lowongan yang tersedia. Silakan cek kembali nanti.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
