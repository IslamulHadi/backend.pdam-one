<?php

declare(strict_types=1);

use App\Models\Pengumuman;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] class extends Component {
    public Pengumuman $pengumuman;

    public function mount(string $slug): void
    {
        $this->pengumuman = Pengumuman::where('slug', $slug)->published()->firstOrFail();
    }

    public function with(): array
    {
        return [
            'relatedPengumuman' => Pengumuman::published()
                ->where('id', '!=', $this->pengumuman->id)
                ->ordered()
                ->take(3)
                ->get(),
        ];
    }
}; ?>

@section('title', $pengumuman->title)

<div>
    <!-- Hero Section -->
    <section class="pdam-page-hero-small">
        <div class="pdam-container">
            <nav class="pdam-breadcrumb">
                <a href="{{ route('home') }}">Beranda</a>
                <span>/</span>
                <a href="{{ route('pengumuman') }}">Pengumuman</a>
                <span>/</span>
                <span>{{ Str::limit($pengumuman->title, 30) }}</span>
            </nav>
        </div>
    </section>

    <!-- Content Section -->
    <section class="pdam-section">
        <div class="pdam-container">
            <div class="pdam-article-layout">
                <article class="pdam-article-main">
                    <header class="pdam-article-header">
                        @php
                            $priorityColors = [
                                'tinggi' => 'pdam-badge-red',
                                'sedang' => 'pdam-badge-yellow',
                                'rendah' => 'pdam-badge-blue',
                            ];
                        @endphp
                        <span
                            class="pdam-article-badge {{ $priorityColors[$pengumuman->priority->value] ?? 'pdam-badge-gray' }}">
                            Prioritas {{ $pengumuman->priority->getLabel() }}
                        </span>
                        <h1 class="pdam-article-title">{{ $pengumuman->title }}</h1>
                        <div class="pdam-article-meta">
                            <span class="pdam-article-date">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                {{ $pengumuman->created_at->translatedFormat('d F Y') }}
                            </span>
                            @if ($pengumuman->end_date)
                                <span class="pdam-article-expires">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    Berlaku hingga {{ $pengumuman->end_date->translatedFormat('d F Y') }}
                                </span>
                            @endif
                        </div>
                    </header>

                    <div class="pdam-article-content prose prose-lg max-w-none">
                        {!! clean($pengumuman->content) !!}
                    </div>

                    <footer class="pdam-article-footer">
                        <a href="{{ route('pengumuman') }}" class="pdam-btn pdam-btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                            Kembali ke Daftar Pengumuman
                        </a>
                    </footer>
                </article>

                <!-- Sidebar -->
                <aside class="pdam-article-sidebar">
                    <div class="pdam-sidebar-widget">
                        <h3 class="pdam-sidebar-title">Pengumuman Lainnya</h3>
                        @if ($relatedPengumuman->count() > 0)
                            <div class="pdam-sidebar-list">
                                @foreach ($relatedPengumuman as $related)
                                    <a href="{{ route('pengumuman.show', $related->slug) }}"
                                        class="pdam-sidebar-item">
                                        <span class="pdam-sidebar-item-title">{{ $related->title }}</span>
                                        <span
                                            class="pdam-sidebar-item-date">{{ $related->created_at->translatedFormat('d M Y') }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Tidak ada pengumuman lainnya.</p>
                        @endif
                    </div>
                </aside>
            </div>
        </div>
    </section>
</div>
