<?php

declare(strict_types=1);

use App\Livewire\Concerns\WithRateLimiting;
use App\Models\Bacameter;
use App\Models\Pelanggan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\{Layout, Title, Validate};
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('components.layouts.public')] #[Title('Lapor Angka Meter Mandiri')] class extends Component {
    use WithFileUploads;
    use WithRateLimiting;

    #[Validate('required|string|max:20')]
    public string $noPelanggan = '';

    #[Validate('required|numeric|min:0')]
    public int|string $standKini = '';

    #[Validate('required|image|max:5120')]
    public $foto;

    public bool $submitted = false;

    public ?string $errorMessage = null;

    public ?array $result = null;

    protected function messages(): array
    {
        return [
            'noPelanggan.required' => 'ID Pelanggan harus diisi.',
            'noPelanggan.max' => 'ID Pelanggan maksimal 20 karakter.',
            'standKini.required' => 'Angka meter harus diisi.',
            'standKini.numeric' => 'Angka meter harus berupa angka.',
            'standKini.min' => 'Angka meter tidak boleh negatif.',
            'foto.required' => 'Foto meteran harus diunggah.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 5MB.',
        ];
    }

    public function submit(): void
    {
        $this->rateLimit(5);
        $this->errorMessage = null;
        $this->validate();

        $today = Carbon::now();
        $lastDayOfMonth = Carbon::now()->endOfMonth()->startOfDay();

        if ($today->isSameDay($lastDayOfMonth)) {
            $this->errorMessage = 'Baca meter mandiri tidak dapat dilakukan di akhir bulan.';
            return;
        }

        // Find pelanggan by no_pelanggan
        $pelanggan = Pelanggan::where('no_pelanggan', $this->noPelanggan)->first();

        if (!$pelanggan) {
            $this->errorMessage = 'Nomor Pelanggan tidak ditemukan.';
            return;
        }

        // Find bacameter record for current period (first day of current month)
        $currentPeriode = Carbon::now()->startOfMonth()->format('Y-m-d');

        $bacameter = Bacameter::where('no_pelanggan', $pelanggan->no_pelanggan)->where('periode', $currentPeriode)->first();

        if (!$bacameter) {
            $this->errorMessage = 'Data baca untuk periode ini tidak ditemukan.';
            return;
        }

        // Calculate pemakaian
        $standKini = (int) $this->standKini;
        $standLalu = $bacameter->stan_lalu ?? 0;
        $pakai = $standKini - $standLalu;

        if ($pakai < 0) {
            $this->errorMessage = 'Angka meter tidak valid. Angka meter sekarang harus lebih besar dari angka meter lalu (' . $standLalu . ').';
            return;
        }

        $fileName = time() . '_' . date('Ym') . '.' . $this->foto->getClientOriginalExtension();
        $fotoPath = $this->foto->storeAs('bacameter/' . $currentPeriode . '/mandiri', $fileName, 'billing');

        $bacameter->update([
            'stan_ini' => $standKini,
            'stan_ini_awal' => $standKini,
            'pakai' => $pakai,
            'foto' => $fotoPath,
            'status_baca' => 'Bacameter Mandiri',
            'tgl_upload' => Carbon::now(),
            'tgl_baca' => Carbon::now(),
        ]);

        $this->result = [
            'nama' => $pelanggan->nama,
            'no_pelanggan' => $pelanggan->no_pelanggan,
            'stand_lalu' => $standLalu,
            'stand_kini' => $standKini,
            'pakai' => $pakai,
            'periode' => Carbon::parse($currentPeriode)->translatedFormat('F Y'),
            'foto' => Storage::disk('billing')->url($fotoPath),
        ];

        $this->submitted = true;
    }

    public function resetForm(): void
    {
        $this->reset(['noPelanggan', 'standKini', 'foto', 'submitted', 'errorMessage', 'result']);
    }
}; ?>

