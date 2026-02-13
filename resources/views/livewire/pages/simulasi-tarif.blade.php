<?php

declare(strict_types=1);

use App\Models\Golongan;
use App\Models\TarifAir;
use App\Models\TarifAirDetail;
use App\Services\TariffCalculationService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] #[Title('Simulasi Tarif Air')] class extends Component {
    #[Validate('required|exists:billing.golongan,id', message: 'Pilih golongan pelanggan')]
    public ?string $golonganId = null;

    #[Validate('required|integer|min:0', message: 'Masukkan pemakaian air (tidak boleh minus)')]
    public ?int $usage = null;

    public ?TarifAirDetail $selectedTarifDetail = null;

    public ?array $calculationResult = null;

    public function calculate(): void
    {
        $this->validate();

        if (!$this->golonganId || $this->usage === null || $this->usage < 0) {
            return;
        }

        // Get active tariff
        $activeTarif = TarifAir::where('is_active', true)
            ->with([
                'detail' => function ($query) {
                    $query->where('golongan_id', $this->golonganId);
                },
            ])
            ->latest()
            ->first();

        if (!$activeTarif || $activeTarif->detail->isEmpty()) {
            session()->flash('error', 'Tarif untuk golongan ini belum tersedia.');
            $this->reset(['calculationResult', 'selectedTarifDetail']);

            return;
        }

        // dd($activeTarif);

        $this->selectedTarifDetail = $activeTarif->detail->first();
        $this->selectedTarifDetail->load('golongan');

        // Calculate tariff
        $service = new TariffCalculationService();
        $this->calculationResult = $service->calculate($this->usage, $this->selectedTarifDetail);
    }

    public function resetCalculation(): void
    {
        $this->reset(['usage', 'calculationResult', 'selectedTarifDetail']);
        $this->resetValidation();
    }

    public function with(): array
    {
        $golongans = Golongan::query()
            ->whereHas('tarifAirDetails', function ($q) {
                $q->whereHas('tarifAir', function ($tarifQuery) {
                    $tarifQuery->where('is_active', true);
                });
            })
            ->orderBy('nama')
            ->get();

        return [
            'golongans' => $golongans,
        ];
    }
}; ?>

