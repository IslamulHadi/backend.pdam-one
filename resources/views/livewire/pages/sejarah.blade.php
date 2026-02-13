<?php

declare(strict_types=1);

use App\Models\Page;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] #[Title('Sejarah PDAM')] class extends Component {
    public ?Page $page = null;

    public function mount(): void
    {
        $this->page = Page::where('slug', 'sejarah')
            ->active()
            ->first();
    }
}; ?>

<div>
    <!-- Hero Section -->
    <section class="pdam-page-hero">
        <div class="pdam-container">
            <h1 class="pdam-page-hero-title">Sejarah PDAM</h1>
            <p class="pdam-page-hero-subtitle">
                Perjalanan panjang melayani masyarakat dalam penyediaan air bersih
            </p>
        </div>
    </section>

    <!-- Content Section -->
    <section class="pdam-section">
        <div class="pdam-container">
            @if ($page)
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
                                    <a href="{{ $media->getUrl() }}" target="_blank"
                                        class="pdam-article-gallery-item">
                                        <img src="{{ $media->getUrl() }}" alt="Gallery image">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </article>
            @else
                <article class="pdam-article">
                    <div class="pdam-prose">
                        <h2>Sejarah Singkat</h2>
                        <p>
                            Perusahaan Daerah Air Minum (PDAM) didirikan sebagai perusahaan daerah yang bergerak dalam
                            bidang pelayanan air bersih kepada masyarakat. Sejarah panjang PDAM dimulai sejak zaman
                            kolonial Belanda dan terus berkembang hingga menjadi salah satu perusahaan pelayanan publik
                            terpenting.
                        </p>

                        <h3>Masa Penjajahan</h3>
                        <p>
                            Pada masa penjajahan Belanda, kebutuhan air bersih untuk penduduk kota mulai diperhatikan.
                            Pembangunan instalasi pengolahan air pertama dimulai untuk memenuhi kebutuhan air bersih
                            masyarakat perkotaan.
                        </p>

                        <h3>Masa Kemerdekaan</h3>
                        <p>
                            Setelah Indonesia merdeka, perusahaan air minum diambil alih oleh pemerintah Indonesia.
                            Perkembangan berlanjut dengan peningkatan kapasitas produksi dan perluasan jaringan
                            distribusi
                            untuk menjangkau lebih banyak masyarakat.
                        </p>

                        <h3>Era Modern</h3>
                        <p>
                            Di era modern, PDAM terus melakukan modernisasi dengan menerapkan teknologi terbaru dalam
                            pengolahan dan distribusi air. Sistem pembayaran digital, monitoring kualitas air real-time,
                            {{-- dan aplikasi mobile untuk pelanggan menjadi bagian dari transformasi digital perusahaan. --}}
                        </p>
                    </div>
                </article>
            @endif
        </div>
    </section>

    <!-- Timeline Section -->
    <section class="pdam-section pdam-bg-light">
        <div class="pdam-container">
            <h2 class="pdam-section-title">Timeline Perkembangan</h2>
            <div class="pdam-timeline">
                <div class="pdam-timeline-item">
                    <div class="pdam-timeline-marker"></div>
                    <div class="pdam-timeline-content">
                        <span class="pdam-timeline-year">1920-an</span>
                        <h3 class="pdam-timeline-title">Era Kolonial</h3>
                        <p class="pdam-timeline-desc">Pembangunan infrastruktur air bersih pertama oleh pemerintah
                            kolonial Belanda.</p>
                    </div>
                </div>
                <div class="pdam-timeline-item">
                    <div class="pdam-timeline-marker"></div>
                    <div class="pdam-timeline-content">
                        <span class="pdam-timeline-year">1945</span>
                        <h3 class="pdam-timeline-title">Pengambilalihan</h3>
                        <p class="pdam-timeline-desc">Setelah kemerdekaan, pengelolaan air minum diambil alih oleh
                            pemerintah Indonesia.</p>
                    </div>
                </div>
                <div class="pdam-timeline-item">
                    <div class="pdam-timeline-marker"></div>
                    <div class="pdam-timeline-content">
                        <span class="pdam-timeline-year">1970-an</span>
                        <h3 class="pdam-timeline-title">Pembentukan PDAM</h3>
                        <p class="pdam-timeline-desc">Dibentuk sebagai Perusahaan Daerah untuk pelayanan air minum
                            masyarakat.</p>
                    </div>
                </div>
                <div class="pdam-timeline-item">
                    <div class="pdam-timeline-marker"></div>
                    <div class="pdam-timeline-content">
                        <span class="pdam-timeline-year">2000-an</span>
                        <h3 class="pdam-timeline-title">Modernisasi</h3>
                        <p class="pdam-timeline-desc">Penerapan teknologi modern dalam pengolahan dan distribusi air.
                        </p>
                    </div>
                </div>
                <div class="pdam-timeline-item">
                    <div class="pdam-timeline-marker"></div>
                    <div class="pdam-timeline-content">
                        <span class="pdam-timeline-year">Sekarang</span>
                        <h3 class="pdam-timeline-title">Era Digital</h3>
                        <p class="pdam-timeline-desc">Transformasi digital dengan layanan online dan aplikasi mobile.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
