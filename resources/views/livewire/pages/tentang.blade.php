<?php

declare(strict_types=1);

use App\Enums\CompanyInfoKey;
use App\Models\CompanyInfo;
use App\Models\Kecamatan;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\{Layout, Title};
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] #[Title('Tentang Kami')] class extends Component {
    public function with(): array
    {
        return Cache::remember('page.tentang', 3600, function () {
            return [
                'vision' => CompanyInfo::getValue(CompanyInfoKey::Vision, 'Menjadi perusahaan air minum modern kelas dunia yang memberikan pelayanan prima dan berkontribusi pada kelestarian lingkungan.'),
                'missions' => [
                    CompanyInfo::getValue(CompanyInfoKey::Mission1, 'Menyediakan air bersih yang memenuhi standar kesehatan (K3) secara berkesinambungan.'),
                    CompanyInfo::getValue(CompanyInfoKey::Mission2, 'Menerapkan teknologi terkini dalam operasional dan pelayanan pelanggan.'),
                    CompanyInfo::getValue(CompanyInfoKey::Mission3, 'Meningkatkan kompetensi sumber daya manusia yang profesional dan berintegritas.'),
                ],
                'foundedYear' => CompanyInfo::getValue(CompanyInfoKey::FoundedYear, '1982'),
                'pelangganAktif' => Pelanggan::active()->count(),
                'kecamatan' => Kecamatan::count(),
                'heroBadge' => CompanyInfo::getValue(CompanyInfoKey::AboutHeroBadge, 'Tentang PDAM Amerta Toraya'),
                'heroTitle' => CompanyInfo::getValue(CompanyInfoKey::AboutHeroTitle, 'Mengalirkan Kehidupan, Membangun Masa Depan'),
                'heroSubtitle' => CompanyInfo::getValue(CompanyInfoKey::AboutHeroSubtitle, 'Berkomitmen menyediakan air bersih berkualitas tinggi dengan teknologi modern dan pelayanan prima untuk kesejahteraan masyarakat dan lingkungan yang berkelanjutan.'),
                'heroImage' => CompanyInfo::getValue(CompanyInfoKey::AboutHeroImage, 'https://images.unsplash.com/photo-1533077162801-86490c593afb?q=80&w=1200&auto=format&fit=crop'),
                'historyTitle' => CompanyInfo::getValue(CompanyInfoKey::AboutHistoryTitle, 'Dedikasi Untuk Negeri'),
                'historyParagraph1' => CompanyInfo::getValue(CompanyInfoKey::AboutHistoryParagraph1, 'PDAM Amerta Toraya didirikan dengan satu tujuan mulia: memastikan setiap tetes air yang sampai ke rumah pelanggan adalah air yang bersih, aman, dan menyehatkan. Sebagai Badan Usaha Milik Daerah (BUMD), kami memegang teguh mandat untuk melayani kebutuhan dasar masyarakat.'),
                'historyParagraph2' => CompanyInfo::getValue(CompanyInfoKey::AboutHistoryParagraph2, 'Kami terus bertransformasi. Dari sistem manual hingga kini kami tidak pernah berhenti berinovasi untuk efisiensi dan kepuasan pelanggan.'),
                'coveragePercentage' => CompanyInfo::getValue(CompanyInfoKey::CoveragePercentage, '98%'),
                'serviceHoursValue' => CompanyInfo::getValue(CompanyInfoKey::ServiceHoursValue, '24/7'),
                'visiMisiSubtitle' => CompanyInfo::getValue(CompanyInfoKey::AboutVisiMisiSubtitle, 'Arah langkah kami dalam mewujudkan pelayanan air bersih yang unggul dan berkelanjutan.'),
                'valuesTitle' => CompanyInfo::getValue(CompanyInfoKey::AboutValuesTitle, 'Nilai Utama Perusahaan'),
                'valuesSubtitle' => CompanyInfo::getValue(CompanyInfoKey::AboutValuesSubtitle, 'Dalam setiap tetes air yang kami alirkan, terkandung nilai-nilai yang menjadi pedoman kerja seluruh insan PDAM.'),
                'value1Title' => CompanyInfo::getValue(CompanyInfoKey::Value1Title, 'Integritas'),
                'value1Description' => CompanyInfo::getValue(CompanyInfoKey::Value1Description, 'Bekerja dengan jujur, transparan, dan bertanggung jawab terhadap amanah publik.'),
                'value2Title' => CompanyInfo::getValue(CompanyInfoKey::Value2Title, 'Orientasi Pelanggan'),
                'value2Description' => CompanyInfo::getValue(CompanyInfoKey::Value2Description, 'Menempatkan kepuasan pelanggan sebagai prioritas utama dalam setiap keputusan.'),
                'value3Title' => CompanyInfo::getValue(CompanyInfoKey::Value3Title, 'Inovasi'),
                'value3Description' => CompanyInfo::getValue(CompanyInfoKey::Value3Description, 'Terus melakukan perbaikan dan pembaruan untuk hasil yang lebih baik dan efisien.'),
                'coverageTitle' => CompanyInfo::getValue(CompanyInfoKey::AboutCoverageTitle, 'Wilayah Layanan Luas'),
                'coverageDescription' => CompanyInfo::getValue(CompanyInfoKey::AboutCoverageDescription, 'Terus memperluas jaringan pipa distribusi hingga ke pelosok daerah untuk pemerataan akses air bersih.'),
            ];
        });
    }
}; ?>

