<?php

declare(strict_types=1);

use App\Livewire\Concerns\WithRateLimiting;
use App\Models\Aduan;
use App\Models\Faq;
use App\Models\{JenisAduan, Kelurahan, Pelanggan};
use Illuminate\Support\Collection;
use Livewire\Attributes\{Layout, Title, Validate};
use Livewire\WithFileUploads;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] #[Title('Layanan Pengaduan')] class extends Component {
    use WithFileUploads;
    use WithRateLimiting;

    #[Validate('required|string|max:255')]
    public string $nama = '';

    #[Validate('nullable|string|max:20')]
    public string $nomorPelanggan = '';

    #[Validate('required|string|exists:billing.jenis_aduan,id')]
    public string $jenisAduanId = '';

    #[Validate('required|string|min:20')]
    public string $keterangan = '';

    #[Validate('required|string|max:15|regex:/^[0-9]+$/')]
    public string $noTelp = '';

    #[Validate('nullable|string|exists:billing.cabang,id')]
    public ?string $cabangId = null;

    #[Validate('nullable|string|exists:billing.kecamatan,id')]
    public ?string $kecamatanId = null;

    #[Validate('nullable|string|exists:billing.kelurahan,id')]
    public ?string $kelurahanId = null;

    #[Validate('nullable|string|exists:billing.jalan,id')]
    public ?string $jalanId = null;

    #[Validate('nullable|string|exists:billing.pelanggan,id')]
    public ?string $pelangganId = null;

    public bool $submitted = false;

    public ?Aduan $aduan = null;

    protected function messages(): array
    {
        return [
            'nama.required' => 'Nama harus diisi.',
            'nomorPelanggan.max' => 'Nomor pelanggan maksimal 20 digit.',
            'noTelp.required' => 'Nomor telepon harus diisi.',
            'noTelp.max' => 'Nomor telepon maksimal 20 digit.',
            'noTelp.regex' => 'Nomor telepon harus valid.',
            'keterangan.required' => 'Keterangan harus diisi.',
            'keterangan.min' => 'Keterangan minimal 20 karakter.',
            'jenisAduanId.required' => 'Jenis aduan harus dipilih.',
        ];
    }

    public function submit(): void
    {
        $this->rateLimit(5);
        $this->validate();
        try {
            $this->aduan = Aduan::create([
                'nomor' => time(),
                'kecamatan_id' => $this->kecamatanId,
                'cabang_id' => $this->cabangId,
                'kelurahan_id' => $this->kelurahanId,
                'jalan_id' => $this->jalanId,
                'nama' => $this->nama,
                'pelanggan_id' => $this->pelangganId,
                'no_telp' => $this->noTelp,
                'jenis_aduan_id' => $this->jenisAduanId,
                'keterangan' => $this->keterangan,
                'sumber_aduan' => 'web',
                'user' => '',
            ]);
        } catch (\Exception $e) {
        }

        $this->submitted = true;
    }

    public function updatedNomorPelanggan(): void
    {
        $pelanggan = Pelanggan::where('no_pelanggan', $this->nomorPelanggan)->first();
        if ($pelanggan) {
            $this->nama = $pelanggan->nama;
            $this->noTelp = $pelanggan->no_hp ?? '-';
            $this->kelurahanId = $pelanggan->kelurahan_id;
            $this->jalanId = $pelanggan->jalan_id;
            $this->kecamatanId = $pelanggan->kecamatan_id;
            $this->cabangId = $pelanggan->cabang_id;
            $this->pelangganId = $pelanggan->id;

            // Dispatch events to update select2 values
            $this->dispatch('update-select2-value', name: 'kelurahanId', value: $pelanggan->kelurahan_id);

            // Update jalan options and value
            $kelurahan = Kelurahan::find($pelanggan->kelurahan_id);
            if ($kelurahan) {
                $this->dispatch('update-select2-options', name: 'jalanId', options: $kelurahan->jalan->pluck('nama', 'id')->toArray());
                $this->dispatch('update-select2-disabled', name: 'jalanId', disabled: false);
                $this->dispatch('update-select2-value', name: 'jalanId', value: $pelanggan->jalan_id);
            }
        }
    }

    public function resetForm(): void
    {
        $this->reset();

        // Reset all select2 components
        $this->dispatch('update-select2-value', name: 'jenisAduanId', value: '');
        $this->dispatch('update-select2-value', name: 'kelurahanId', value: '');
        $this->dispatch('update-select2-value', name: 'jalanId', value: '');
        $this->dispatch('update-select2-options', name: 'jalanId', options: []);
        $this->dispatch('update-select2-disabled', name: 'jalanId', disabled: true);
    }

    public function updatedKelurahanId(): void
    {
        if ($this->kelurahanId) {
            $kelurahan = Kelurahan::find($this->kelurahanId);
            $this->kecamatanId = $kelurahan->kecamatan_id;
            $this->cabangId = $kelurahan->kecamatan->cabang_id;
            $this->jalanId = null;

            // Dispatch event to update jalan options
            $this->dispatch('update-select2-options', name: 'jalanId', options: $kelurahan->jalan->pluck('nama', 'id')->toArray());
            $this->dispatch('update-select2-disabled', name: 'jalanId', disabled: false);
        } else {
            $this->jalanId = null;
            $this->dispatch('update-select2-options', name: 'jalanId', options: []);
            $this->dispatch('update-select2-disabled', name: 'jalanId', disabled: true);
        }
    }

    public function getJalanProperty(): Collection
    {
        if ($this->kelurahanId) {
            $kelurahan = Kelurahan::find($this->kelurahanId);

            return $kelurahan ? $kelurahan->jalan : collect();
        }

        return collect();
    }

    public function with(): array
    {
        return [
            'faqs' => Faq::active()->byCategory('pengaduan')->ordered()->take(3)->get(),
            'jenisAduan' => JenisAduan::all(),
            'kelurahan' => Kelurahan::all(),
            'jalan' => $this->jalan,
        ];
    }
}; ?>

