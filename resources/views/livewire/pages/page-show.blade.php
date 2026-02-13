<?php

declare(strict_types=1);

use App\Models\Page;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] class extends Component {
    public Page $page;

    public function mount(string $slug): void
    {
        $this->page = Page::where('slug', $slug)
            ->active()
            ->firstOrFail();
    }
}; ?>

@section('title', $page->meta_title ?: $page->title)
@section('meta_description', $page->meta_description)

<div>
    <!-- Hero Section -->
    <section class="pdam-page-hero">
        <div class="pdam-container">
            <h1 class="pdam-page-hero-title">{{ $page->title }}</h1>
        </div>
    </section>

    <!-- Content Section -->
    <section class="pdam-section">
        <div class="pdam-container">
            <article class="pdam-article">
                @if ($page->getFirstMediaUrl('featured'))
                    <div class="pdam-article-featured">
                        <img src="{{ $page->getFirstMediaUrl('featured') }}" alt="{{ $page->title }}"
                            class="pdam-article-featured-img">
                    </div>
                @endif

                <div class="pdam-prose">
                    {!! clean($page->content) !!}
                </div>

                @if ($page->getMedia('images')->count() > 0)
                    <div class="pdam-article-gallery">
                        <h3 class="pdam-article-gallery-title">Galeri Foto</h3>
                        <div class="pdam-article-gallery-grid">
                            @foreach ($page->getMedia('images') as $media)
                                <a href="{{ $media->getUrl() }}" target="_blank" class="pdam-article-gallery-item">
                                    <img src="{{ $media->getUrl() }}" alt="Gallery image">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </article>
        </div>
    </section>
</div>
