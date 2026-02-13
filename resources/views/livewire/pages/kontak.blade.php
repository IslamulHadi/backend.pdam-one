<?php

declare(strict_types=1);

use App\Enums\CompanyInfoKey;
use App\Livewire\Concerns\WithRateLimiting;
use App\Models\CompanyInfo;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new 
#[Layout('components.layouts.public')] 
#[Title('Hubungi Kami')] 
class extends Component {
    use WithRateLimiting;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:20')]
    public string $phone = '';

    #[Validate('required|string|in:umum,layanan,tagihan,kerjasama,lainnya')]
    public string $category = '';

    #[Validate('required|string|min:10|max:5000')]
    public string $message = '';

    public bool $submitted = false;

    /** Honeypot field - should always be empty */
    public string $website = '';

    public function submit(): void
    {
        $this->rateLimit(5);

        // Honeypot check
        if ($this->website !== '') {
            return;
        }

        $this->validate();

        ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'category' => $this->category,
            'message' => $this->message,
        ]);

        $this->submitted = true;
    }

    public function resetForm(): void
    {
        $this->reset();
    }

    public function with(): array
    {
        return Cache::remember('page.kontak', 3600, function () {
            return [
                'companyPhone' => CompanyInfo::getValue(CompanyInfoKey::Phone, '(0423) 23344'),
                'companyEmail' => CompanyInfo::getValue(CompanyInfoKey::Email, 'pdamone@amertatoraya.com'),
                'companyAddress' => CompanyInfo::getValue(CompanyInfoKey::OfficeAddress, 'Jalan Tedong Bonga, Kompleks Pasar Bolu, Kelurahan Tallunglipu, Kecamatan Tallunglipu, Kabupaten Toraja Utara, Provinsi Sulawesi Selatan'),
            ];
        });
    }
}; ?>



