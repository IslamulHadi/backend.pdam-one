<?php

declare(strict_types=1);

use App\Models\Aduan;
use App\Models\RealisasiAduan;
use Livewire\Volt\Component;
use Livewire\Attributes\{Url, Layout, Title};

new #[Layout('components.layouts.public')] #[Title('Lacak Status Pengaduan')] class extends Component {
    #[Url]
    public string $ticket = '';

    public ?Aduan $aduan = null;
    public ?RealisasiAduan $realisasi = null;
    public bool $searched = false;
    public bool $notFound = false;

    public function mount(): void
    {
        if ($this->ticket) {
            $this->search();
        }
    }

    public function search(): void
    {
        if (empty($this->ticket)) {
            return;
        }

        $this->searched = true;
        $this->notFound = false;

        $this->aduan = Aduan::query()
            ->with(['jenisAduan', 'kelurahan', 'jalan', 'kecamatan', 'cabang', 'realisasi'])
            ->where('nomor', $this->ticket)
            ->first();

        if (!$this->aduan) {
            $this->notFound = true;
            $this->realisasi = null;
        } else {
            // Ambil realisasi terbaru (jika ada)
            $this->realisasi = $this->aduan->realisasi()->latest()->first();
        }
    }

    public function resetSearch(): void
    {
        $this->reset(['ticket', 'aduan', 'realisasi', 'searched', 'notFound']);
    }
}; ?>

