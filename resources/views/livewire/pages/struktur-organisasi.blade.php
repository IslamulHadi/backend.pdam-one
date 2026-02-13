<?php

declare(strict_types=1);

use App\Enums\PejabatLevel;
use App\Models\Pejabat;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] #[Title('Struktur Organisasi')] class extends Component {
    public function with(): array
    {
        return [
            'direksi' => Pejabat::direksi()->active()->ordered()->withSigap()->get(),
            'kabid' => Pejabat::kabid()->active()->ordered()->withSigap()->get(),
            'kasubid' => Pejabat::kasubid()->active()->ordered()->withSigap()->get(),
        ];
    }
}; ?>

<div class="min-h-screen bg-slate-50">
    <section class="pdam-page-header">
        <div class="pdam-container">
            <h1 class="pdam-page-title text-white">Struktur Organisasi</h1>
            <p class="pdam-page-subtitle">
                Struktur organisasi perusahaan untuk memenuhi kebutuhan masyarakat
            </p>
        </div>
    </section>

    <!-- Org Chart Section -->
    <section class="pdam-section">
        <div class="pdam-container">
            @if ($direksi->isNotEmpty() || $kabid->isNotEmpty() || $kasubid->isNotEmpty())
                <div class="pdam-org-tree">

                    {{-- ===== LEVEL 1: Direktur Utama (top leader) ===== --}}
                    @if ($direksi->isNotEmpty())
                        @php $topLeader = $direksi->first(); @endphp
                        <div class="pdam-org-level">
                            <div wire:key="dir-{{ $topLeader->id }}" class="pdam-org-node pdam-org-node-primary">
                                <div class="pdam-org-avatar">
                                    @if ($topLeader->getFirstMediaUrl('foto'))
                                        <img src="{{ $topLeader->getFirstMediaUrl('foto') }}"
                                            alt="{{ $topLeader->display_nama }}" class="w-full h-full object-cover">
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                    @endif
                                </div>
                                <h3 class="pdam-org-name">{{ $topLeader->display_nama }}</h3>
                                <p class="pdam-org-position">{{ $topLeader->display_jabatan }}</p>
                            </div>
                        </div>

                        {{-- ===== Connector: Direktur -> Direksi lainnya ===== --}}
                        @if ($direksi->count() > 1)
                            {{-- Vertical line down --}}
                            <div class="pdam-org-connector-down"></div>

                            {{-- Branches to each direksi --}}
                            <div class="pdam-org-connector-branches">
                                @foreach ($direksi->skip(1) as $pejabat)
                                    <div wire:key="branch-dir-{{ $pejabat->id }}" class="pdam-org-branch-item">
                                        <div class="pdam-org-node pdam-org-node-direksi">
                                            <span
                                                class="pdam-org-level-label pdam-org-level-label-direksi">Direksi</span>
                                            <div class="pdam-org-avatar">
                                                @if ($pejabat->getFirstMediaUrl('foto'))
                                                    <img src="{{ $pejabat->getFirstMediaUrl('foto') }}"
                                                        alt="{{ $pejabat->display_nama }}"
                                                        class="w-full h-full object-cover">
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="28"
                                                        height="28" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                        <circle cx="12" cy="7" r="4"></circle>
                                                    </svg>
                                                @endif
                                            </div>
                                            <h3 class="pdam-org-name">{{ $pejabat->display_nama }}</h3>
                                            <p class="pdam-org-position">{{ $pejabat->display_jabatan }}</p>
                                            @if ($pejabat->display_bidang)
                                                <p class="pdam-org-bidang">{{ $pejabat->display_bidang }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif

                    {{-- ===== Connector: Direksi -> Kabid ===== --}}
                    @if ($kabid->isNotEmpty())
                        {{-- Vertical line down --}}
                        <div class="pdam-org-connector-down"></div>

                        {{-- Branches to each kabid --}}
                        <div class="pdam-org-connector-branches">
                            @foreach ($kabid as $pejabat)
                                <div wire:key="branch-kabid-{{ $pejabat->id }}" class="pdam-org-branch-item">
                                    <div class="pdam-org-node pdam-org-node-kabid">
                                        <span class="pdam-org-level-label pdam-org-level-label-kabid">Kabid</span>
                                        @if ($pejabat->getFirstMediaUrl('foto'))
                                            <div class="pdam-org-avatar pdam-org-avatar-sm">
                                                <img src="{{ $pejabat->getFirstMediaUrl('foto') }}"
                                                    alt="{{ $pejabat->display_nama }}"
                                                    class="w-full h-full object-cover">
                                            </div>
                                        @endif
                                        <h4 class="pdam-org-name">{{ $pejabat->display_nama }}</h4>
                                        <p class="pdam-org-position">{{ $pejabat->display_jabatan }}</p>
                                        @if ($pejabat->display_bidang)
                                            <p class="pdam-org-bidang">{{ $pejabat->display_bidang }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- ===== Connector: Kabid -> Kasubid ===== --}}
                    @if ($kasubid->isNotEmpty())
                        {{-- Vertical line down --}}
                        <div class="pdam-org-connector-down"></div>

                        {{-- Branches to each kasubid --}}
                        <div class="pdam-org-connector-branches">
                            @foreach ($kasubid as $pejabat)
                                <div wire:key="branch-kasubid-{{ $pejabat->id }}" class="pdam-org-branch-item">
                                    <div class="pdam-org-node pdam-org-node-kasubid">
                                        <span class="pdam-org-level-label pdam-org-level-label-kasubid">Kasubid</span>
                                        @if ($pejabat->getFirstMediaUrl('foto'))
                                            <div class="pdam-org-avatar pdam-org-avatar-sm">
                                                <img src="{{ $pejabat->getFirstMediaUrl('foto') }}"
                                                    alt="{{ $pejabat->display_nama }}"
                                                    class="w-full h-full object-cover">
                                            </div>
                                        @endif
                                        <h4 class="pdam-org-name">{{ $pejabat->display_nama }}</h4>
                                        <p class="pdam-org-position">{{ $pejabat->display_jabatan }}</p>
                                        @if ($pejabat->display_bidang)
                                            <p class="pdam-org-bidang">{{ $pejabat->display_bidang }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            @else
                {{-- Empty State --}}
                <div class="text-center py-16">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-gray-300" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-600">Data Struktur Organisasi</h3>
                    <p class="mt-2 text-gray-500">Informasi struktur organisasi akan segera ditampilkan.</p>
                </div>
            @endif
        </div>
    </section>
</div>
