<?php

declare(strict_types=1);

use App\Models\Pelanggan;
use App\Models\Rekening;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Database\Eloquent\Collection;

new #[Layout('components.layouts.public')] #[Title('Cek Tagihan')] class extends Component {
    #[Validate('required|string|min:5')]
    public string $nomoPelanggan = '';

    public ?Pelanggan $pelanggan = null;
    public ?Collection $tagihan = null;
    public bool $searched = false;
    public bool $notFound = false;

    protected function messages(): array
    {
        return [
            'nomoPelanggan.required' => 'Nomor pelanggan harus diisi.',
            'nomoPelanggan.min' => 'Nomor pelanggan minimal 5 digit.',
        ];
    }

    public function search(): void
    {
        $this->validate();

        $this->searched = true;
        $this->notFound = false;

        $this->pelanggan = Pelanggan::where('no_pelanggan', $this->nomoPelanggan)->first();

        if ($this->pelanggan) {
            $this->tagihan = $this->pelanggan->tunggakan()->get();
        } else {
            $this->notFound = true;
        }
    }

    public function resetSearch(): void
    {
        $this->reset(['nomoPelanggan', 'pelanggan', 'tagihan', 'searched', 'notFound']);
    }
}; ?>

<div>
    <!-- Search Section -->
    <section class="pdam-bill-search">
        <div class="pdam-container">
            <div class="pdam-bill-search-box">
                <h1 class="pdam-section-title">Cek Tagihan Air</h1>
                <p class="pdam-section-subtitle">
                    Masukkan nomor pelanggan Anda untuk melihat detail tagihan bulan ini dan riwayat pemakaian air.
                </p>

                <form wire:submit="search" class="pdam-search-input-wrapper">
                    <svg class="pdam-search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <input type="text" wire:model="nomoPelanggan" class="pdam-form-input"
                        placeholder="Masukkan nomor pelanggan" autocomplete="off">
                    <button type="submit" class="pdam-btn pdam-btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="search">Cek Tagihan</span>
                        <span wire:loading wire:target="search">Mencari...</span>
                    </button>
                </form>
                @error('nomoPelanggan')
                    <p style="color: var(--pdam-danger); font-size: 0.875rem; margin-top: 0.5rem;" class="mt-2 text-left">{{ $message }}</p>
                @enderror

                <p style="font-size: 0.875rem; color: var(--pdam-gray-500); margin-top: 1rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" style="display: inline; vertical-align: middle;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    Nomor pelanggan dapat dilihat pada struk pembayaran bulan lalu.
                </p>


            </div>

            <!-- Bill Result -->
            @if ($searched)
                <div class="pdam-bill-result">
                    @if ($notFound)
                        <div class="pdam-card pdam-text-center" style="padding: 3rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" style="color: var(--pdam-gray-400); margin: 0 auto 1rem;">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                <line x1="8" y1="11" x2="14" y2="11"></line>
                            </svg>
                            <h3
                                style="font-size: 1.25rem; font-weight: 700; color: var(--pdam-dark); margin-bottom: 0.5rem;">
                                Pelanggan Tidak Ditemukan</h3>
                            <p style="color: var(--pdam-gray-600); margin-bottom: 1.5rem;">
                                Nomor pelanggan "{{ $nomoPelanggan }}" tidak terdaftar dalam sistem kami.
                            </p>
                            <button wire:click="resetSearch" class="pdam-btn pdam-btn-secondary">Coba Lagi</button>
                        </div>
                    @elseif($pelanggan && $tagihan->count() > 0)
                        <div class="pdam-bill-card">
                            <!-- Header -->
                            <div class="pdam-bill-header">
                                <div>
                                    <div class="pdam-bill-ticket">ID Pelanggan:</div>
                                    <div class="pdam-bill-ticket-number">
                                        {{ $tagihan[0]->no_pelanggan }}
                                        <button
                                            onclick="navigator.clipboard.writeText('{{ $tagihan[0]->no_pelanggan }}')"
                                            title="Salin">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                style="color: var(--pdam-gray-400);">
                                                <rect x="9" y="9" width="13" height="13" rx="2"
                                                    ry="2"></rect>
                                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <div
                                        style="font-size: 0.875rem; color: var(--pdam-gray-500); text-align: right; margin-bottom: 0.25rem;">
                                        Status Terkini</div>
                                    <span class="pdam-bill-status belum-lunas">
                                        {{ $tagihan->count() > 0 ? $tagihan->count() . ' Tunggakan' : 'Lunas' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Customer Info -->
                            <div class="pdam-bill-customer">
                                <div class="pdam-bill-customer-avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <div>
                                    <div class="pdam-bill-customer-name">{{ $pelanggan->nama ?? '-' }}</div>
                                    <div class="pdam-bill-customer-address">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                            <circle cx="12" cy="10" r="3"></circle>
                                        </svg>
                                        {{ $pelanggan->alamat ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Total Tagihan -->
                            @php
                                $totalTagihan = 0;
                                foreach ($tagihan as $item) {
                                    $totalTagihan +=
                                        ($item->harga_air ?? 0) +
                                        ($item->by_administrasi ?? 0) +
                                        ($item->by_pemeliharaan ?? 0) +
                                        ($item->by_retribusi ?? 0) -
                                        ($item->diskon ?? 0) +
                                        ($item->denda ?? 0) +
                                        ($item->by_materai ?? 0);
                                }
                            @endphp
                            <div
                                style="margin-top: 2rem; padding: 1.5rem; background: linear-gradient(135deg, var(--pdam-primary, #0066cc), var(--pdam-primary-dark, #0052a3)); border-radius: 0.5rem; color: white;">
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                                    <div>
                                        <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.25rem;">Total
                                            Tagihan</div>
                                        <div style="font-size: 2rem; font-weight: 700;">Rp
                                            {{ number_format($totalTagihan, 0, ',', '.') }}</div>
                                        <div style="font-size: 0.75rem; opacity: 0.8; margin-top: 0.25rem;">
                                            {{ $tagihan->count() }} Tagihan</div>
                                    </div>
                                    {{-- <div style="font-size: 3rem; opacity: 0.2;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="12" y1="2" x2="12" y2="22"></line>
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                        </svg>
                                    </div> --}}
                                </div>
                            </div>

                            <!-- Tagihan Table -->
                            <div style="overflow-x: auto; margin-top: 2rem;">
                                <table
                                    style="width: 100%; border-collapse: collapse; background: white; border-radius: 0.5rem; overflow: hidden;">
                                    <thead>
                                        <tr style="background: var(--pdam-primary, #0066cc); color: white;">
                                            <th
                                                style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.875rem;">
                                                Golongan</th>
                                            <th
                                                style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.875rem;">
                                                Periode</th>
                                            <th
                                                style="padding: 1rem; text-align: right; font-weight: 600; font-size: 0.875rem;">
                                                Meter Awal</th>
                                            <th
                                                style="padding: 1rem; text-align: right; font-weight: 600; font-size: 0.875rem;">
                                                Meter Akhir</th>
                                            <th
                                                style="padding: 1rem; text-align: right; font-weight: 600; font-size: 0.875rem;">
                                                Pemakaian (m³)</th>
                                            <th
                                                style="padding: 1rem; text-align: right; font-weight: 600; font-size: 0.875rem;">
                                                Harga Air</th>
                                            <th
                                                style="padding: 1rem; text-align: right; font-weight: 600; font-size: 0.875rem;">
                                                By. Admin</th>
                                            <th
                                                style="padding: 1rem; text-align: right; font-weight: 600; font-size: 0.875rem;">
                                                By. Pemeliharaan</th>
                                            <th
                                                style="padding: 1rem; text-align: right; font-weight: 600; font-size: 0.875rem;">
                                                Denda</th>
                                            <th
                                                style="padding: 1rem; text-align: right; font-weight: 600; font-size: 0.875rem;">
                                                Total Bayar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tagihan as $item)
                                            @php
                                                $total =
                                                    ($item->harga_air ?? 0) +
                                                    ($item->by_administrasi ?? 0) +
                                                    ($item->by_pemeliharaan ?? 0) +
                                                    ($item->denda ?? 0);
                                            @endphp
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td style="padding: 1rem; font-size: 0.875rem;">
                                                    {{ $item->golongan_nama ?? '-' }}
                                                </td>
                                                <td style="padding: 1rem; font-size: 0.875rem;">
                                                    {{ $item->periode ?? '-' }}</td>
                                                <td style="padding: 1rem; text-align: right; font-size: 0.875rem;">
                                                    {{ number_format($item->stan_lalu ?? 0, 0, ',', '.') }}</td>
                                                <td style="padding: 1rem; text-align: right; font-size: 0.875rem;">
                                                    {{ number_format($item->stan_ini ?? 0, 0, ',', '.') }}</td>
                                                <td
                                                    style="padding: 1rem; text-align: right; font-size: 0.875rem; font-weight: 600;">
                                                    {{ number_format($item->pakai ?? 0, 0, ',', '.') }}</td>
                                                <td style="padding: 1rem; text-align: right; font-size: 0.875rem;">Rp
                                                    {{ number_format($item->harga_air ?? 0, 0, ',', '.') }}</td>
                                                <td style="padding: 1rem; text-align: right; font-size: 0.875rem;">Rp
                                                    {{ number_format($item->by_administrasi ?? 0, 0, ',', '.') }}</td>
                                                <td style="padding: 1rem; text-align: right; font-size: 0.875rem;">Rp
                                                    {{ number_format($item->by_pemeliharaan ?? 0, 0, ',', '.') }}</td>
                                                <td
                                                    style="padding: 1rem; text-align: right; font-size: 0.875rem; color: var(--pdam-danger, #ef4444);">
                                                    @if (($item->denda ?? 0) > 0)
                                                        Rp {{ number_format($item->denda, 0, ',', '.') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td
                                                    style="padding: 1rem; text-align: right; font-size: 0.875rem; font-weight: 700; color: var(--pdam-primary, #0066cc);">
                                                    Rp {{ number_format($total, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    @elseif($pelanggan && $tagihan->count() == 0)
                        <div class="pdam-card pdam-text-center" style="padding: 3rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                style="color: var(--pdam-success); margin: 0 auto 1rem;">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <h3
                                style="font-size: 1.25rem; font-weight: 700; color: var(--pdam-dark); margin-bottom: 0.5rem;">
                                Tidak Ada Tagihan</h3>
                            <p style="color: var(--pdam-gray-600); margin-bottom: 1.5rem;">
                                Pelanggan <strong>{{ $pelanggan->nama }}</strong> tidak memiliki tagihan yang tercatat.
                            </p>
                            <button wire:click="resetSearch" class="pdam-btn pdam-btn-secondary">Cari Pelanggan
                                Lain</button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </section>
</div>