<div>
    <!-- Page Header -->
    <section class="pdam-page-header">
        <div class="pdam-container">
            <h1 class="pdam-page-title text-white">Layanan Pengaduan</h1>
            <p class="pdam-page-subtitle">
                Sampaikan keluhan atau kendala layanan air Anda. Tim kami akan segera menindaklanjuti laporan Anda demi
                kenyamanan bersama.
            </p>
        </div>
    </section>

    <!-- Complaint Section -->
    <section class="pdam-complaint-section">
        <div class="pdam-container">
            @if ($submitted && $aduan)
                <!-- Success Message -->
                <div class="pdam-card pdam-text-center" style="max-width: 600px; margin: 0 auto; padding: 3rem;">
                    <div
                        style="width: 80px; height: 80px; background: var(--pdam-success); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                            fill="none" stroke="white" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--pdam-dark); margin-bottom: 0.75rem;">
                        Laporan Berhasil Dikirim!</h2>
                    <p style="color: var(--pdam-gray-600); margin-bottom: 1.5rem;">
                        Terima kasih telah melapor. Tim kami akan segera menindaklanjuti keluhan Anda.
                    </p>

                    <div
                        style="background: var(--pdam-gray-100); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 1.5rem;">
                        <div style="font-size: 0.875rem; color: var(--pdam-gray-500);">Nomor Tiket Pengaduan</div>
                        <div style="font-size: 1.75rem; font-weight: 800; color: var(--pdam-primary);">
                            {{ $aduan->nomor }}</div>
                        <p style="font-size: 0.875rem; color: var(--pdam-gray-500); margin-top: 0.5rem;">
                            Simpan nomor ini untuk melacak status pengaduan Anda.
                        </p>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: center;">
                        <a href="{{ route('pengaduan.lacak') }}?ticket={{ $aduan->nomor }}"
                            class="pdam-btn pdam-btn-primary">Lacak Status</a>
                        <button wire:click="resetForm" class="pdam-btn pdam-btn-secondary">Buat Laporan Baru</button>
                    </div>
                </div>
            @else
                <div class="pdam-complaint-layout">
                    <!-- Sidebar -->
                    <div class="pdam-complaint-sidebar">
                        <h2 class="pdam-complaint-sidebar-title text-white">Butuh Bantuan Mendesak?</h2>
                        <p class="pdam-complaint-sidebar-text">
                            Untuk keadaan darurat seperti pipa pecah besar atau gangguan total area, Anda dapat
                            menghubungi call center kami.
                        </p>

                        <div class="pdam-complaint-contact">
                            <div class="pdam-complaint-contact-item">
                                <div class="pdam-complaint-contact-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="pdam-complaint-contact-label">Call Center 24 Jam</div>
                                    <div class="pdam-complaint-contact-value">(021) 555-0123</div>
                                </div>
                            </div>
                            <div class="pdam-complaint-contact-item">
                                <div class="pdam-complaint-contact-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="pdam-complaint-contact-label">WhatsApp Center</div>
                                    <div class="pdam-complaint-contact-value">0812-3456-7890</div>
                                </div>
                            </div>
                        </div>

                        <div class="pdam-complaint-hours">
                            <div class="pdam-complaint-hours-title">Jam Operasional Kantor</div>
                            <div class="pdam-complaint-hours-text">
                                Senin - Jumat: 08.00 - 16.00 WIB<br>
                                Sabtu - Minggu: Tutup
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <div class="pdam-complaint-form">
                        <!-- Track Existing Complaint CTA -->
                        <div style="background: linear-gradient(135deg, var(--pdam-primary-50) 0%, var(--pdam-primary-100) 100%); border: 1px solid var(--pdam-primary-200); border-radius: var(--radius-lg); padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 40px; height: 40px; background: var(--pdam-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <path d="m21 21-4.3-4.3"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--pdam-dark); font-size: 0.95rem;">Sudah pernah melapor?</div>
                                    <div style="font-size: 0.85rem; color: var(--pdam-gray-600);">Lacak status pengaduan Anda</div>
                                </div>
                            </div>
                            <a href="{{ route('pengaduan.lacak') }}" class="pdam-btn pdam-btn-primary" style="white-space: nowrap;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                                    <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"></path>
                                </svg>
                                Lacak Pengaduan
                            </a>
                        </div>

                        <h2 class="pdam-complaint-form-title">Formulir Pengaduan</h2>

                        <form wire:submit="submit">
                            <div class="pdam-form-row">
                                <div class="pdam-form-group">
                                    <label class="pdam-form-label">Nama Lengkap</label>
                                    <input type="text" wire:model="nama" class="pdam-form-input"
                                        placeholder="Masukkan nama Anda">
                                    @error('nama')
                                        <span
                                            style="font-size: 0.875rem; color: var(--pdam-danger);">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="pdam-form-group">
                                    <label class="pdam-form-label">No. Sambungan / ID</label>
                                    <input type="text" wire:model.live.debounce.1000ms="nomorPelanggan"
                                        class="pdam-form-input" placeholder="Contoh: 12345678">
                                    @error('nomorPelanggan')
                                        <span
                                            style="font-size: 0.875rem; color: var(--pdam-danger);">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="pdam-form-row">
                                <div class="pdam-form-group">
                                    <label class="pdam-form-label">Nomor Telepon</label>
                                    <input type="text" wire:model="noTelp" class="pdam-form-input"
                                        placeholder="Masukkan nomor telepon Anda">
                                    @error('noTelp')
                                        <span
                                            style="font-size: 0.875rem; color: var(--pdam-danger);">{{ $message }}</span>
                                    @enderror
                                </div>


                                <div class="pdam-form-group">
                                    <label class="pdam-form-label">Jenis Aduan</label>
                                    <x-select2 name="jenisAduanId" wireModel="jenisAduanId" :options="$jenisAduan->pluck('nama', 'id')->toArray()"
                                        :value="$jenisAduanId" placeholder="Pilih Jenis Aduan" />
                                    @error('jenisAduanId')
                                        <span
                                            style="font-size: 0.875rem; color: var(--pdam-danger);">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>


                            <div class="pdam-form-row">
                                <div class="pdam-form-group">
                                    <label class="pdam-form-label">Kelurahan</label>
                                    <x-select2 name="kelurahanId" wireModel="kelurahanId" :options="$kelurahan->pluck('nama', 'id')->toArray()"
                                        placeholder="Pilih Kelurahan" :value="$kelurahanId" />
                                    @error('kelurahanId')
                                        <span
                                            style="font-size: 0.875rem; color: var(--pdam-danger);">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="pdam-form-group">
                                    <label class="pdam-form-label">Jalan</label>
                                    <x-select2 name="jalanId" wireModel="jalanId" :options="$jalan->pluck('nama', 'id')->toArray()"
                                        placeholder="Pilih Jalan" :disabled="!$kelurahanId" :value="$jalanId" />
                                    @error('jalanId')
                                        <span
                                            style="font-size: 0.875rem; color: var(--pdam-danger);">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>


                            <div class="pdam-form-group">
                                <label class="pdam-form-label">Deskripsi Aduan</label>
                                <textarea wire:model="keterangan" class="pdam-form-input pdam-form-textarea"
                                    placeholder="Jelaskan detail masalah yang Anda alami..." rows="4"></textarea>
                                @error('keterangan')
                                    <span
                                        style="font-size: 0.875rem; color: var(--pdam-danger);">{{ $message }}</span>
                                @enderror
                            </div>


                            <button type="submit" class="pdam-btn pdam-btn-primary pdam-btn-lg" style="width: 100%;"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="submit">Kirim Laporan Pengaduan</span>
                                <span wire:loading wire:target="submit">Mengirim...</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- FAQ Section -->
            <div class="pdam-faq-section">
                <h3 class="pdam-section-title pdam-text-center">Pertanyaan Umum</h3>
                <div class="pdam-faq-grid">
                    @forelse($faqs as $faq)
                        <a href="{{ route('faq', ['category' => 'pengaduan', 'faq' => $faq->id]) }}" class="pdam-faq-item pdam-faq-item-link">
                            <span>{{ $faq->question }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="pdam-faq-item-arrow">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </a>
                    @empty
                        <a href="{{ route('faq') }}" class="pdam-faq-item pdam-faq-item-link">
                            <span>Berapa lama proses perbaikan?</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="pdam-faq-item-arrow">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </a>
                        <a href="{{ route('faq') }}" class="pdam-faq-item pdam-faq-item-link">
                            <span>Bagaimana cara cek status laporan?</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="pdam-faq-item-arrow">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </a>
                        <a href="{{ route('faq') }}" class="pdam-faq-item pdam-faq-item-link">
                            <span>Syarat pasang baru?</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="pdam-faq-item-arrow">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </a>
                    @endforelse
                </div>
                <div class="pdam-text-center" style="margin-top: 1.5rem;">
                    <a href="{{ route('faq', ['category' => 'pengaduan']) }}" class="pdam-btn pdam-btn-secondary">
                        Lihat Semua FAQ
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