<div>
    <!-- Page Header -->
    <section class="pdam-page-header">
        <div class="pdam-container">
            <div style="margin-bottom: 0.5rem;">
                <a href="{{ route('tarif') }}"
                    style="color: var(--pdam-primary); font-size: 0.875rem; text-decoration: none;">
                    • Tarif Air Minum
                </a>
            </div>
            <h1 class="pdam-page-title text-white">Simulasi Tarif Air</h1>
            <p class="pdam-page-subtitle">
                Hitung perkiraan tagihan bulanan Anda dengan mudah. Pilih golongan pelanggan dan masukkan jumlah
                pemakaian air.
            </p>
        </div>
    </section>

    <!-- Simulation Section -->
    <section class="pdam-section">
        <div class="pdam-container">
            <div class="pdam-simulation-grid">
                <!-- Left Panel: Input Form -->
                <div class="pdam-card" style="padding: 2rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                        <div
                            style="width: 2.5rem; height: 2.5rem; background: var(--pdam-primary-light); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" style="color: var(--pdam-primary);">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                <line x1="8" y1="21" x2="16" y2="21"></line>
                                <line x1="12" y1="17" x2="12" y2="21"></line>
                            </svg>
                        </div>
                        <div>
                            <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--pdam-gray-900); margin: 0;">
                                Data Pemakaian</h2>
                            <p style="font-size: 0.875rem; color: var(--pdam-gray-600); margin: 0.25rem 0 0 0;">Isi data
                                di bawah ini</p>
                        </div>
                    </div>

                    <form wire:submit="calculate">
                        <!-- Golongan Selection -->
                        <div style="margin-bottom: 1.5rem;">
                            <label
                                style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--pdam-gray-700); margin-bottom: 0.5rem;">
                                Golongan Pelanggan
                            </label>
                            <x-select2 name="golongan" wireModel="golonganId" :options="$golongans->pluck('nama', 'id')->toArray()"
                                placeholder="Pilih Golongan" :value="$golonganId" />
                            {{-- <select wire:model="golonganId" class="pdam-form-input" style="width: 100%;" required>
                                <option value="">Pilih Golongan</option>
                                @foreach ($golongans as $golongan)
                                    <option value="{{ $golongan->id }}">{{ $golongan->nama ?? $golongan->id }}</option>
                                @endforeach
                            </select> --}}
                            @error('golonganId')
                                <p style="color: var(--pdam-danger); font-size: 0.75rem; margin-top: 0.25rem;">
                                    {{ $message }}</p>
                            @enderror
                            <p style="font-size: 0.75rem; color: var(--pdam-gray-500); margin-top: 0.5rem;">
                                *Golongan menentukan tarif dasar per meter kubik.
                            </p>
                        </div>

                        <!-- Usage Input -->
                        <div style="margin-bottom: 2rem;">
                            <label
                                style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--pdam-gray-700); margin-bottom: 0.5rem;">
                                Pemakaian Air (m³)
                            </label>
                            <div style="position: relative;">
                                <input type="number" wire:model="usage" class="pdam-form-input"
                                    placeholder="Masukkan pemakaian air" min="0" step="0.1"
                                    style="width: 100%; padding-right: 3rem;" required>
                                <span
                                    style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: var(--pdam-gray-500); font-size: 0.875rem;">m³</span>
                            </div>
                            @error('usage')
                                <p style="color: var(--pdam-danger); font-size: 0.75rem; margin-top: 0.25rem;">
                                    {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Calculate Button -->
                        <button type="submit" class="pdam-btn pdam-btn-primary"
                            style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem;"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="calculate">Hitung Simulasi</span>
                            <span wire:loading wire:target="calculate">Menghitung...</span>
                            <svg wire:loading.remove wire:target="calculate" xmlns="http://www.w3.org/2000/svg"
                                width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Right Panel: Result -->
                <div class="pdam-card" style="padding: 2rem;">
                    @if ($calculationResult && $selectedTarifDetail)
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                            <div
                                style="width: 2.5rem; height: 2.5rem; background: var(--pdam-success); opacity: 0.1; border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" style="color: var(--pdam-success);">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <div>
                                <h2
                                    style="font-size: 1.25rem; font-weight: 600; color: var(--pdam-gray-900); margin: 0;">
                                    Hasil Estimasi</h2>
                            </div>
                        </div>

                        <!-- Golongan Badge -->
                        <div style="margin-bottom: 1.5rem;">
                            <span
                                style="display: inline-block; padding: 0.375rem 0.75rem; background: var(--pdam-primary-light); color: var(--pdam-primary); border-radius: var(--radius-full); font-size: 0.875rem; font-weight: 500;">
                                Golongan {{ $selectedTarifDetail->golongan->nama ?? 'N/A' }}
                            </span>
                        </div>

                        <!-- Usage Breakdown -->
                        <div style="margin-bottom: 2rem;">
                            <h3
                                style="font-size: 0.875rem; font-weight: 600; color: var(--pdam-gray-700); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
                                Rincian Pemakaian Air ({{ number_format($usage, 0, ',', '.') }} M³)
                            </h3>
                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                @foreach ($calculationResult['pemakaian'] as $breakdown)
                                    <div
                                        style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--pdam-gray-200);">
                                        <div>
                                            <span style="font-size: 0.875rem; color: var(--pdam-gray-700);">
                                                Blok {{ $breakdown['block'] }} ({{ $breakdown['range'] }} m³) @
                                                {{ \App\Services\TariffCalculationService::formatRupiah($breakdown['rate']) }}
                                                @if ($breakdown['usage'] > 0)
                                                    x {{ $breakdown['usage'] }} m³
                                                @endif
                                            </span>
                                        </div>
                                        <span
                                            style="font-size: 0.875rem; font-weight: 600; color: var(--pdam-gray-900);">
                                            {{ \App\Services\TariffCalculationService::formatRupiah($breakdown['subtotal']) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--pdam-gray-200);">
                                <div>
                                    <span style="font-size: 0.875rem; color: var(--pdam-gray-700);">
                                        Subtotal Pemakaian
                                    </span>
                                </div>
                                <span style="font-size: 0.875rem; font-weight: 600; color: var(--pdam-gray-900);">
                                    {{ \App\Services\TariffCalculationService::formatRupiah($calculationResult['pemakaian_subtotal']) }}
                                </span>
                            </div>
                        </div>

                        <!-- Additional Costs -->
                        <div style="margin-bottom: 2rem;">
                            <h3
                                style="font-size: 0.875rem; font-weight: 600; color: var(--pdam-gray-700); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
                                Biaya Tambahan
                            </h3>
                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                <div
                                    style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--pdam-gray-200);">
                                    <span style="font-size: 0.875rem; color: var(--pdam-gray-700);">Biaya
                                        Administrasi</span>
                                    <span style="font-size: 0.875rem; font-weight: 600; color: var(--pdam-gray-900);">
                                        {{ \App\Services\TariffCalculationService::formatRupiah($calculationResult['by_administrasi']) }}
                                    </span>
                                </div>
                                <div
                                    style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--pdam-gray-200);">
                                    <span style="font-size: 0.875rem; color: var(--pdam-gray-700);">Biaya
                                        Pemeliharaan</span>
                                    <span style="font-size: 0.875rem; font-weight: 600; color: var(--pdam-gray-900);">
                                        {{ \App\Services\TariffCalculationService::formatRupiah($calculationResult['by_pemeliharaan']) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Total -->
                        <div
                            style="margin-bottom: 2rem; padding: 1.5rem; background: var(--pdam-gray-100); border-radius: var(--radius-lg);">
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <h3
                                    style="font-size: 0.875rem; font-weight: 600; color: var(--pdam-gray-700); text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">
                                    Total Estimasi Tagihan
                                </h3>
                            </div>
                            <div
                                style="font-size: 2rem; font-weight: 700; color: var(--pdam-primary); margin-top: 0.5rem;">
                                {{ \App\Services\TariffCalculationService::formatRupiah($calculationResult['total']) }}
                            </div>
                            <p
                                style="font-size: 0.75rem; color: var(--pdam-gray-500); margin-top: 0.5rem; margin-bottom: 0;">
                                *Belum termasuk denda (jika ada)
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div style="display: flex; gap: 1rem;">
                            <button type="button" onclick="window.print()" class="pdam-btn pdam-btn-secondary"
                                style="flex: 1;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.5rem;">
                                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                    <path
                                        d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2">
                                    </path>
                                    <rect x="6" y="14" width="12" height="8"></rect>
                                </svg>
                                Cetak
                            </button>
                            <button type="button" onclick="shareCalculation()" class="pdam-btn pdam-btn-secondary"
                                style="flex: 1;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.5rem;">
                                    <circle cx="18" cy="5" r="3"></circle>
                                    <circle cx="6" cy="12" r="3"></circle>
                                    <circle cx="18" cy="19" r="3"></circle>
                                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                                </svg>
                                Bagikan
                            </button>
                        </div>
                    @else
                        <div style="text-align: center; padding: 3rem 1rem; color: var(--pdam-gray-500);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                style="margin-bottom: 1rem; opacity: 0.5;">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2">
                                </rect>
                                <line x1="8" y1="21" x2="16" y2="21"></line>
                                <line x1="12" y1="17" x2="12" y2="21"></line>
                            </svg>
                            <p style="font-size: 0.875rem; margin: 0;">Pilih golongan dan masukkan pemakaian air, lalu
                                klik "Hitung Simulasi" untuk melihat hasil perhitungan.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Information Section -->
            <div class="pdam-info-grid"
                style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-top: 3rem;">
                <!-- Disclaimer -->
                <div
                    style="padding: 1.5rem; background: var(--pdam-white); border-radius: var(--radius-lg); border: 1px solid var(--pdam-gray-200);">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" style="color: var(--pdam-info);">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <h4 style="font-size: 0.875rem; font-weight: 600; color: var(--pdam-gray-900); margin: 0;">
                            Disclaimer</h4>
                    </div>
                    <p style="font-size: 0.875rem; color: var(--pdam-gray-600); line-height: 1.6; margin: 0;">
                        Perhitungan ini hanya estimasi berdasarkan tarif yang berlaku saat ini. Tagihan riil mungkin
                        berbeda karena pembulatan atau denda keterlambatan.
                    </p>
                </div>
                {{-- 
                <!-- Legal Basis -->
                <div style="padding: 1.5rem; background: var(--pdam-white); border-radius: var(--radius-lg); border: 1px solid var(--pdam-gray-200);">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--pdam-primary);">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <h4 style="font-size: 0.875rem; font-weight: 600; color: var(--pdam-gray-900); margin: 0;">Dasar Hukum</h4>
                    </div>
                    <p style="font-size: 0.875rem; color: var(--pdam-gray-600); line-height: 1.6; margin: 0;">
                        Tarif air minum mengacu pada Peraturan Walikota No. 25 Tahun 2023 tentang Penyesuaian Tarif Air Minum PDAM Smart.
                    </p>
                </div>

                <!-- Help -->
                <div style="padding: 1.5rem; background: var(--pdam-white); border-radius: var(--radius-lg); border: 1px solid var(--pdam-gray-200);">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--pdam-accent);">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                        <h4 style="font-size: 0.875rem; font-weight: 600; color: var(--pdam-gray-900); margin: 0;">Butuh Bantuan?</h4>
                    </div>
                    <p style="font-size: 0.875rem; color: var(--pdam-gray-600); line-height: 1.6; margin: 0;">
                        Jika Anda memiliki pertanyaan mengenai tagihan atau tarif, silakan hubungi Call Center kami di 021-555-0123.
                    </p>
                </div> --}}
            </div>
        </div>
    </section>

    @script
        <script>
            function shareCalculation() {
                if (navigator.share) {
                    const text = 'Simulasi Tarif Air PDAM Smart - ' + window.location.href;
                    navigator.share({
                        title: 'Simulasi Tarif Air',
                        text: text,
                        url: window.location.href,
                    }).catch(() => {
                        // Fallback to clipboard
                        copyToClipboard(window.location.href);
                    });
                } else {
                    // Fallback to clipboard
                    copyToClipboard(window.location.href);
                }
            }

            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(() => {
                    alert('Link simulasi telah disalin ke clipboard!');
                }).catch(() => {
                    // Fallback for older browsers
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                    alert('Link simulasi telah disalin ke clipboard!');
                });
            }
        </script>
    @endscript
</div>
