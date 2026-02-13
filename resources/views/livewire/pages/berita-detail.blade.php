<?php

declare(strict_types=1);

use App\Models\Berita;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
new #[Layout('components.layouts.public')] #[Title('Berita Detail')] class extends Component {
    public Berita $berita;

    public function mount(string $slug): void
    {
        $this->berita = Berita::where('slug', $slug)->with(['kategori', 'author'])->firstOrFail();
        $this->berita->incrementViewCount();
    }

    public function with(): array
    {
        return [
            'relatedNews' => Berita::published()
                ->with('kategori')
                ->where('id', '!=', $this->berita->id)
                ->whereHas('kategori', function ($query) {
                    $query->whereIn('id', $this->berita->kategori->pluck('id'));
                })
                ->latest('published_at')
                ->take(3)
                ->get(),
        ];
    }
}; ?>

<div>
    <!-- Breadcrumb Header -->
    <section class="pdam-page-header" style="padding: 1.5rem 0;">
        <div class="pdam-container">
            <nav class="pdam-breadcrumb" style="justify-content: flex-start;">
                <a href="{{ route('home') }}">Beranda</a>
                <span>/</span>
                <a href="{{ route('berita') }}">Berita</a>
                <span>/</span>
                <span>{{ Str::limit($berita->title, 40) }}</span>
            </nav>
        </div>
    </section>

    <!-- Article Section -->
    <article class="pdam-article">
        <div class="pdam-container">
            <div class="pdam-article-container">
                <!-- Header -->
                <header class="pdam-article-header">
                    @if ($berita->kategori->count() > 0)
                        <div class="pdam-article-meta gap-1">
                            @foreach ($berita->kategori as $kategori)
                                <span class="pdam-news-category gap-1 {{ $kategori->id }}">{{ $kategori->nama }}</span>
                            @endforeach
                        </div>
                    @endif
                    <h1 class="pdam-article-title">{{ $berita->title }}</h1>
                    <div class="pdam-article-info">
                        <span class="pdam-article-author">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            {{ $berita->author?->name ?? 'Admin' }}
                        </span>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" style="display: inline; vertical-align: middle;">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            {{ $berita->published_at->translatedFormat('d F Y') }}
                        </span>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" style="display: inline; vertical-align: middle;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            {{ $berita->reading_time }} menit baca
                        </span>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" style="display: inline; vertical-align: middle;">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            {{ number_format($berita->view_count) }}x dilihat
                        </span>
                    </div>
                </header>

                <!-- Featured Image -->
                @if ($berita->getFirstMediaUrl('thumbnails'))
                    <img src="{{ $berita->getFirstMediaUrl('thumbnails') }}" alt="{{ $berita->title }}"
                        class="pdam-article-image">
                @else
                    <div class="pdam-article-image"
                        style="height: 400px; background: linear-gradient(135deg, var(--pdam-primary), var(--pdam-primary-darker)); border-radius: var(--radius-xl);">
                    </div>
                @endif

                <!-- Content -->
                <div class="pdam-article-content">
                    {!! clean($berita->content) !!}
                </div>

                <!-- Share -->
                <div class="pdam-article-share">
                    <span class="pdam-article-share-text">Bagikan artikel ini:</span>
                    <div class="pdam-article-share-links">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                            target="_blank" class="pdam-article-share-link" title="Share to Facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                            </svg>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($berita->title) }}"
                            target="_blank" class="pdam-article-share-link" title="Share to Twitter">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 1200 1227" fill="none">
                                <rect width="1200" height="1227" fill="black" rx="160"/>
                                <path d="M849.991 314H1041.25L712.824 669.937L1099.36 1112H859.861L614.677 838.144L341.373 1112H150.067L499.137 734.197L131.464 314H377.455L595.44 565.941L849.991 314ZM807.298 1036.7H893.506L427.653 382.842H335.17L807.298 1036.7Z" fill="white"/>
                            </svg>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($berita->title . ' ' . request()->url()) }}"
                            target="_blank" class="pdam-article-share-link" title="Share to WhatsApp">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                                </path>
                            </svg>
                        </a>
                        <button onclick="navigator.clipboard.writeText('{{ request()->url() }}')"
                            class="pdam-article-share-link" title="Copy Link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <!-- Related News -->
    @if ($relatedNews->count() > 0)
        <section class="pdam-related-news">
            <div class="pdam-container">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h2 class="pdam-section-title pdam-mb-0">Berita Terkait</h2>
                    <a href="{{ route('berita') }}" class="pdam-news-link">
                        Lihat Semua
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                </div>
                <div class="pdam-news-listings-grid">
                    @foreach ($relatedNews as $related)
                        <article class="pdam-news-card">
                            @if ($related->getFirstMediaUrl('thumbnails'))
                                <img src="{{ $related->getFirstMediaUrl('thumbnails') }}"
                                    alt="{{ $related->title }}" class="pdam-news-card-image">
                            @else
                                <div class="pdam-news-card-image"
                                    style="background: linear-gradient(135deg, var(--pdam-gray-300), var(--pdam-gray-400));">
                                </div>
                            @endif
                            <div class="pdam-news-card-content">
                                <div class="pdam-news-card-meta">
                                    @foreach ($related->kategori as $kategori)
                                        <span class="pdam-news-category">{{ $kategori->nama }}</span>
                                    @endforeach
                                    <span
                                        class="pdam-news-date">{{ $related->published_at->translatedFormat('d M Y') }}</span>
                                </div>
                                <h3 class="pdam-news-card-title">
                                    <a href="{{ route('berita.show', $related->slug) }}">{{ $related->title }}</a>
                                </h3>
                                <p class="pdam-news-card-excerpt">{{ Str::limit($related->excerpt, 100) }}</p>
                                <a href="{{ route('berita.show', $related->slug) }}" class="pdam-news-link">
                                    Baca Selengkapnya
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
