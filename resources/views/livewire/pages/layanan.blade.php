<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Cache;
use App\Enums\CompanyInfoKey;
use App\Models\CompanyInfo;

new #[Layout('components.layouts.public')] #[Title('Layanan Kami')] class extends Component {
    public function with(): array
    {
        return Cache::remember('page.layanan', 3600, function () {
            return [
                'companyPhone' => CompanyInfo::getValue(CompanyInfoKey::Phone, '(0423) 23344'),
            ];
        });
    }
}; ?>

<div>
    <!-- Page Header -->
    <section class="pdam-page-header">
        <div class="pdam-container">
            <h1 class="pdam-page-title text-white">Layanan Kami</h1>
            <p class="pdam-page-subtitle">
                Berbagai layanan air bersih untuk memenuhi kebutuhan rumah tangga, bisnis, dan industri Anda.
            </p>
        </div>
    </section>

    <!-- Services Grid -->
    <section class="pdam-section">
        <div class="pdam-container">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <!-- Pasang Baru -->
                <div class="group bg-white rounded-2xl p-6 lg:p-8 shadow-md hover:shadow-xl border border-gray-100 hover:border-[var(--pdam-primary-light)] transition-all duration-300 hover:-translate-y-1 flex flex-col" x-data x-intersect="$el.classList.add('animate-in')">
                    <div class="w-14 h-14 lg:w-16 lg:h-16 flex items-center justify-center bg-[var(--pdam-primary-lighter)] text-[var(--pdam-primary)] rounded-xl group-hover:bg-[var(--pdam-primary)] group-hover:text-white transition-all duration-300 mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            <path d="M9 12l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg lg:text-xl font-bold text-[var(--pdam-dark)] mb-3">Pasang Baru</h3>
                    <p class="text-[var(--pdam-gray-600)] text-sm lg:text-base leading-relaxed mb-4">
                        Pengajuan pemasangan sambungan air baru untuk rumah tinggal, apartemen, atau tempat usaha.
                        Proses cepat dan transparan.
                    </p>
                    <ul class="list-disc pl-5 text-[var(--pdam-gray-600)] text-sm lg:text-[0.9375rem] space-y-1.5 mb-6 flex-grow">
                        <li>Survei lokasi gratis</li>
                        <li>Estimasi biaya transparan</li>
                        <li>Pemasangan 3-7 hari kerja</li>
                    </ul>
                    <a href="{{ route('pasang-baru') }}" class="pdam-btn pdam-btn-primary w-full mt-auto">Ajukan Sekarang</a>
                </div>

                <!-- Cek Tagihan -->
                <div class="group bg-white rounded-2xl p-6 lg:p-8 shadow-md hover:shadow-xl border border-gray-100 hover:border-[var(--pdam-primary-light)] transition-all duration-300 hover:-translate-y-1 flex flex-col" x-data x-intersect="$el.classList.add('animate-in')">
                    <div class="w-14 h-14 lg:w-16 lg:h-16 flex items-center justify-center bg-[var(--pdam-primary-lighter)] text-[var(--pdam-primary)] rounded-xl group-hover:bg-[var(--pdam-primary)] group-hover:text-white transition-all duration-300 mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                    </div>
                    <h3 class="text-lg lg:text-xl font-bold text-[var(--pdam-dark)] mb-3">Cek Tagihan</h3>
                    <p class="text-[var(--pdam-gray-600)] text-sm lg:text-base leading-relaxed mb-4">
                        Lihat detail tagihan bulanan dan riwayat pemakaian air Anda.
                    </p>
                    <ul class="list-disc pl-5 text-[var(--pdam-gray-600)] text-sm lg:text-[0.9375rem] space-y-1.5 mb-6 flex-grow">
                        <li>Cek tagihan real-time</li>
                        <li>Riwayat pemakaian air lengkap</li>
                    </ul>
                    <a href="{{ route('cek-tagihan') }}" class="pdam-btn pdam-btn-primary w-full mt-auto">Cek Tagihan</a>
                </div>

                <!-- Lapor Gangguan -->
                <div class="group bg-white rounded-2xl p-6 lg:p-8 shadow-md hover:shadow-xl border border-gray-100 hover:border-[var(--pdam-primary-light)] transition-all duration-300 hover:-translate-y-1 flex flex-col" x-data x-intersect="$el.classList.add('animate-in')">
                    <div class="w-14 h-14 lg:w-16 lg:h-16 flex items-center justify-center bg-[var(--pdam-primary-lighter)] text-[var(--pdam-primary)] rounded-xl group-hover:bg-[var(--pdam-primary)] group-hover:text-white transition-all duration-300 mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path
                                d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg lg:text-xl font-bold text-[var(--pdam-dark)] mb-3">Lapor Gangguan</h3>
                    <p class="text-[var(--pdam-gray-600)] text-sm lg:text-base leading-relaxed mb-4">
                        Laporkan masalah seperti pipa bocor, meteran rusak, atau gangguan aliran air lainnya untuk
                        penanganan cepat.
                    </p>
                    <ul class="list-disc pl-5 text-[var(--pdam-gray-600)] text-sm lg:text-[0.9375rem] space-y-1.5 mb-6 flex-grow">
                        <li>Respon dalam 24 jam</li>
                        <li>Lacak status real-time</li>
                        <li>Tim teknis profesional</li>
                    </ul>
                    <a href="{{ route('pengaduan') }}" class="pdam-btn pdam-btn-primary w-full mt-auto">Lapor Sekarang</a>
                </div>

                <!-- Simulasi Tarif -->
                <div class="group bg-white rounded-2xl p-6 lg:p-8 shadow-md hover:shadow-xl border border-gray-100 hover:border-[var(--pdam-primary-light)] transition-all duration-300 hover:-translate-y-1 flex flex-col" x-data x-intersect="$el.classList.add('animate-in')">
                    <div class="w-14 h-14 lg:w-16 lg:h-16 flex items-center justify-center bg-[var(--pdam-primary-lighter)] text-[var(--pdam-primary)] rounded-xl group-hover:bg-[var(--pdam-primary)] group-hover:text-white transition-all duration-300 mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                            <line x1="8" y1="21" x2="16" y2="21"></line>
                            <line x1="12" y1="17" x2="12" y2="21"></line>
                        </svg>
                    </div>
                    <h3 class="text-lg lg:text-xl font-bold text-[var(--pdam-dark)] mb-3">Simulasi Tarif</h3>
                    <p class="text-[var(--pdam-gray-600)] text-sm lg:text-base leading-relaxed mb-4">
                        Hitung estimasi tagihan bulanan berdasarkan golongan pelanggan dan perkiraan pemakaian air Anda.
                    </p>
                    <ul class="list-disc pl-5 text-[var(--pdam-gray-600)] text-sm lg:text-[0.9375rem] space-y-1.5 mb-6 flex-grow">
                        <li>Pilih golongan tarif</li>
                        <li>Masukkan estimasi pemakaian</li>
                        <li>Hasil kalkulasi instan</li>
                    </ul>
                    <a href="{{ route('simulasi-tarif') }}" class="pdam-btn pdam-btn-primary w-full mt-auto">Simulasi Sekarang</a>
                </div>

                <!-- Lapor Angka Meter Mandiri -->
                <div class="group bg-white rounded-2xl p-6 lg:p-8 shadow-md hover:shadow-xl border border-gray-100 hover:border-[var(--pdam-primary-light)] transition-all duration-300 hover:-translate-y-1 flex flex-col" x-data x-intersect="$el.classList.add('animate-in')">
                    <div class="w-14 h-14 lg:w-16 lg:h-16 flex items-center justify-center bg-[var(--pdam-primary-lighter)] text-[var(--pdam-primary)] rounded-xl group-hover:bg-[var(--pdam-primary)] group-hover:text-white transition-all duration-300 mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h3 class="text-lg lg:text-xl font-bold text-[var(--pdam-dark)] mb-3">Lapor Angka Meter Mandiri</h3>
                    <p class="text-[var(--pdam-gray-600)] text-sm lg:text-base leading-relaxed mb-4">
                        Laporkan angka meter air Anda secara mandiri untuk akurasi tagihan bulanan.
                    </p>
                    <ul class="list-disc pl-5 text-[var(--pdam-gray-600)] text-sm lg:text-[0.9375rem] space-y-1.5 mb-6 flex-grow">
                        <li>Foto meteran yang jelas</li>
                        <li>Input angka meter terkini</li>
                        <li>Tagihan lebih akurat</li>
                    </ul>
                    <a href="{{ route('lapor-angka-meter-mandiri') }}" class="pdam-btn pdam-btn-primary w-full mt-auto">Lapor Sekarang</a>
                </div>

                <!-- Download Center -->
                <div class="group bg-white rounded-2xl p-6 lg:p-8 shadow-md hover:shadow-xl border border-gray-100 hover:border-[var(--pdam-primary-light)] transition-all duration-300 hover:-translate-y-1 flex flex-col" x-data x-intersect="$el.classList.add('animate-in')">
                    <div class="w-14 h-14 lg:w-16 lg:h-16 flex items-center justify-center bg-[var(--pdam-primary-lighter)] text-[var(--pdam-primary)] rounded-xl group-hover:bg-[var(--pdam-primary)] group-hover:text-white transition-all duration-300 mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                    </div>
                    <h3 class="text-lg lg:text-xl font-bold text-[var(--pdam-dark)] mb-3">Download Center</h3>
                    <p class="text-[var(--pdam-gray-600)] text-sm lg:text-base leading-relaxed mb-4">
                        Unduh formulir, peraturan, panduan, dan dokumen penting lainnya yang Anda butuhkan.
                    </p>
                    <ul class="list-disc pl-5 text-[var(--pdam-gray-600)] text-sm lg:text-[0.9375rem] space-y-1.5 mb-6 flex-grow">
                        <li>Formulir pendaftaran</li>
                        <li>Peraturan & panduan</li>
                        <li>Dokumen resmi lainnya</li>
                    </ul>
                    <a href="{{ route('download') }}" class="pdam-btn pdam-btn-primary w-full mt-auto">Lihat Download</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="pdam-section bg-gradient-to-br from-[var(--pdam-primary)] to-[var(--pdam-primary-darker)]">
        <div class="pdam-container text-center">
            <h2 class="text-2xl lg:text-3xl font-bold text-white mb-4">Butuh Bantuan Lainnya?</h2>
            <p class="text-white/90 max-w-xl mx-auto mb-8 text-sm lg:text-base">
                Tim layanan pelanggan kami siap membantu Anda 24 jam sehari, 7 hari seminggu. Jangan ragu untuk
                menghubungi kami.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('kontak') }}" class="pdam-btn bg-white text-[var(--pdam-primary)] hover:bg-gray-100 w-full sm:w-auto">
                    Hubungi Kami
                </a>
                <a href="tel:{{ $companyPhone }}" class="pdam-btn pdam-btn-outline border-white text-white hover:bg-white/10 w-full sm:w-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path
                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                        </path>
                    </svg>
                    {{ $companyPhone }}
                </a>
            </div>
        </div>
    </section>
</div>
