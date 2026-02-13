<?php

declare(strict_types=1);

use App\Models\TarifAir;
use App\Models\TarifAirDetail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] #[Title('Informasi Tarif Air')] class extends Component {
    public ?string $selectedGolonganId = null;

    public ?string $expandedTarifId = null;

    public ?string $expandedDetailId = null;

    public string $search = '';

    public function setGolonganFilter(?string $golonganId): void
    {
        $this->selectedGolonganId = $golonganId;
    }

    public function toggleExpandTarif(string $id): void
    {
        $this->expandedTarifId = $this->expandedTarifId === $id ? null : $id;
    }

    public function toggleExpandDetail(string $id): void
    {
        $this->expandedDetailId = $this->expandedDetailId === $id ? null : $id;
    }

    public function with(): array
    {
        $selectedGolonganId = $this->selectedGolonganId;
        $searchTerm = $this->search;

        $query = TarifAir::query()
            ->where('is_active', true)
            ->with(['detail.golongan']);

        // Apply golongan filter at query level if selected
        if ($selectedGolonganId) {
            $query->whereHas('detail', function ($q) use ($selectedGolonganId) {
                $q->where('golongan_id', $selectedGolonganId);
            });
        }

        $tarifs = $query->orderBy('created_at', 'desc')->get();

        // Filter detail by golongan if selected
        if ($selectedGolonganId) {
            $tarifs->each(function ($tarif) use ($selectedGolonganId) {
                $filteredDetails = $tarif->detail->filter(function ($detail) use ($selectedGolonganId) {
                    return $detail->golongan_id === $selectedGolonganId;
                });
                $tarif->setRelation('detail', $filteredDetails);
            });
        }

        // Apply search filter
        if ($searchTerm) {
            $tarifs = $tarifs->filter(function ($tarif) use ($searchTerm) {
                $matchesTarif = stripos($tarif->nomor, $searchTerm) !== false || stripos($tarif->keterangan, $searchTerm) !== false;

                // Also check if any detail matches the search
                $matchesDetail =
                    $tarif->detail
                        ->filter(function ($detail) use ($searchTerm) {
                            $golonganName = $detail->golongan->nama ?? '';

                            return stripos($golonganName, $searchTerm) !== false;
                        })
                        ->count() > 0;

                return $matchesTarif || $matchesDetail;
            });
        }

        // Get unique golongan list for tabs
        $golongans = TarifAirDetail::query()
            ->whereHas('tarifAir', function ($q) {
                $q->where('is_active', true);
            })
            ->with('golongan')
            ->get()
            ->pluck('golongan')
            ->filter()
            ->unique('id')
            ->sortBy('nama')
            ->values();

        return [
            'tarifs' => $tarifs,
            'golongans' => $golongans,
        ];
    }
}; ?>