<div>
    <!-- Page Header -->
    <section class="pdam-page-header">
        <div class="pdam-container">
            <h1 class="pdam-page-title text-white">Lacak Status Pengaduan</h1>
            <p class="pdam-page-subtitle">
                Masukkan nomor tiket pengaduan Anda untuk melihat progres penanganan masalah secara real-time.
            </p>
            <div style="margin-top: 1.5rem;">
                <a href="{{ route('pengaduan') }}" class="pdam-btn pdam-btn-secondary"
                    style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.3);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5"></path>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Buat Pengaduan Baru
                </a>
            </div>
        </div>
    </section>

    <!-- Tracking Section -->
    <section class="pdam-bill-search">
        <div class="pdam-container">
            <div class="pdam-bill-search-box">
                <form wire:submit="search" class="pdam-search-input-wrapper">
                    <svg class="pdam-search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" wire:model="ticket" class="pdam-form-input"
                        placeholder="Masukkan nomor tiket (contoh: P-2023-8892)" autocomplete="off">
                    <button type="submit" class="pdam-btn pdam-btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="search">Lacak</span>
                        <span wire:loading wire:target="search">...</span>
                    </button>
                </form>
            </div>

            <!-- Tracking Result -->
            @if ($searched)
                <div class="pdam-tracking-result">
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
                                Tiket Tidak Ditemukan</h3>
                            <p style="color: var(--pdam-gray-600); margin-bottom: 1.5rem;">
                                Nomor tiket "{{ $ticket }}" tidak terdaftar dalam sistem kami.
                            </p>
                            <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
                                <button wire:click="resetSearch" class="pdam-btn pdam-btn-secondary">Coba Lagi</button>
                                <a href="{{ route('pengaduan') }}" class="pdam-btn pdam-btn-primary"
                                    style="display: inline-flex; align-items: center; gap: 0.5rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Buat Pengaduan Baru
                                </a>
                            </div>
                        </div>
                    @elseif($aduan)
                        <div class="pdam-tracking-card">
                            <!-- Header -->
                            <div class="pdam-tracking-header">
                                <div>
                                    <div class="pdam-bill-ticket">Nomor Tiket</div>
                                    <div class="pdam-bill-ticket-number">
                                        {{ $aduan->nomor }}
                                        <button onclick="navigator.clipboard.writeText('{{ $aduan->nomor }}')"
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
                                    <span class="pdam-bill-status"
                                        style="background: {{ $aduan->status_color }}; color: white;">
                                        {{ $aduan->status_label }}
                                    </span>
                                </div>
                            </div>

                            <!-- Info Grid -->
                            <div class="pdam-tracking-info">
                                <div class="pdam-bill-info-item">
                                    <div class="pdam-bill-info-label">Jenis Aduan</div>
                                    <div class="pdam-bill-info-value">{{ $aduan->jenisAduan->nama ?? '-' }}</div>
                                </div>
                                <div class="pdam-bill-info-item">
                                    <div class="pdam-bill-info-label">Nama Pelapor</div>
                                    <div class="pdam-bill-info-value">{{ $aduan->nama ?? '-' }}</div>
                                </div>
                                <div class="pdam-bill-info-item">
                                    <div class="pdam-bill-info-label">No. Telepon</div>
                                    <div class="pdam-bill-info-value">{{ $aduan->no_telp ?? '-' }}</div>
                                </div>
                                <div class="pdam-bill-info-item">
                                    <div class="pdam-bill-info-label">Tanggal Lapor</div>
                                    <div class="pdam-bill-info-value">
                                        {{ $aduan->created_at->translatedFormat('d M Y, H:i') }} WIB
                                    </div>
                                </div>
                                @if ($aduan->kelurahan || $aduan->jalan)
                                    <div class="pdam-bill-info-item">
                                        <div class="pdam-bill-info-label">Alamat</div>
                                        <div class="pdam-bill-info-value">
                                            @if ($aduan->jalan)
                                                {{ $aduan->jalan->nama }},
                                            @endif
                                            @if ($aduan->kelurahan)
                                                {{ $aduan->kelurahan->nama }}
                                            @endif
                                            @if (!$aduan->jalan && !$aduan->kelurahan)
                                                -
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                @if ($aduan->kecamatan)
                                    <div class="pdam-bill-info-item">
                                        <div class="pdam-bill-info-label">Kecamatan</div>
                                        <div class="pdam-bill-info-value">{{ $aduan->kecamatan->nama ?? '-' }}</div>
                                    </div>
                                @endif

                                @if ($aduan->waktu_mulai)
                                    <div class="pdam-bill-info-item">
                                        <div class="pdam-bill-info-label">Waktu Mulai</div>
                                        <div class="pdam-bill-info-value">
                                            {{ \Carbon\Carbon::parse($aduan->waktu_mulai)->translatedFormat('d M Y, H:i') }}
                                            WIB
                                        </div>
                                    </div>
                                @endif
                                @if ($aduan->waktu_selesai)
                                    <div class="pdam-bill-info-item">
                                        <div class="pdam-bill-info-label">Waktu Selesai</div>
                                        <div class="pdam-bill-info-value">
                                            {{ \Carbon\Carbon::parse($aduan->waktu_selesai)->translatedFormat('d M Y, H:i') }}
                                            WIB
                                        </div>
                                    </div>
                                @endif
                                @if ($aduan->waktu_pengerjaan)
                                    <div class="pdam-bill-info-item">
                                        <div class="pdam-bill-info-label">Waktu Pengerjaan</div>
                                        <div class="pdam-bill-info-value">
                                            {{ \Carbon\Carbon::parse($aduan->waktu_pengerjaan)->translatedFormat('d M Y, H:i') }}
                                            WIB
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if ($aduan->keterangan)
                                <div
                                    style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--pdam-gray-200);">
                                    <div class="pdam-bill-info-label ml-2" style="margin-bottom: 0.5rem;">Keterangan
                                        Aduan</div>
                                    <div class="pdam-bill-info-value" style="white-space: pre-wrap;">
                                        {{ $aduan->keterangan }}</div>
                                </div>
                            @endif

                            {{-- Informasi Realisasi/Penanganan --}}
                            @if ($realisasi)
                                <div
                                    style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--pdam-gray-200);">
                                    <h4
                                        style="font-size: 1rem; font-weight: 600; color: var(--pdam-dark); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            style="color: var(--pdam-primary);">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                        Informasi Penanganan
                                    </h4>
                                    <div class="pdam-tracking-info">
                                        @if ($realisasi->petugas)
                                            <div class="pdam-bill-info-item">
                                                <div class="pdam-bill-info-label">Petugas</div>
                                                <div class="pdam-bill-info-value">{{ $realisasi->petugas }}</div>
                                            </div>
                                        @endif
                                        @if ($realisasi->tgl_proses)
                                            <div class="pdam-bill-info-item">
                                                <div class="pdam-bill-info-label">Tanggal Penugasan</div>
                                                <div class="pdam-bill-info-value">
                                                    {{ \Carbon\Carbon::parse($realisasi->tgl_proses)->translatedFormat('d M Y, H:i') }}
                                                    WIB
                                                </div>
                                            </div>
                                        @endif
                                        @if ($realisasi->tgl_selesai)
                                            <div class="pdam-bill-info-item">
                                                <div class="pdam-bill-info-label">Tanggal Selesai</div>
                                                <div class="pdam-bill-info-value">
                                                    {{ \Carbon\Carbon::parse($realisasi->tgl_selesai)->translatedFormat('d M Y, H:i') }}
                                                    WIB
                                                </div>
                                            </div>
                                        @endif
                                        @if ($realisasi->keterangan)
                                            <div class="pdam-bill-info-item" style="grid-column: span 2;">
                                                <div class="pdam-bill-info-label">Keterangan Penanganan</div>
                                                <div class="pdam-bill-info-value" style="white-space: pre-wrap;">
                                                    {{ $realisasi->keterangan }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Dokumentasi Foto Pengerjaan --}}
                            @if ($aduan->foto_sebelum || $aduan->foto_sedang || $aduan->foto_sesudah)
                                <div
                                    style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--pdam-gray-200);">
                                    <h4
                                        style="font-size: 1rem; font-weight: 600; color: var(--pdam-dark); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            style="color: var(--pdam-primary);">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2">
                                            </rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                        Dokumentasi Pengerjaan
                                    </h4>
                                    <div
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                                        @if ($aduan->foto_sebelum)
                                            <div class="pdam-photo-card">
                                                <div class="pdam-photo-label">
                                                    <span
                                                        style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: var(--pdam-info); color: white; border-radius: 4px; font-size: 0.75rem; font-weight: 500;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                            height="12" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <polyline points="12 6 12 12 16 14"></polyline>
                                                        </svg>
                                                        Sebelum
                                                    </span>
                                                </div>
                                                <a href="{{ $aduan->foto_sebelum_url }}" target="_blank"
                                                    style="display: block;">
                                                    <img src="{{ $aduan->foto_sebelum_url }}" alt="Foto Sebelum"
                                                        style="width: 100%; height: 180px; object-fit: cover; border-radius: 8px; border: 1px solid var(--pdam-gray-200);"
                                                        loading="lazy">
                                                </a>
                                                <div
                                                    style="font-size: 0.75rem; color: var(--pdam-gray-500); margin-top: 0.5rem; text-align: center;">
                                                    Kondisi sebelum perbaikan
                                                </div>
                                            </div>
                                        @endif

                                        @if ($aduan->foto_sedang)
                                            <div class="pdam-photo-card">
                                                <div class="pdam-photo-label">
                                                    <span
                                                        style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: var(--pdam-warning); color: white; border-radius: 4px; font-size: 0.75rem; font-weight: 500;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                            height="12" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path
                                                                d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z">
                                                            </path>
                                                        </svg>
                                                        Proses
                                                    </span>
                                                </div>
                                                <a href="{{ $aduan->foto_sedang_url }}" target="_blank"
                                                    style="display: block;">
                                                    <img src="{{ $aduan->foto_sedang_url }}" alt="Foto Sedang Dikerjakan"
                                                        style="width: 100%; height: 180px; object-fit: cover; border-radius: 8px; border: 1px solid var(--pdam-gray-200);"
                                                        loading="lazy">
                                                </a>
                                                <div
                                                    style="font-size: 0.75rem; color: var(--pdam-gray-500); margin-top: 0.5rem; text-align: center;">
                                                    Proses pengerjaan
                                                </div>
                                            </div>
                                        @endif

                                        @if ($aduan->foto_sesudah)
                                            <div class="pdam-photo-card">
                                                <div class="pdam-photo-label">
                                                    <span
                                                        style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: var(--pdam-success); color: white; border-radius: 4px; font-size: 0.75rem; font-weight: 500;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                            height="12" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <polyline points="20 6 9 17 4 12"></polyline>
                                                        </svg>
                                                        Sesudah
                                                    </span>
                                                </div>
                                                <a href="{{ $aduan->foto_sesudah_url }}" target="_blank"
                                                    style="display: block;">
                                                    <img src="{{ $aduan->foto_sesudah_url }}" alt="Foto Sesudah"
                                                        style="width: 100%; height: 180px; object-fit: cover; border-radius: 8px; border: 1px solid var(--pdam-gray-200);"
                                                        loading="lazy">
                                                </a>
                                                <div
                                                    style="font-size: 0.75rem; color: var(--pdam-gray-500); margin-top: 0.5rem; text-align: center;">
                                                    Kondisi setelah perbaikan
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Timeline -->
                            <div class="pdam-tracking-timeline" style="margin-top: 2rem;">
                                <h4
                                    style="font-size: 1rem; font-weight: 600; color: var(--pdam-dark); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        style="color: var(--pdam-primary);">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    Timeline Pengaduan
                                </h4>
                                <div class="pdam-timeline">
                                    @php
                                        $statusOrder = [
                                            \App\Models\Aduan::STATUS_OPEN => 'Open',
                                            \App\Models\Aduan::STATUS_PENUGASAN => 'Penugasan',
                                            \App\Models\Aduan::STATUS_MULAI_DIKERJAKAN => 'Mulai Dikerjakan',
                                            \App\Models\Aduan::STATUS_PROSES => 'Proses',
                                            \App\Models\Aduan::STATUS_SELESAI => 'Selesai',
                                        ];
                                        $currentStatus = (int) $aduan->status;
                                        $statusKeys = array_keys($statusOrder);
                                        $currentStatusIndex = array_search($currentStatus, $statusKeys);

                                        // Descriptions dengan informasi lebih detail
                                        $statusDescriptions = [
                                            \App\Models\Aduan::STATUS_OPEN =>
                                                'Laporan telah diterima dan sedang dalam antrian untuk ditindaklanjuti.',
                                            \App\Models\Aduan::STATUS_PENUGASAN =>
                                                'Petugas telah ditugaskan untuk menangani laporan ini.',
                                            \App\Models\Aduan::STATUS_MULAI_DIKERJAKAN =>
                                                'Petugas telah mulai mengerjakan perbaikan di lokasi.',
                                            \App\Models\Aduan::STATUS_PROSES =>
                                                'Perbaikan sedang dalam proses pengerjaan.',
                                            \App\Models\Aduan::STATUS_SELESAI =>
                                                'Perbaikan telah selesai dilaksanakan.',
                                        ];
                                    @endphp

                                    @foreach ($statusOrder as $statusValue => $statusLabel)
                                        @php
                                            $index = array_search($statusValue, $statusKeys);
                                            $isCompleted = $index !== false && $index < $currentStatusIndex;
                                            $isCurrent = $index === $currentStatusIndex;
                                            $isPending = $index !== false && $index > $currentStatusIndex;

                                            // Tentukan tanggal berdasarkan status dan data realisasi
                                            $statusDate = null;
                                            $statusPetugas = null;

                                            if ($statusValue === \App\Models\Aduan::STATUS_OPEN) {
                                                $statusDate = $aduan->created_at;
                                            } elseif (
                                                $statusValue === \App\Models\Aduan::STATUS_PENUGASAN &&
                                                $realisasi?->tgl_proses
                                            ) {
                                                $statusDate = $realisasi->tgl_proses;
                                                $statusPetugas = $realisasi->petugas;
                                            } elseif (
                                                $statusValue === \App\Models\Aduan::STATUS_MULAI_DIKERJAKAN &&
                                                $aduan->waktu_mulai
                                            ) {
                                                $statusDate = $aduan->waktu_mulai;
                                            } elseif (
                                                $statusValue === \App\Models\Aduan::STATUS_PROSES &&
                                                $aduan->waktu_pengerjaan
                                            ) {
                                                $statusDate = $aduan->waktu_pengerjaan;
                                            } elseif ($statusValue === \App\Models\Aduan::STATUS_SELESAI) {
                                                $statusDate =
                                                    $realisasi?->tgl_selesai ?? $aduan->waktu_selesai ?? null;
                                            }
                                        @endphp
                                        <div class="pdam-timeline-item">
                                            <div
                                                class="pdam-timeline-dot {{ $isCompleted ? 'completed' : ($isCurrent ? 'current' : 'pending') }}">
                                                @if ($isCompleted)
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                        height="12" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                @elseif($isCurrent)
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="10"
                                                        height="10" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path
                                                            d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z">
                                                        </path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div
                                                class="pdam-timeline-title {{ $isCurrent ? 'current' : ($isPending ? 'pending' : '') }}">
                                                {{ $statusLabel }}
                                            </div>
                                            @if ($isCompleted || $isCurrent)
                                                <div class="pdam-timeline-description">
                                                    {{ $statusDescriptions[$statusValue] ?? '' }}
                                                </div>
                                                @if ($statusPetugas)
                                                    <div class="pdam-timeline-date"
                                                        style="display: flex; align-items: center; gap: 0.25rem;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                            height="12" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2">
                                                            </path>
                                                            <circle cx="12" cy="7" r="4"></circle>
                                                        </svg>
                                                        Petugas: {{ $statusPetugas }}
                                                    </div>
                                                @endif
                                                @if ($statusDate)
                                                    <div class="pdam-timeline-date">
                                                        {{ \Carbon\Carbon::parse($statusDate)->translatedFormat('d M Y, H:i') }}
                                                        WIB
                                                    </div>
                                                @endif
                                            @elseif($isPending)
                                                <div class="pdam-timeline-description"
                                                    style="color: var(--pdam-gray-400);">
                                                    Menunggu proses sebelumnya selesai.
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </section>
</div>
