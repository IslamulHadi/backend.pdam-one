<?php

declare(strict_types=1);

use App\Enums\DownloadCategory;
use App\Models\Download;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] #[Title('Download Center')] class extends Component {
    #[Url]
    public string $category = '';

    #[Url]
    public string $search = '';

    public function downloadFile(string $id)
    {
        $download = Download::findOrFail($id);
        $download->incrementDownloadCount();

        $media = $download->getFirstMedia('files');

        return response()->streamDownload(function () use ($media) {
            echo file_get_contents($media->getPath());
        }, $media->file_name);
    }

    public function with(): array
    {
        $query = Download::query()->active()->ordered()->when($this->category, fn($q) => $q->byCategory($this->category))->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%')->orWhere('description', 'like', '%' . $this->search . '%'));

        return [
            'downloads' => $query->get(),
            'categories' => DownloadCategory::toArray(),
        ];
    }
}; ?>

<div>
    <!-- Hero Section -->
    <section class="pdam-page-hero">
        <div class="pdam-container">
            <h1 class="pdam-page-hero-title text-white">Download Center</h1>
            <p class="pdam-page-hero-subtitle">
                Unduh formulir, peraturan, dan dokumen penting lainnya.
            </p>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="pdam-section" style="padding-top: 2rem; padding-bottom: 1rem;">
        <div class="pdam-container">
            <div class="pdam-filter-bar">
                <div class="pdam-filter-group">
                    <label class="pdam-filter-label">Kategori:</label>
                    <x-select2 name="category" wireModel="category" :options="$categories" placeholder="Semua Kategori"
                        :value="$category" />
                </div>

                <div class="pdam-search-box">
                    <input type="text" wire:model.live.debounce.300ms="search" class="pdam-input"
                        placeholder="Cari file...">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="pdam-search-icon">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- Download List -->
    <section class="pdam-section" style="padding-top: 1rem;">
        <div class="pdam-container">
            <div wire:loading.class="opacity-50" class="transition-opacity duration-300">
                @if ($downloads->count() > 0)
                    <div class="pdam-download-list">
                        @foreach ($downloads as $download)
                            <div class="pdam-download-card">
                                <div class="pdam-download-icon">
                                    @php
                                        $ext = $download->file_extension ?? 'FILE';
                                        $iconColors = [
                                            'PDF' => 'pdam-icon-red',
                                            'DOC' => 'pdam-icon-blue',
                                            'DOCX' => 'pdam-icon-blue',
                                            'XLS' => 'pdam-icon-green',
                                            'XLSX' => 'pdam-icon-green',
                                            'PPT' => 'pdam-icon-orange',
                                            'PPTX' => 'pdam-icon-orange',
                                            'ZIP' => 'pdam-icon-purple',
                                            'RAR' => 'pdam-icon-purple',
                                        ];
                                    @endphp
                                    <div class="pdam-file-icon {{ $iconColors[$ext] ?? 'pdam-icon-gray' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z">
                                            </path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                        <span class="pdam-file-ext">{{ $ext }}</span>
                                    </div>
                                </div>
                                <div class="pdam-download-content">
                                    @php
                                        $categoryColors = [
                                            'formulir' => 'pdam-badge-blue',
                                            'peraturan' => 'pdam-badge-red',
                                            'panduan' => 'pdam-badge-green',
                                            'lainnya' => 'pdam-badge-gray',
                                        ];
                                    @endphp
                                    <span
                                        class="pdam-download-badge {{ $categoryColors[$download->category->value] ?? 'pdam-badge-gray' }}">
                                        {{ $download->category->getLabel() }}
                                    </span>
                                    <h3 class="pdam-download-title">{{ $download->title }}</h3>
                                    @if ($download->description)
                                        <p class="pdam-download-desc">{{ Str::limit($download->description, 100) }}</p>
                                    @endif
                                    <div class="pdam-download-meta">
                                        <span class="pdam-download-size">{{ $download->file_size ?? '-' }}</span>
                                        <span class="pdam-download-count">{{ $download->download_count }} unduhan</span>
                                    </div>
                                </div>
                                <div class="pdam-download-action">
                                    @if ($download->getFirstMedia('files'))
                                        <button wire:click="downloadFile('{{ $download->id }}')"
                                            class="pdam-btn-download">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="7 10 12 15 17 10"></polyline>
                                                <line x1="12" y1="15" x2="12" y2="3">
                                                </line>
                                            </svg>
                                            Download
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="pdam-empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"
                            stroke-linejoin="round" class="pdam-empty-icon">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        <h3 class="pdam-empty-title">Tidak Ada File</h3>
                        <p class="pdam-empty-text">
                            @if ($search || $category)
                                Tidak ditemukan file yang sesuai dengan filter Anda.
                            @else
                                Belum ada file yang tersedia untuk diunduh.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