<div>
    <!-- Page Header -->
    <section class="pdam-page-header">
        <div class="pdam-container">
            <h1 class="pdam-page-title text-white">Informasi Tarif Air</h1>
            <p class="pdam-page-subtitle">
                Daftar lengkap tarif pemakaian air berdasarkan golongan pelanggan dan regulasi yang berlaku.
            </p>
        </div>
    </section>

    <!-- Tariff Section -->
    <section class="pdam-tariff-section">
        <div class="pdam-container">
            <!-- Filters and Search -->
            <div style="margin-bottom: 2rem;">
                <!-- Golongan Tabs -->
                <div class="pdam-tariff-filters" style="margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
                    <button wire:click="setGolonganFilter(null)"
                        class="pdam-tariff-filter {{ $selectedGolonganId === null ? 'active' : '' }}">
                        Semua
                    </button>
                    @foreach ($golongans as $golongan)
                        @if ($golongan)
                            <button wire:click="setGolonganFilter('{{ $golongan->id }}')"
                                class="pdam-tariff-filter {{ $selectedGolonganId === $golongan->id ? 'active' : '' }}">
                                {{ $golongan->nama ?? $golongan->id }}
                            </button>
                        @endif
                    @endforeach
                </div>

                <!-- Search and Download -->
                {{-- <div
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div style="position: relative; flex: 1; max-width: 400px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--pdam-gray-400);">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="search" class="pdam-form-input"
                            placeholder="Cari nomor, keterangan, atau golongan..."
                            style="padding-left: 2.5rem; width: 100%; height: 2.75rem;">
                    </div> --}}
                    {{-- <button class="pdam-btn pdam-btn-secondary pdam-btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        Unduh PDF
                    </button> --}}
                {{-- </div> --}}
            </div>

            <!-- Tariff List -->
            <div class="pdam-card" style="padding: 0; overflow: hidden;">
                @forelse($tarifs as $tarif)
                    <div wire:key="tarif-{{ $tarif->id }}" class="pdam-tariff-group"
                        style="border-bottom: 1px solid var(--pdam-gray-200);">
                        <!-- Tarif Header -->
                        <div class="pdam-tariff-header"
                            style="padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;"
                            wire:click="toggleExpandTarif('{{ $tarif->id }}')">
                            <div>
                                <h3
                                    style="font-size: 1.125rem; font-weight: 600; color: var(--pdam-gray-900); margin-bottom: 0.25rem;">
                                    {{ $tarif->nomor }}
                                </h3>
                                <p style="font-size: 0.875rem; color: var(--pdam-gray-600); margin: 0;">
                                    {{ $tarif->keterangan }}
                                </p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                style="transform: {{ $expandedTarifId === $tarif->id ? 'rotate(180deg)' : 'rotate(0deg)' }}; transition: transform 0.2s; color: var(--pdam-gray-500);">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>

                        <!-- Tarif Details Table -->
                        @if ($expandedTarifId === $tarif->id && $tarif->detail->count() > 0)
                            <div style="padding: 0 1.5rem 1.5rem 1.5rem;">
                                <table class="pdam-tariff-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 100px;">Golongan</th>
                                            <th style="text-align: center;">Minimum (M³)</th>
                                            <th style="text-align: center;">Rp Minimum</th>
                                            <th>Progresif Tarif</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tarif->detail as $detail)
                                            <tr wire:key="detail-{{ $detail->id }}">
                                                <td>
                                                    <span
                                                        class="pdam-tariff-code">{{ $detail->golongan->nama ?? '-' }}</span>
                                                </td>
                                                <td style="text-align: center;">
                                                    <span class="pdam-tariff-price">{{ $detail->minimum ?? 0 }}
                                                        M³</span>
                                                </td>
                                                <td style="text-align: center;">
                                                    <span class="pdam-tariff-price">
                                                        Rp {{ number_format($detail->rp_minimum ?? 0, 0, ',', '.') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if (is_array($detail->progresif) && count($detail->progresif) > 0)
                                                        <div
                                                            style="display: flex; flex-direction: column; gap: 0.25rem;">
                                                            @foreach ($detail->progresif as $tier)
                                                                @if (isset($tier['min']) && isset($tier['max']) && isset($tier['harga']))
                                                                    <div
                                                                        style="font-size: 0.875rem; color: var(--pdam-gray-700);">
                                                                        {{ $tier['min'] }} - {{ $tier['max'] }} M³:
                                                                        <strong>Rp
                                                                            {{ number_format((float) $tier['harga'], 0, ',', '.') }}/M³</strong>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span style="color: var(--pdam-gray-400);">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button wire:click="toggleExpandDetail('{{ $detail->id }}')"
                                                        style="background: none; border: none; cursor: pointer; padding: 0.5rem; color: var(--pdam-gray-500);">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18"
                                                            height="18" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            style="transform: {{ $expandedDetailId === $detail->id ? 'rotate(180deg)' : 'rotate(0deg)' }}; transition: transform 0.2s;">
                                                            <polyline points="6 9 12 15 18 9"></polyline>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                            @if ($expandedDetailId === $detail->id)
                                                <tr wire:key="expand-detail-{{ $detail->id }}">
                                                    <td colspan="5"
                                                        style="padding: 0; background: var(--pdam-gray-50);">
                                                        <div class="pdam-tariff-expand">
                                                            <div class="pdam-tariff-expand-grid">
                                                                <div>
                                                                    <div class="pdam-tariff-expand-title">Detail
                                                                        Progresif</div>
                                                                    @if (is_array($detail->progresif) && count($detail->progresif) > 0)
                                                                        <table
                                                                            style="width: 100%; margin-top: 0.75rem; font-size: 0.875rem;">
                                                                            <thead>
                                                                                <tr
                                                                                    style="border-bottom: 1px solid var(--pdam-gray-200);">
                                                                                    <th
                                                                                        style="text-align: left; padding: 0.5rem 0; color: var(--pdam-gray-700);">
                                                                                        Konsumsi (M³)</th>
                                                                                    <th
                                                                                        style="text-align: right; padding: 0.5rem 0; color: var(--pdam-gray-700);">
                                                                                        Tarif per M³</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach ($detail->progresif as $tier)
                                                                                    @if (isset($tier['min']) && isset($tier['max']) && isset($tier['harga']))
                                                                                        <tr
                                                                                            style="border-bottom: 1px solid var(--pdam-gray-100);">
                                                                                            <td
                                                                                                style="padding: 0.5rem 0; color: var(--pdam-gray-600);">
                                                                                                {{ $tier['min'] }} -
                                                                                                {{ $tier['max'] }}
                                                                                            </td>
                                                                                            <td
                                                                                                style="text-align: right; padding: 0.5rem 0; font-weight: 600; color: var(--pdam-gray-900);">
                                                                                                Rp
                                                                                                {{ number_format((float) $tier['harga'], 0, ',', '.') }}
                                                                                            </td>
                                                                                        </tr>
                                                                                    @endif
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    @else
                                                                        <p
                                                                            style="color: var(--pdam-gray-400); margin-top: 0.5rem;">
                                                                            Tidak ada data progresif</p>
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    <div class="pdam-tariff-expand-title">Biaya
                                                                        Lain-Lain</div>
                                                                    <div
                                                                        style="font-size: 0.875rem; color: var(--pdam-gray-700); margin-top: 0.75rem;">
                                                                        <div
                                                                            style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                                                            <span>Biaya Denda</span>
                                                                            <span>Rp {{ number_format($detail->golongan->by_denda ?? 0, 0, ',', '.') }}</span>
                                                                        </div>
                                                                        <div
                                                                            style="display: flex; justify-content: space-between;">
                                                                            <span>Biaya Administrasi</span>
                                                                            <span>Rp {{ number_format($detail->golongan->by_administrasi ?? 0, 0, ',', '.') }}</span>
                                                                        </div>
                                                                        <div
                                                                            style="display: flex; justify-content: space-between;">
                                                                            <span>Biaya Pemeliharaan</span>
                                                                            <span>Rp {{ number_format($detail->golongan->by_pemeliharaan ?? 0, 0, ',', '.') }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    style="display: flex; align-items: center; justify-content: center;">
                                                                    <a href="{{ route('simulasi-tarif') }}"
                                                                        class="pdam-btn pdam-btn-primary pdam-btn-sm">Simulasi
                                                                        Hitung</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @empty
                    <div style="text-align: center; padding: 3rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" style="color: var(--pdam-gray-300); margin-bottom: 1rem;">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="3" y1="9" x2="21" y2="9"></line>
                            <line x1="9" y1="21" x2="9" y2="9"></line>
                        </svg>
                        <p style="color: var(--pdam-gray-500);">Tidak ada data tarif air yang tersedia.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination Info -->
            @if ($tarifs->count() > 0)
                <div
                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; font-size: 0.875rem; color: var(--pdam-gray-500);">
                    <span>Menampilkan {{ $tarifs->count() }} regulasi tarif air</span>
                </div>
            @endif

            <!-- Important Note -->
            <div class="pdam-callout" style="margin-top: 2rem;">
                <h4 class="pdam-callout-title">Catatan Penting</h4>
                <p class="pdam-callout-text">
                    Tarif di atas belum termasuk biaya administrasi dan biaya pemeliharaan. Keterlambatan pembayaran
                    tagihan akan dikenakan denda sesuai dengan peraturan yang berlaku. Untuk informasi perubahan
                    golongan, silakan kunjungi kantor layanan terdekat.
                </p>
            </div>
        </div>
    </section>
</div>
