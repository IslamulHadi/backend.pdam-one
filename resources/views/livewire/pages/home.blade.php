<?php

declare(strict_types=1);

use App\Models\Berita;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\GangguanAir;
use App\Models\Kecamatan;
use App\Models\Lowongan;
use App\Models\Pelanggan;
use App\Models\Pengumuman;
use App\Models\Slider;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] #[Title('Beranda')] class extends Component {
    public function with(): array
    {
        return Cache::remember('page.home', 300, function () {
            return [
                'latestNews' => Berita::published()->latest('published_at')->take(4)->get(),
                'kecamatan' => Kecamatan::all(),
                'pelangganAktif' => Pelanggan::active()->count(),
                'sliders' => Slider::active()->published()->ordered()->take(5)->get(),
                'pengumuman' => Pengumuman::active()->published()->ordered()->take(3)->get(),
                'gangguanAktif' => GangguanAir::active()->ordered()->take(5)->get(),
                'galleries' => Gallery::active()->ordered()->take(6)->get(),
                'lowongan' => Lowongan::active()->open()->notExpired()->ordered()->take(3)->get(),
                'faqs' => Faq::where('is_active', true)->orderBy('display_order')->take(5)->get(),
            ];
        });
    }
}; ?>

<div>
    <!-- Hero Section - Clean & Modern -->
    <section class="pdam-hero-clean" x-data="{ activeSlide: 0, totalSlides: {{ max($sliders->count(), 1) }} }"
        x-init="setInterval(() => activeSlide = (activeSlide + 1) % totalSlides, 6000)">
        
        <div class="pdam-hero-bg">
            @if ($sliders->count() > 0)
                @foreach ($sliders as $index => $slider)
                    <div class="pdam-hero-slide" x-show="activeSlide === {{ $index }}"
                        x-transition:enter="transition ease-out duration-700"
                        x-transition:enter-start="opacity-0 scale-105" 
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-500"
                        x-transition:leave-start="opacity-100"  
                        x-transition:leave-end="opacity-0"
                        style="background-image: url('{{ $slider->getFirstMediaUrl('image') ?: 'https://images.unsplash.com/photo-1584568694244-14fbdf83bd30?w=1920&q=80' }}');" class="bg-primary">
                    </div>
                @endforeach
            @else
                <div class="pdam-hero-slide active" style="background-color: var(--pdam-primary);"></div>
            @endif
            <div class="pdam-hero-overlay-clean"></div>
        </div>

        <div class="pdam-container pdam-hero-inner">
            <div class="pdam-hero-text">
                <div class="pdam-hero-badge-clean" x-data x-intersect="$el.classList.add('animate-in')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
                    </svg>
                    Air Bersih untuk Kehidupan
                </div>
                
                <h1 class="pdam-hero-title-clean">
                    @if ($sliders->count() > 0)
                        @foreach ($sliders as $index => $slider)
                            <span x-show="activeSlide === {{ $index }}" x-cloak
                                x-transition:enter="transition ease-out duration-500 delay-100"
                                x-transition:enter-start="opacity-0 translate-y-4"
                                x-transition:enter-end="opacity-100 translate-y-0">
                                {{ $slider->title }}
                            </span>
                        @endforeach
                    @else
                        Melayani dengan <span class="pdam-text-accent">Sepenuh Hati</span>
                    @endif
                </h1>
                
                <p class="pdam-hero-desc-clean">
                    @if ($sliders->count() > 0)
                        @foreach ($sliders as $index => $slider)
                            <span x-show="activeSlide === {{ $index }}" x-cloak>
                                {{ $slider->subtitle ?? 'Nikmati kemudahan layanan air bersih dengan sistem digital terintegrasi' }}
                            </span>
                        @endforeach
                    @else
                        Nikmati kemudahan akses layanan air bersih dengan sistem digital terintegrasi untuk kebutuhan Anda.
                    @endif
                </p>

                <div class="pdam-hero-cta">
                    <a href="{{ route('cek-tagihan') }}" class="pdam-btn pdam-btn-white pdam-btn-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                        Cek Tagihan
                    </a>
                    <a href="{{ route('pengaduan') }}" class="pdam-btn pdam-btn-outline-white pdam-btn-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                        Lapor Pengaduan
                    </a>
                </div>

                @if ($sliders->count() > 1)
                    <div class="pdam-hero-indicators">
                        @foreach ($sliders as $index => $slider)
                            <button @click="activeSlide = {{ $index }}"
                                :class="{ 'active': activeSlide === {{ $index }} }"
                                class="pdam-indicator"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Quick Services - Floating Cards -->
    <section class="pdam-quick-services">
        <div class="pdam-container">
            <div class="pdam-services-grid">
                <a href="{{ route('cek-tagihan') }}" class="pdam-service-card" x-data x-intersect="$el.classList.add('animate-in')">
                    <div class="pdam-service-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                            <line x1="2" y1="10" x2="22" y2="10"></line>
                        </svg>
                    </div>
                    <div class="pdam-service-content">
                        <h3>Cek Tagihan</h3>
                        <p>Informasi tagihan & pemakaian</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="pdam-service-arrow">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>

                <a href="{{ route('pengaduan') }}" class="pdam-service-card" x-data x-intersect="$el.classList.add('animate-in')" style="animation-delay: 0.1s">
                    <div class="pdam-service-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <div class="pdam-service-content">
                        <h3>Pengaduan</h3>
                        <p>Laporkan keluhan Anda</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="pdam-service-arrow">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>

                <a href="{{ route('pasang-baru') }}" class="pdam-service-card" x-data x-intersect="$el.classList.add('animate-in')" style="animation-delay: 0.2s">
                    <div class="pdam-service-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                    </div>
                    <div class="pdam-service-content">
                        <h3>Pasang Baru</h3>
                        <p>Daftar sambungan baru</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="pdam-service-arrow">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>

                <a href="{{ route('loket-pembayaran') }}" class="pdam-service-card" x-data x-intersect="$el.classList.add('animate-in')" style="animation-delay: 0.3s">
                    <div class="pdam-service-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <div class="pdam-service-content">
                        <h3>Lokasi Pembayaran</h3>
                        <p>Temukan loket terdekat</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="pdam-service-arrow">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Water Disruption Alert -->
    @if ($gangguanAktif->count() > 0)
        <section class="pdam-alert-section">
            <div class="pdam-container">
                <div class="pdam-alert-banner">
                    <div class="pdam-alert-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                        <span>Info Gangguan</span>
                    </div>
                    <div class="pdam-alert-content">
                        <div class="pdam-alert-marquee">
                            @foreach ($gangguanAktif as $gangguan)
                                <span class="pdam-alert-item">
                                    <strong>{{ $gangguan->title }}</strong> - {{ Str::limit($gangguan->affected_areas_text, 60) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <a href="{{ route('gangguan-air') }}" class="pdam-alert-link">
                        Selengkapnya
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
        </section>
    @endif

    <!-- Statistics Section -->
    <section class="pdam-stats-clean">
        <div class="pdam-container">
            <div class="pdam-stats-grid">
                <div class="pdam-stat-item" x-data="{ count: 0, target: {{ $pelangganAktif }} }"
                    x-intersect.once="let interval = setInterval(() => { if(count < target) count += Math.ceil(target/50); else { count = target; clearInterval(interval); } }, 30)">
                    <div class="pdam-stat-icon-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="pdam-stat-value" x-text="count.toLocaleString('id-ID')">0</div>
                    <div class="pdam-stat-label">Pelanggan Aktif</div>
                </div>

                <div class="pdam-stat-item">
                    <div class="pdam-stat-icon-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <div class="pdam-stat-value">98%</div>
                    <div class="pdam-stat-label">Cakupan Layanan</div>
                </div>

                <div class="pdam-stat-item">
                    <div class="pdam-stat-icon-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="pdam-stat-value">24/7</div>
                    <div class="pdam-stat-label">Layanan Teknis</div>
                </div>

                <div class="pdam-stat-item" x-data="{ count: 0, target: {{ $kecamatan->count() }} }"
                    x-intersect.once="let interval = setInterval(() => { if(count < target) count++; else clearInterval(interval); }, 100)">
                    <div class="pdam-stat-icon-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <div class="pdam-stat-value" x-text="count">0</div>
                    <div class="pdam-stat-label">Kecamatan Terlayani</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Coverage Area Map -->
    <section class="pdam-section pdam-bg-white">
        <div class="pdam-container">
            <div class="pdam-section-header">
                <span class="pdam-section-label">Cakupan Layanan</span>
                <h2 class="pdam-section-title">Wilayah Pelayanan PDAM</h2>
                <p class="pdam-section-desc">Temukan lokasi pembayaran dan area layanan PDAM terdekat dari Anda</p>
            </div>

            <div class="pdam-map-wrapper">
                <div class="pdam-coverage-map">
                    <iframe
                        src="https://www.openstreetmap.org/export/embed.html?bbox=119.8920,-2.9669,119.9320,-2.9469&layer=mapnik&marker=-2.956917402906132,119.91204033283509"
                        width="100%" 
                        height="100%" 
                        style="border: 0; border-radius: var(--radius-xl);" 
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade" 
                        title="Peta Wilayah Layanan PDAM">
                    </iframe>
                </div>
                <div class="pdam-map-info">
                    <a href="{{ route('loket-pembayaran') }}" class="pdam-btn pdam-btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        Lihat Semua Lokasi
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Announcements Section -->
    @if ($pengumuman->count() > 0)
        <section class="pdam-section pdam-bg-light">
            <div class="pdam-container">
                <div class="pdam-section-header-row">
                    <div>
                        <span class="pdam-section-label">Informasi Terkini</span>
                        <h2 class="pdam-section-title">Pengumuman</h2>
                    </div>
                    <a href="{{ route('pengumuman') }}" class="pdam-btn pdam-btn-outline pdam-btn-sm">
                        Lihat Semua
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                </div>

                <div class="pdam-announcement-grid">
                    @foreach ($pengumuman as $item)
                        <a href="{{ route('pengumuman.show', $item->slug) }}" class="pdam-announcement-card" 
                            x-data x-intersect="$el.classList.add('animate-in')">
                            <div class="pdam-announcement-date">
                                <span class="pdam-date-day">{{ $item->created_at->translatedFormat('d') }}</span>
                                <span class="pdam-date-month text-white">{{ $item->created_at->translatedFormat('M') }}</span>
                            </div>
                            <div class="pdam-announcement-body">
                                @php
                                    $priorityClass = match($item->priority->value) {
                                        'tinggi' => 'pdam-badge-danger',
                                        'sedang' => 'pdam-badge-warning',
                                        default => 'pdam-badge-info'
                                    };
                                @endphp
                                <span class="pdam-badge {{ $priorityClass }}">{{ $item->priority->getLabel() }}</span>
                                <h3 class="pdam-announcement-title">{{ $item->title }}</h3>
                                <p class="pdam-announcement-excerpt">{{ Str::limit($item->excerpt, 100) }}</p>
                            </div>
                            <div class="pdam-announcement-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- News Section -->
    <section class="pdam-section pdam-bg-white">
        <div class="pdam-container">
            <div class="pdam-section-header-row">
                <div>
                    <span class="pdam-section-label">Berita & Informasi</span>
                    <h2 class="pdam-section-title">Berita Terkini</h2>
                </div>
                <a href="{{ route('berita') }}" class="pdam-btn pdam-btn-outline pdam-btn-sm">
                    Lihat Semua
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>

            <div class="pdam-news-grid">
                @forelse($latestNews as $news)
                    <a href="{{ route('berita.show', $news->slug) }}" class="pdam-news-card"
                        x-data x-intersect="$el.classList.add('animate-in')">
                        <div class="pdam-news-image">
                            @if ($news->getFirstMediaUrl('thumbnails'))
                                <img src="{{ $news->getFirstMediaUrl('thumbnails') }}" alt="{{ $news->title }}">
                            @else
                                <div class="pdam-news-placeholder">
                                    <img src="{{ asset('pdam.png') }}" alt="PDAM">
                                </div>
                            @endif
                        </div>
                        <div class="pdam-news-content">
                            <div class="pdam-news-meta">
                                <span class="pdam-news-date">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    {{ $news->published_at->translatedFormat('d M Y') }}
                                </span>
                            </div>
                            <h3 class="pdam-news-title">{{ Str::limit($news->title, 60) }}</h3>
                            <p class="pdam-news-excerpt">{{ Str::limit($news->excerpt, 100) }}</p>
                            <span class="pdam-read-more">Baca Selengkapnya</span>
                        </div>
                    </a>
                @empty
                    <div class="pdam-empty-state-inline">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        <p>Belum ada berita terbaru</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    @if ($galleries->count() > 0)
        <section class="pdam-section pdam-bg-light">
            <div class="pdam-container">
                <div class="pdam-section-header-row">
                    <div>
                        <span class="pdam-section-label">Dokumentasi</span>
                        <h2 class="pdam-section-title">Galeri Kegiatan</h2>
                    </div>
                    <a href="{{ route('galeri') }}" class="pdam-btn pdam-btn-outline pdam-btn-sm">
                        Lihat Semua
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                </div>

                <div class="pdam-gallery-grid">
                    @foreach ($galleries as $gallery)
                        <a href="{{ route('galeri') }}" class="pdam-gallery-item"
                            x-data x-intersect="$el.classList.add('animate-in')">
                            @if ($gallery->type->value === 'foto' && $gallery->getFirstMediaUrl('images'))
                                <img src="{{ $gallery->getFirstMediaUrl('images') }}" alt="{{ $gallery->title }}">
                            @elseif($gallery->type->value === 'video' && $gallery->video_url)
                                @php
                                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $gallery->video_url, $matches);
                                    $videoId = $matches[1] ?? null;
                                @endphp
                                @if ($videoId)
                                    <img src="https://img.youtube.com/vi/{{ $videoId }}/hqdefault.jpg" alt="{{ $gallery->title }}">
                                    <div class="pdam-gallery-play">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="white">
                                            <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                        </svg>
                                    </div>
                                @endif
                            @else
                                <div class="pdam-gallery-placeholder">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                </div>
                            @endif
                            <div class="pdam-gallery-overlay">
                                <span class="pdam-gallery-title">{{ $gallery->title }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- FAQ Section -->
    @if ($faqs->count() > 0)
        <section class="pdam-section pdam-bg-white">
            <div class="pdam-container">
                <div class="pdam-faq-wrapper">
                    <div class="pdam-faq-header">
                        <span class="pdam-section-label">Bantuan</span>
                        <h2 class="pdam-section-title">Pertanyaan Umum</h2>
                        <p class="pdam-section-desc">Temukan jawaban untuk pertanyaan yang sering diajukan</p>
                        <a href="{{ route('faq') }}" class="pdam-btn pdam-btn-primary pdam-btn-sm">
                            Lihat Semua FAQ
                        </a>
                    </div>

                    <div class="pdam-faq-list" x-data="{ open: 0 }">
                        @foreach ($faqs as $index => $faq)
                            <div class="pdam-faq-item" x-data x-intersect="$el.classList.add('animate-in')">
                                <button class="pdam-faq-question" @click="open = open === {{ $index }} ? null : {{ $index }}">
                                    <span>{{ $faq->question }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        :class="{ 'rotate-180': open === {{ $index }} }" class="pdam-faq-icon transition-transform duration-200">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </button>
                                <div class="pdam-faq-answer" x-show="open === {{ $index }}" x-collapse>
                                    {!! clean($faq->answer) !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Career Section -->
    @if ($lowongan->count() > 0)
        <section class="pdam-section pdam-bg-primary">
            <div class="pdam-container">
                <div class="pdam-section-header-row pdam-text-white">
                    <div>
                        <span class="pdam-section-label pdam-label-white">Karir</span>
                        <h2 class="pdam-section-title">Lowongan Kerja</h2>
                        <p class="pdam-section-desc">Bergabunglah bersama kami dalam melayani masyarakat</p>
                    </div>
                    <a href="{{ route('karir') }}" class="pdam-btn pdam-btn-white pdam-btn-sm">
                        Lihat Semua
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                </div>

                <div class="pdam-career-grid">
                    @foreach ($lowongan as $job)
                        <a href="{{ route('karir.detail', $job->slug) }}" class="pdam-career-card"
                            x-data x-intersect="$el.classList.add('animate-in')">
                            <div class="pdam-career-header">
                                @php
                                    $typeBadge = match($job->employment_type->value) {
                                        'full_time' => 'pdam-badge-success',
                                        'contract' => 'pdam-badge-warning',
                                        default => 'pdam-badge-info'
                                    };
                                @endphp
                                <span class="pdam-badge {{ $typeBadge }}">{{ $job->employment_type->getLabel() }}</span>
                            </div>
                            <h3 class="pdam-career-title">{{ $job->title }}</h3>
                            @if ($job->department)
                                <p class="pdam-career-dept">{{ $job->department }}</p>
                            @endif
                            <div class="pdam-career-meta">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    {{ $job->location }}
                                </span>
                                @if ($job->deadline)
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        {{ $job->deadline->translatedFormat('d M Y') }}
                                    </span>
                                @endif
                            </div>
                            <span class="pdam-career-cta">Lamar Sekarang &rarr;</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Contact CTA -->
    <section class="pdam-section pdam-bg-light">
        <div class="pdam-container">
            <div class="pdam-contact-cta">
                <div class="pdam-contact-text">
                    <h2 class="pdam-contact-title">Butuh Bantuan?</h2>
                    <p class="pdam-contact-desc">Tim kami siap membantu Anda 24 jam sehari, 7 hari seminggu.</p>
                </div>
                <div class="pdam-contact-actions">
                    <a href="tel:08001234567" class="pdam-contact-btn pdam-btn-phone">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        <div>
                            <span>Call Center</span>
                            <strong>0800 123 4567</strong>
                        </div>
                    </a>
                    <a href="https://wa.me/628123456789" target="_blank" class="pdam-contact-btn pdam-btn-whatsapp">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        <div>
                            <span>WhatsApp</span>
                            <strong>+62 812 3456 789</strong>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

