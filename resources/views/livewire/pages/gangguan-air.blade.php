<?php

declare(strict_types=1);

use App\Enums\GangguanStatus;
use App\Enums\SeverityLevel;
use App\Models\GangguanAir;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.public')] #[Title('Info Gangguan Air')] class extends Component {
    use WithPagination;

    #[Url]
    public string $status = '';

    #[Url]
    public string $severity = '';

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSeverity(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        $query = GangguanAir::query()
            ->ordered()
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->severity, fn($q) => $q->bySeverity($this->severity));

        $activeCount = GangguanAir::active()->count();

        return [
            'gangguanList' => $query->paginate(10),
            'statuses' => GangguanStatus::toArray(),
            'severities' => SeverityLevel::toArray(),
            'activeCount' => $activeCount,
        ];
    }
}; ?>

<div>
    <!-- Hero Section -->
    <section class="pdam-page-hero">
        <div class="pdam-container">
            <h1 class="pdam-page-hero-title text-white">Info Gangguan Air</h1>
            <p class="pdam-page-hero-subtitle">
                Informasi terkini mengenai gangguan distribusi air di wilayah layanan PDAM.
            </p>
        </div>
    </section>

    <!-- Active Alert Banner -->
    @if ($activeCount > 0)
        <section class="pdam-alert-banner">
            <div class="pdam-container">
                <div class="pdam-alert pdam-alert-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                        </path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <span>Terdapat <strong>{{ $activeCount }}</strong> gangguan air yang sedang berlangsung saat ini.
                    </span>
                </div>
            </div>
        </section>
    @endif

    <!-- Filter Section -->
    <section class="pdam-section" style="padding-top: 2rem; padding-bottom: 1rem;">
        <div class="pdam-container">
            <div class="pdam-filter-bar">
                <div class="pdam-filter-group">
                    <label class="pdam-filter-label">Status:</label>
                    <select wire:model.live="status" class="pdam-select">
                        <option value="">Semua Status</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pdam-filter-group">
                    <label class="pdam-filter-label">Tingkat:</label>
                    <select wire:model.live="severity" class="pdam-select">
                        <option value="">Semua Tingkat</option>
                        @foreach ($severities as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- Gangguan List -->
    <section class="pdam-section" style="padding-top: 1rem;">
        <div class="pdam-container">
            <div wire:loading.class="opacity-50" class="transition-opacity duration-300">
                @if ($gangguanList->count() > 0)
                    <div class="pdam-gangguan-list">
                        @foreach ($gangguanList as $gangguan)
                            <article
                                class="pdam-gangguan-card {{ $gangguan->isActive() ? 'pdam-gangguan-active' : 'pdam-gangguan-resolved' }}">
                                <div class="pdam-gangguan-header">
                                    <div class="pdam-gangguan-badges">
                                        @php
                                            $severityColors = [
                                                'ringan' => 'pdam-badge-green',
                                                'sedang' => 'pdam-badge-yellow',
                                                'berat' => 'pdam-badge-red',
                                            ];
                                        @endphp
                                        <span
                                            class="pdam-gangguan-badge {{ $severityColors[$gangguan->severity->value] ?? 'pdam-badge-gray' }}">
                                            {{ $gangguan->severity->getLabel() }}
                                        </span>
                                        <span
                                            class="pdam-gangguan-status {{ $gangguan->isActive() ? 'pdam-status-active' : 'pdam-status-resolved' }}">
                                            @if ($gangguan->isActive())
                                                <span class="pdam-pulse"></span>
                                            @endif
                                            {{ $gangguan->status->getLabel() }}
                                        </span>
                                    </div>
                                    <span class="pdam-gangguan-time">
                                        {{ $gangguan->start_datetime->translatedFormat('d M Y, H:i') }}
                                    </span>
                                </div>

                                <h2 class="pdam-gangguan-title">{{ $gangguan->title }}</h2>

                                <p class="pdam-gangguan-desc">{{ Str::limit($gangguan->description, 200) }}</p>

                                @if ($gangguan->affected_areas && count($gangguan->affected_areas) > 0)
                                    <div class="pdam-gangguan-areas">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                            <circle cx="12" cy="10" r="3"></circle>
                                        </svg>
                                        <span>Wilayah terdampak: {{ implode(', ', $gangguan->affected_areas) }}</span>
                                    </div>
                                @endif

                                <div class="pdam-gangguan-footer">
                                    <div class="pdam-gangguan-timeline">
                                        <div class="pdam-timeline-item">
                                            <span class="pdam-timeline-label">Mulai:</span>
                                            <span
                                                class="pdam-timeline-value">{{ $gangguan->start_datetime->translatedFormat('d M Y, H:i') }}</span>
                                        </div>
                                        @if ($gangguan->isResolved() && $gangguan->actual_end_datetime)
                                            <div class="pdam-timeline-item">
                                                <span class="pdam-timeline-label">Selesai:</span>
                                                <span
                                                    class="pdam-timeline-value">{{ $gangguan->actual_end_datetime->translatedFormat('d M Y, H:i') }}</span>
                                            </div>
                                        @elseif($gangguan->estimated_end_datetime)
                                            <div class="pdam-timeline-item">
                                                <span class="pdam-timeline-label">Est. Selesai:</span>
                                                <span
                                                    class="pdam-timeline-value">{{ $gangguan->estimated_end_datetime->translatedFormat('d M Y, H:i') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if ($gangguan->isResolved() && $gangguan->resolution_notes)
                                    <div class="pdam-gangguan-resolution">
                                        <strong>Catatan:</strong> {{ $gangguan->resolution_notes }}
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="pdam-pagination-wrapper mt-8">
                        {{ $gangguanList->links() }}
                    </div>
                @else
                    <div class="pdam-empty-state pdam-empty-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"
                            stroke-linejoin="round" class="pdam-empty-icon">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <h3 class="pdam-empty-title">Tidak Ada Gangguan</h3>
                        <p class="pdam-empty-text">
                            @if ($status || $severity)
                                Tidak ditemukan gangguan yang sesuai dengan filter Anda.
                            @else
                                Saat ini tidak ada gangguan distribusi air. Layanan berjalan normal.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Emergency Contact -->
    <section class="pdam-section" style="background: var(--pdam-gray-100);">
        <div class="pdam-container">
            <div class="pdam-emergency-box">
                <div class="pdam-emergency-content">
                    <h3 class="pdam-emergency-title">Laporkan Gangguan Air</h3>
                    <p class="pdam-emergency-text">
                        Jika Anda mengalami gangguan air di wilayah Anda yang belum tercatat, segera laporkan kepada
                        kami.
                    </p>
                </div>
                <div class="pdam-emergency-actions">
                    <a href="{{ route('pengaduan') }}" class="pdam-btn pdam-btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="12" y1="18" x2="12" y2="12"></line>
                            <line x1="9" y1="15" x2="15" y2="15"></line>
                        </svg>
                        Buat Pengaduan
                    </a>
                    <a href="tel:+62211234567" class="pdam-btn pdam-btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                            </path>
                        </svg>
                        Hubungi Call Center
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