<div>
    <!-- Hero Section -->
    <section class="pdam-about-hero">
        <div class="pdam-container">
            <span class="pdam-about-hero-badge">{{ $heroBadge }}</span>
            <h1 class="pdam-about-hero-title text-white">
                {!! nl2br(e($heroTitle)) !!}
            </h1>
            <p class="pdam-about-hero-subtitle">
                {{ $heroSubtitle }}
            </p>
        </div>
    </section>

    <!-- Main Image -->
    <section class="pdam-about-image-section">
        <div class="pdam-container">
            <img src="{{ $heroImage }}"
                alt="Water Treatment Plant" class="pdam-about-main-image">
        </div>
    </section>

    <!-- History Section -->
    <section class="pdam-section">
        <div class="pdam-container">
            <div class="pdam-about-history">
                <div class="pdam-about-history-content">
                    <h2>{{ $historyTitle }}</h2>
                    <p>{{ $historyParagraph1 }}</p>
                    <p>{{ $historyParagraph2 }}</p>
                    <a href="{{ route('sejarah') }}" class="pdam-btn pdam-btn-secondary">Lihat Sejarah Perusahaan</a>
                </div>
                <div class="pdam-about-stats">
                    <div class="pdam-about-stat">
                        <div class="pdam-about-stat-value">{{ number_format($pelangganAktif) }}</div>
                        <div class="pdam-about-stat-label">Pelanggan Aktif</div>
                    </div>
                    <div class="pdam-about-stat">
                        <div class="pdam-about-stat-value">{{ $coveragePercentage }}</div>
                        <div class="pdam-about-stat-label">Cakupan Wilayah</div>
                    </div>
                    <div class="pdam-about-stat">
                        <div class="pdam-about-stat-value">{{ $serviceHoursValue }}</div>
                        <div class="pdam-about-stat-label">Layanan Siaga</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision & Mission -->
    <section class="pdam-vision-mission">
        <div class="pdam-container">
            <div class="pdam-section-header">
                <h2 class="pdam-section-title">Visi & Misi</h2>
                <p class="pdam-section-subtitle">
                    {{ $visiMisiSubtitle }}
                </p>
            </div>

            <div class="pdam-vision-mission-grid">
                <div class="pdam-vision-card">
                    <div class="pdam-vision-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <h3 class="pdam-vision-title">Visi Kami</h3>
                    <p class="pdam-vision-text">"{{ $vision }}"</p>
                </div>

                <div class="pdam-mission-card">
                    <div class="pdam-mission-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <h3 class="pdam-mission-title">Misi Kami</h3>
                    <ul class="pdam-mission-list">
                        @foreach ($missions as $mission)
                            <li>{{ $mission }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="pdam-values-section">
        <div class="pdam-container">
            <div class="pdam-values-grid">
                <div>
                    <h2 class="pdam-section-title pdam-text-left">{{ $valuesTitle }}</h2>
                    <p class="pdam-section-subtitle pdam-text-left pdam-mb-4">
                        {{ $valuesSubtitle }}
                    </p>

                    <div class="pdam-values-list">
                        <div class="pdam-value-item">
                            <div class="pdam-value-icon"></div>
                            <div>
                                <h4 class="pdam-value-title">{{ $value1Title }}</h4>
                                <p class="pdam-value-description">{{ $value1Description }}</p>
                            </div>
                        </div>
                        <div class="pdam-value-item">
                            <div class="pdam-value-icon"></div>
                            <div>
                                <h4 class="pdam-value-title">{{ $value2Title }}</h4>
                                <p class="pdam-value-description">{{ $value2Description }}</p>
                            </div>
                        </div>
                        <div class="pdam-value-item">
                            <div class="pdam-value-icon"></div>
                            <div>
                                <h4 class="pdam-value-title">{{ $value3Title }}</h4>
                                <p class="pdam-value-description">{{ $value3Description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pdam-coverage-card">
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
                    <div class="pdam-coverage-content">
                        <h3 class="pdam-coverage-title">{{ $coverageTitle }}</h3>
                        <p class="pdam-coverage-text">
                            Kami melayani {{ $kecamatan }} kecamatan. {{ $coverageDescription }}
                        </p>
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
        </div>
    </section>
</div>