<div>
    <!-- Breadcrumb -->
    <section style="background: var(--pdam-gray-100); padding: 1rem 0;">
        <div class="pdam-container">
            <nav class="pdam-breadcrumb" style="justify-content: flex-start; margin: 0;">
                <a href="{{ route('home') }}">Beranda</a>
                <span>/</span>
                <span class="pdam-text-primary">Kontak</span>
            </nav>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="pdam-contact-section">
        <div class="pdam-container">
            <div style="margin-bottom: 2rem;">
                <h1 class="pdam-section-title pdam-text-left" style="color: var(--pdam-primary);">Hubungi Kami</h1>
                <p class="pdam-section-subtitle pdam-text-left pdam-mb-0">
                    Kami siap membantu Anda. Silakan hubungi kami melalui saluran resmi di bawah ini atau kunjungi
                    kantor pelayanan terdekat.
                </p>
            </div>

            <div class="pdam-contact-layout">
                <!-- Left Column -->
                <div>
                    <!-- Contact Cards -->
                    <div class="pdam-contact-info">
                        <div class="pdam-contact-card">
                            <div class="pdam-contact-card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <div class="pdam-contact-card-title">Call Center</div>
                                <div class="pdam-contact-card-subtitle">24 Jam Bebas Pulsa</div>
                                <a href="tel:{{ $companyPhone }}"
                                    class="pdam-contact-card-value">{{ $companyPhone }}</a>
                            </div>
                        </div>

                        <div class="pdam-contact-card">
                            <div class="pdam-contact-card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                    </path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </div>
                            <div>
                                <div class="pdam-contact-card-title">Email</div>
                                <div class="pdam-contact-card-subtitle">Layanan Pelanggan</div>
                                <a href="mailto:{{ $companyEmail }}"
                                    class="pdam-contact-card-value">{{ $companyEmail }}</a>
                            </div>
                        </div>
                    </div>

                    <!-- Office Address -->
                    <div class="pdam-contact-card" style="margin-bottom: 2rem;">
                        <div class="pdam-contact-card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                        <div>
                            <div class="pdam-contact-card-title">Kantor Pusat</div>
                            <div style="color: var(--pdam-gray-600); font-size: 0.9375rem;">{{ $companyAddress }}</div>
                        </div>
                    </div>

                    <!-- Map -->
                    <div>
                        <h3 style="font-weight: 600; color: var(--pdam-dark); margin-bottom: 1rem;">Lokasi Kami</h3>
                        <div class="pdam-contact-map">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.5011094509628!2d119.9119667!3d-2.9583615999999995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2d93c2710a9a9215%3A0x6d25f9a0261e6a2f!2sPDAM%20KABUPATEN%20TORAJA%20UTARA!5e0!3m2!1sen!2sid!4v1767795606901!5m2!1sen!2sid"
                                width="100%" height="100%" style="border:0;" allowfullscreen=""
                                loading="lazy"></iframe>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Form -->
                <div class="pdam-contact-form-card">
                    @if ($submitted)
                        <div class="pdam-text-center" style="padding: 2rem 0;">
                            <div
                                style="width: 80px; height: 80px; background: var(--pdam-success); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                    viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <h3
                                style="font-size: 1.25rem; font-weight: 700; color: var(--pdam-dark); margin-bottom: 0.5rem;">
                                Pesan Terkirim!</h3>
                            <p style="color: var(--pdam-gray-600); margin-bottom: 1.5rem;">
                                Terima kasih telah menghubungi kami. Tim kami akan segera merespons pesan Anda.
                            </p>
                            <button wire:click="resetForm" class="pdam-btn pdam-btn-primary">Kirim Pesan Lain</button>
                        </div>
                    @else
                        <h2
                            style="font-size: 1.5rem; font-weight: 700; color: var(--pdam-dark); margin-bottom: 0.75rem;">
                            Kirim Pesan</h2>
                        <p style="color: var(--pdam-gray-600); margin-bottom: 1.5rem; font-size: 0.9375rem;">
                            Silakan isi formulir di bawah ini untuk pertanyaan umum atau kerjasama. Untuk keluhan
                            teknis, gunakan menu <a href="{{ route('pengaduan') }}" class="pdam-text-primary"
                                style="font-weight: 600;">Lapor Gangguan</a>.
                        </p>

                        <form wire:submit="submit">
                            @error('rateLimiter')
                                <div style="padding: 0.75rem 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; margin-bottom: 1rem; color: #dc2626; font-size: 0.875rem;">
                                    {{ $message }}
                                </div>
                            @enderror

                            {{-- Honeypot --}}
                            <div style="position: absolute; left: -9999px;" aria-hidden="true">
                                <input type="text" wire:model="website" name="website" tabindex="-1" autocomplete="off">
                            </div>

                            <div class="pdam-form-group">
                                <label class="pdam-form-label">Nama Lengkap</label>
                                <input type="text" wire:model="name" class="pdam-form-input"
                                    placeholder="Masukkan nama lengkap Anda">
                                @error('name')
                                    <span style="font-size: 0.875rem; color: var(--pdam-danger);">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="pdam-form-row">
                                <div class="pdam-form-group">
                                    <label class="pdam-form-label">Email</label>
                                    <input type="email" wire:model="email" class="pdam-form-input"
                                        placeholder="contoh@email.com">
                                    @error('email')
                                        <span
                                            style="font-size: 0.875rem; color: var(--pdam-danger);">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="pdam-form-group">
                                    <label class="pdam-form-label">No. Telepon / WA</label>
                                    <input type="text" wire:model="phone" class="pdam-form-input"
                                        placeholder="0812...">
                                </div>
                            </div>

                            <div class="pdam-form-group">
                                <label class="pdam-form-label">Kategori Pesan</label>
                                <select wire:model="category" class="pdam-form-input pdam-form-select">
                                    <option value="">Pilih kategori</option>
                                    <option value="umum">Pertanyaan Umum</option>
                                    <option value="layanan">Informasi Layanan</option>
                                    <option value="tagihan">Pertanyaan Tagihan</option>
                                    <option value="kerjasama">Kerjasama</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                                @error('category')
                                    <span
                                        style="font-size: 0.875rem; color: var(--pdam-danger);">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="pdam-form-group">
                                <label class="pdam-form-label">Isi Pesan</label>
                                <textarea wire:model="message" class="pdam-form-input pdam-form-textarea"
                                    placeholder="Tuliskan pesan Anda secara detail di sini..." rows="5"></textarea>
                                @error('message')
                                    <span
                                        style="font-size: 0.875rem; color: var(--pdam-danger);">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="pdam-btn pdam-btn-primary pdam-btn-lg" style="width: 100%;"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="submit">Kirim Pesan</span>
                                <span wire:loading wire:target="submit">Mengirim...</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
