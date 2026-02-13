<?php

declare(strict_types=1);

use App\Models\Lowongan;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] class extends Component {
    public Lowongan $lowongan;

    public function mount(string $slug): void
    {
        $this->lowongan = Lowongan::where('slug', $slug)
            ->active()
            ->firstOrFail();
    }

    public function with(): array
    {
        $relatedJobs = Lowongan::where('id', '!=', $this->lowongan->id)
            ->active()
            ->open()
            ->notExpired()
            ->ordered()
            ->limit(3)
            ->get();

        return [
            'relatedJobs' => $relatedJobs,
        ];
    }
}; ?>

@section('title', $lowongan->title . ' - Karir')

<div>
    <!-- Hero Section -->
    <section class="pdam-page-hero">
        <div class="pdam-container">
            <nav class="pdam-breadcrumb">
                <a href="{{ route('home') }}">Beranda</a>
                <span>/</span>
                <a href="{{ route('karir') }}">Karir</a>
                <span>/</span>
                <span>{{ $lowongan->title }}</span>
            </nav>
        </div>
    </section>

    <!-- Job Detail Section -->
    <section class="pdam-section">
        <div class="pdam-container">
            <div class="pdam-content-grid">
                <!-- Main Content -->
                <div class="pdam-main-content">
                    <article class="pdam-job-detail">
                        <header class="pdam-job-detail-header">
                            <div class="pdam-job-detail-title-wrap">
                                <h1 class="pdam-job-detail-title">{{ $lowongan->title }}</h1>
                                @if ($lowongan->department)
                                    <p class="pdam-job-detail-department">{{ $lowongan->department }}</p>
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
                                class="pdam-job-badge-lg {{ $typeColors[$lowongan->employment_type->value] ?? 'pdam-badge-gray' }}">
                                {{ $lowongan->employment_type->getLabel() }}
                            </span>
                        </header>

                        <div class="pdam-job-detail-meta">
                            <div class="pdam-job-detail-meta-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <span>{{ $lowongan->location }}</span>
                            </div>
                            @if ($lowongan->deadline)
                                <div class="pdam-job-detail-meta-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2">
                                        </rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <span>Deadline: {{ $lowongan->deadline->translatedFormat('d F Y') }}</span>
                                </div>
                            @endif
                            <div class="pdam-job-detail-meta-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <span>Diposting: {{ $lowongan->created_at->translatedFormat('d F Y') }}</span>
                            </div>
                        </div>

                        @if ($lowongan->isAcceptingApplications())
                            <div class="pdam-job-status-accepting">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                <span>Menerima Lamaran</span>
                            </div>
                        @else
                            <div class="pdam-job-status-closed">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                <span>Tidak Menerima Lamaran</span>
                            </div>
                        @endif

                        <div class="pdam-job-section">
                            <h2 class="pdam-job-section-title">Deskripsi</h2>
                            <div class="pdam-prose">
                                {!! clean($lowongan->description) !!}
                            </div>
                        </div>

                        <div class="pdam-job-section">
                            <h2 class="pdam-job-section-title">Persyaratan</h2>
                            <div class="pdam-prose">
                                {!! clean($lowongan->requirements) !!}
                            </div>
                        </div>

                        @if ($lowongan->responsibilities)
                            <div class="pdam-job-section">
                                <h2 class="pdam-job-section-title">Tanggung Jawab</h2>
                                <div class="pdam-prose">
                                    {!! clean($lowongan->responsibilities) !!}
                                </div>
                            </div>
                        @endif

                        <div class="pdam-job-apply-section">
                            <h2 class="pdam-job-section-title">Cara Melamar</h2>
                            <p class="pdam-job-apply-text">
                                Kirimkan lamaran lengkap Anda (CV, surat lamaran, dan dokumen pendukung lainnya) ke:
                            </p>
                            <div class="pdam-job-apply-info">
                                <p><strong>Email:</strong> hrd@pdam.go.id</p>
                                <p><strong>Subject:</strong> Lamaran - {{ $lowongan->title }}</p>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Sidebar -->
                <aside class="pdam-sidebar">
                    @if ($relatedJobs->count() > 0)
                        <div class="pdam-sidebar-section">
                            <h3 class="pdam-sidebar-title">Lowongan Lainnya</h3>
                            <div class="pdam-sidebar-list">
                                @foreach ($relatedJobs as $job)
                                    <a href="{{ route('karir.detail', $job->slug) }}" class="pdam-sidebar-item">
                                        <span class="pdam-sidebar-item-title">{{ $job->title }}</span>
                                        @if ($job->deadline)
                                            <span class="pdam-sidebar-item-meta">Deadline:
                                                {{ $job->deadline->translatedFormat('d M Y') }}</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="pdam-sidebar-section">
                        <h3 class="pdam-sidebar-title">Kontak HRD</h3>
                        <div class="pdam-contact-info">
                            <p>
                                <strong>Email:</strong><br>
                                hrd@pdam.go.id
                            </p>
                            <p>
                                <strong>Telepon:</strong><br>
                                (031) 5039373
                            </p>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</div>