<div>
    <!-- Page Header -->
    <section class="pdam-page-header">
        <div class="pdam-container">
            <h1 class="pdam-page-title text-white">Lapor Angka Meter Mandiri</h1>
            <p class="pdam-page-subtitle">
                Laporkan angka meter air Anda secara mandiri untuk memastikan tagihan yang akurat.
            </p>
        </div>
    </section>

    <!-- Main Section -->
    <section class="pdam-section">
        <div class="pdam-container">
            @if ($submitted && $result)
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
                        Terima kasih telah melaporkan angka meter Anda.
                    </p>

                    <div
                        style="background: var(--pdam-gray-100); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 1.5rem; text-align: left;">
                        <div style="display: grid; gap: 0.75rem;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--pdam-gray-500);">Nama Pelanggan</span>
                                <span style="font-weight: 600; color: var(--pdam-dark);">{{ $result['nama'] }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--pdam-gray-500);">No. Pelanggan</span>
                                <span
                                    style="font-weight: 600; color: var(--pdam-dark);">{{ $result['no_pelanggan'] }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--pdam-gray-500);">Periode</span>
                                <span style="font-weight: 600; color: var(--pdam-dark);">{{ $result['periode'] }}</span>
                            </div>
                            <hr style="border: none; border-top: 1px solid var(--pdam-gray-200); margin: 0.5rem 0;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--pdam-gray-500);">Stand Lalu</span>
                                <span
                                    style="font-weight: 600; color: var(--pdam-dark);">{{ number_format($result['stand_lalu']) }}
                                    m³</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--pdam-gray-500);">Stand Sekarang</span>
                                <span
                                    style="font-weight: 600; color: var(--pdam-dark);">{{ number_format($result['stand_kini']) }}
                                    m³</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--pdam-gray-500);">Pemakaian</span>
                                <span
                                    style="font-weight: 700; color: var(--pdam-primary); font-size: 1.125rem;">{{ number_format($result['pakai']) }}
                                    m³</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--pdam-gray-500);">Foto Meteran</span>
                                <a href="{{ $result['foto'] }}" target="_blank">
                                    <img src="{{ $result['foto'] }}" alt="Foto Meteran"
                                        style="max-width: 100px; max-height: 100px; border-radius: var(--radius-md); object-fit: cover;">
                                </a>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: center;">
                        <a href="{{ route('home') }}" class="pdam-btn pdam-btn-primary">Kembali ke Beranda</a>
                        <button wire:click="resetForm" class="pdam-btn pdam-btn-secondary">Lapor Lagi</button>
                    </div>
                </div>
            @else
                <div style="max-width: 700px; margin: 0 auto;">
                    <!-- Form Card -->
                    <div class="pdam-card" style="padding: 2.5rem;">
                        <div class="pdam-text-center" style="margin-bottom: 2rem;">
                            <div
                                style="width: 64px; height: 64px; background: var(--pdam-primary-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                    viewBox="0 0 24 24" fill="none" stroke="var(--pdam-primary)" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <h2
                                style="font-size: 1.5rem; font-weight: 700; color: var(--pdam-dark); margin-bottom: 0.5rem;">
                                Formulir Lapor Meter</h2>
                            <p style="color: var(--pdam-gray-500);">
                                Silakan isi data berikut dengan benar. Pastikan foto meteran terlihat jelas.
                            </p>
                        </div>

                        @if ($errorMessage)
                            <div
                                style="background: #fef2f2; border: 1px solid #fecaca; border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.5rem; display: flex; gap: 0.75rem; align-items: flex-start;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    style="flex-shrink: 0; margin-top: 2px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                <span style="color: #dc2626; font-size: 0.9375rem;">{{ $errorMessage }}</span>
                            </div>
                        @endif

                        <form wire:submit="submit">
                            <div class="pdam-form-row">
                                <div class="pdam-form-group">
                                    <label class="pdam-form-label">ID Pelanggan <span
                                            style="color: var(--pdam-danger);">*</span></label>
                                    <input type="text" wire:model="noPelanggan" class="pdam-form-input"
                                        placeholder="Masukkan nomor pelanggan Anda">
                                    @error('noPelanggan')
                                        <span
                                            style="font-size: 0.875rem; color: var(--pdam-danger);">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="pdam-form-group">
                                    <label class="pdam-form-label">Angka Meter (m³) <span
                                            style="color: var(--pdam-danger);">*</span></label>
                                    <input type="number" wire:model="standKini" class="pdam-form-input"
                                        placeholder="Masukkan angka pada meteran" min="0">
                                    @error('standKini')
                                        <span
                                            style="font-size: 0.875rem; color: var(--pdam-danger);">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="pdam-form-group">
                                <label class="pdam-form-label">Upload Foto Meteran <span
                                        style="color: var(--pdam-danger);">*</span></label>

                                <!-- Hidden file input -->
                                <input type="file" wire:model="foto" accept="image/*" id="foto-input"
                                    style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); border: 0;">

                                <label for="foto-input"
                                    style="display: block; border: 2px dashed var(--pdam-gray-300); border-radius: var(--radius-lg); padding: 2rem; text-align: center; background: var(--pdam-gray-50); transition: all 0.2s ease; cursor: pointer;"
                                    x-data="{ isDragging: false }" x-on:dragover.prevent="isDragging = true"
                                    x-on:dragleave.prevent="isDragging = false" x-on:drop.prevent="isDragging = false"
                                    :style="isDragging ?
                                        'border-color: var(--pdam-primary); background: var(--pdam-primary-light);' : ''">

                                    @if ($foto)
                                        <div style="margin-bottom: 1rem;">
                                            <img src="{{ $foto->temporaryUrl() }}"
                                                style="max-width: 100%; max-height: 100%; border-radius: var(--radius-md); object-fit: cover;">
                                        </div>
                                        <p
                                            style="color: var(--pdam-success); font-size: 0.875rem; margin-bottom: 0.5rem;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                style="display: inline; vertical-align: middle;">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                            Foto berhasil dipilih
                                        </p>
                                        <span
                                            style="display: inline-block; margin-top: 0.5rem; padding: 0.5rem 1rem; background: var(--pdam-primary); color: white; border-radius: var(--radius-md); font-size: 0.875rem;">
                                            Ganti Foto
                                        </span>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                            viewBox="0 0 24 24" fill="none" stroke="var(--pdam-gray-400)"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                            style="margin-bottom: 1rem;">
                                            <rect x="3" y="3" width="18" height="18" rx="2"
                                                ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                        <p style="color: var(--pdam-gray-600); margin-bottom: 0.5rem;">
                                            Klik di sini untuk memilih foto
                                        </p>
                                        <p style="color: var(--pdam-gray-400); font-size: 0.875rem;">
                                            Format: JPG, PNG, GIF (Maks. 5MB)
                                        </p>
                                        <span
                                            style="display: inline-block; margin-top: 1rem; padding: 0.5rem 1rem; background: var(--pdam-primary); color: white; border-radius: var(--radius-md); font-size: 0.875rem;">
                                            Pilih Foto
                                        </span>
                                    @endif
                                </label>

                                <div wire:loading wire:target="foto" style="margin-top: 0.5rem;">
                                    <span style="font-size: 0.875rem; color: var(--pdam-primary);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            style="display: inline; vertical-align: middle; animation: spin 1s linear infinite;">
                                            <line x1="12" y1="2" x2="12" y2="6">
                                            </line>
                                            <line x1="12" y1="18" x2="12" y2="22">
                                            </line>
                                            <line x1="4.93" y1="4.93" x2="7.76" y2="7.76">
                                            </line>
                                            <line x1="16.24" y1="16.24" x2="19.07" y2="19.07">
                                            </line>
                                            <line x1="2" y1="12" x2="6" y2="12">
                                            </line>
                                            <line x1="18" y1="12" x2="22" y2="12">
                                            </line>
                                            <line x1="4.93" y1="19.07" x2="7.76" y2="16.24">
                                            </line>
                                            <line x1="16.24" y1="7.76" x2="19.07" y2="4.93">
                                            </line>
                                        </svg>
                                        Mengunggah foto...
                                    </span>
                                </div>

                                @error('foto')
                                    <span
                                        style="font-size: 0.875rem; color: var(--pdam-danger); display: block; margin-top: 0.5rem;">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Info Box -->
                            <div
                                style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.5rem; display: flex; gap: 0.75rem; align-items: flex-start;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    style="flex-shrink: 0; margin-top: 2px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                <div style="font-size: 0.875rem; color: #1e40af;">
                                    <p style="font-weight: 600; margin-bottom: 0.25rem;">Petunjuk Pengisian:</p>
                                    <ul style="margin: 0; padding-left: 1rem; list-style: disc;">
                                        <li>Pastikan angka pada meteran terbaca dengan jelas pada foto.</li>
                                        <li>Lapor meter tidak dapat dilakukan pada hari terakhir bulan.</li>
                                        <li>Angka meter yang dilaporkan harus lebih besar dari angka meter bulan lalu.
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <button type="submit" class="pdam-btn pdam-btn-primary pdam-btn-lg" style="width: 100%;"
                                wire:loading.attr="disabled" wire:target="submit, foto">
                                <span wire:loading.remove wire:target="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        style="display: inline; vertical-align: middle; margin-right: 0.5rem;">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                    Kirim Laporan
                                </span>
                                <span wire:loading wire:target="submit">Mengirim...</span>
                            </button>
                        </form>
                    </div>

                    <!-- Help Section -->
                    <div style="margin-top: 2rem; text-align: center;">
                        <p style="color: var(--pdam-gray-500); font-size: 0.9375rem;">
                            Butuh bantuan?
                            <a href="{{ route('kontak') }}"
                                style="color: var(--pdam-primary); font-weight: 500;">Hubungi kami</a>
                            atau kunjungi halaman
                            <a href="{{ route('faq') }}"
                                style="color: var(--pdam-primary); font-weight: 500;">FAQ</a>
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <style>
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
</div>
