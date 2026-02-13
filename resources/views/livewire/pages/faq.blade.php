<?php

declare(strict_types=1);

use App\Enums\FaqCategory;
use App\Models\Faq;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\{Layout, Title, Url};
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] #[Title('FAQ - Pertanyaan Umum')] class extends Component {
    #[Url]
    public ?string $category = null;

    #[Url]
    public ?string $search = '';

    public ?string $openFaqId = null;

    public function mount(): void
    {
        // Get FAQ ID from query string to auto-expand
        $faqId = request()->query('faq');
        if ($faqId) {
            $this->openFaqId = $faqId;
        }
    }

    public function toggleFaq(string $id): void
    {
        $this->openFaqId = $this->openFaqId === $id ? null : $id;
    }

    public function setCategory(?string $category): void
    {
        $this->category = $category;
        $this->openFaqId = null;
    }

    public function with(): array
    {
        $query = Faq::active()->ordered();

        if ($this->category) {
            $query->where('category', $this->category);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('question', 'like', '%' . $this->search . '%')
                    ->orWhere('answer', 'like', '%' . $this->search . '%');
            });
        }

        return [
            'faqs' => $query->get(),
            'categories' => FaqCategory::cases(),
            'faqCounts' => Cache::remember('faq.counts', 3600, fn () => [
                'all' => Faq::active()->count(),
                ...collect(FaqCategory::cases())->mapWithKeys(fn ($cat) => [
                    $cat->value => Faq::active()->where('category', $cat->value)->count(),
                ])->toArray(),
            ]),
        ];
    }
}; ?>

<div>
    <!-- Page Header -->
    <section class="pdam-page-header">
        <div class="pdam-container">
            <h1 class="pdam-page-title text-white">Pertanyaan Umum (FAQ)</h1>
            <p class="pdam-page-subtitle">
                Temukan jawaban atas pertanyaan yang sering diajukan seputar layanan PDAM.
                Tidak menemukan jawaban? Silakan hubungi kami.
            </p>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="pdam-section" style="background: var(--pdam-gray-50);">
        <div class="pdam-container">
            <!-- Search & Filter -->
            <div class="pdam-faq-controls">
                <!-- Search Box -->
                <div class="pdam-faq-search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="pdam-faq-search-icon">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search" class="pdam-faq-search-input"
                        placeholder="Cari pertanyaan...">
                </div>

                <!-- Category Filter -->
                <div class="pdam-faq-categories">
                    <button wire:click="setCategory(null)"
                        class="pdam-faq-category-btn {{ !$category ? 'active' : '' }}">
                        Semua
                        <span class="pdam-faq-category-count">{{ $faqCounts['all'] }}</span>
                    </button>
                    @foreach ($categories as $cat)
                        <button wire:click="setCategory('{{ $cat->value }}')"
                            class="pdam-faq-category-btn {{ $category === $cat->value ? 'active' : '' }}">
                            {{ $cat->getLabel() }}
                            <span class="pdam-faq-category-count">{{ $faqCounts[$cat->value] ?? 0 }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- FAQ List -->
            <div class="pdam-faq-list">
                @forelse($faqs as $faq)
                    <div class="pdam-faq-accordion {{ $openFaqId === $faq->id ? 'open' : '' }}"
                        wire:key="faq-{{ $faq->id }}">
                        <button class="pdam-faq-accordion-header" wire:click="toggleFaq('{{ $faq->id }}')">
                            <div class="pdam-faq-accordion-title">
                                <span class="pdam-faq-accordion-category">{{ $faq->category->getLabel() }}</span>
                                <span class="pdam-faq-accordion-question">{{ $faq->question }}</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="pdam-faq-accordion-icon">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                        <div class="pdam-faq-accordion-body">
                            <div class="pdam-faq-accordion-content">
                                {!! clean($faq->answer) !!}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="pdam-faq-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                        <h3>Tidak ada FAQ ditemukan</h3>
                        <p>Coba ubah kata kunci pencarian atau pilih kategori lain.</p>
                    </div>
                @endforelse
            </div>

            <!-- Still Need Help -->
            <div class="pdam-faq-help-box">
                {{-- <div class="pdam-faq-help-content">
                    <h3>Masih butuh bantuan?</h3>
                    <p>Jika Anda tidak menemukan jawaban yang dicari, tim kami siap membantu Anda.</p> --}}
                {{-- </div> --}}
                {{-- <div class="pdam-faq-help-actions">
                    <a href="{{ route('pengaduan') }}" class="pdam-btn pdam-btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                        Ajukan Pertanyaan
                    </a>
                    <a href="{{ route('kontak') }}" class="pdam-btn pdam-btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                            </path>
                        </svg>
                        Hubungi Kami
                    </a>
                </div> --}}
            </div>
        </div>
    </section>
</div>

@push('scripts')
    <style>
        /* FAQ Controls */
        .pdam-faq-controls {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        /* Search */
        .pdam-faq-search {
            position: relative;
            max-width: 500px;
        }

        .pdam-faq-search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--pdam-gray-400);
        }

        .pdam-faq-search-input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 2px solid var(--pdam-gray-200);
            border-radius: var(--radius-lg);
            font-size: 1rem;
            transition: all 0.2s ease;
            background: var(--pdam-white);
        }

        .pdam-faq-search-input:focus {
            outline: none;
            border-color: var(--pdam-primary);
            box-shadow: 0 0 0 3px rgba(0, 102, 179, 0.1);
        }

        /* Category Filter */
        .pdam-faq-categories {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .pdam-faq-category-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border: 2px solid var(--pdam-gray-200);
            border-radius: var(--radius-full);
            background: var(--pdam-white);
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--pdam-gray-600);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .pdam-faq-category-btn:hover {
            border-color: var(--pdam-primary);
            color: var(--pdam-primary);
        }

        .pdam-faq-category-btn.active {
            background: var(--pdam-primary);
            border-color: var(--pdam-primary);
            color: var(--pdam-white);
        }

        .pdam-faq-category-count {
            padding: 0.125rem 0.5rem;
            background: var(--pdam-gray-100);
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .pdam-faq-category-btn.active .pdam-faq-category-count {
            background: rgba(255, 255, 255, 0.2);
            color: var(--pdam-white);
        }

        /* FAQ List */
        .pdam-faq-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        /* Accordion */
        .pdam-faq-accordion {
            background: var(--pdam-white);
            border-radius: var(--radius-lg);
            border: 1px solid var(--pdam-gray-200);
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .pdam-faq-accordion:hover {
            border-color: var(--pdam-gray-300);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .pdam-faq-accordion.open {
            border-color: var(--pdam-primary);
            box-shadow: 0 4px 12px rgba(0, 102, 179, 0.1);
        }

        .pdam-faq-accordion-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 1.25rem 1.5rem;
            background: none;
            border: none;
            cursor: pointer;
            text-align: left;
        }

        .pdam-faq-accordion-title {
            display: flex;
            flex-direction: column;
            gap: 0.375rem;
            flex: 1;
            padding-right: 1rem;
        }

        .pdam-faq-accordion-category {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--pdam-primary);
        }

        .pdam-faq-accordion-question {
            font-size: 1rem;
            font-weight: 600;
            color: var(--pdam-dark);
            line-height: 1.4;
        }

        .pdam-faq-accordion-icon {
            flex-shrink: 0;
            color: var(--pdam-gray-400);
            transition: transform 0.3s ease;
        }

        .pdam-faq-accordion.open .pdam-faq-accordion-icon {
            transform: rotate(180deg);
            color: var(--pdam-primary);
        }

        .pdam-faq-accordion-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .pdam-faq-accordion.open .pdam-faq-accordion-body {
            max-height: 1000px;
        }

        .pdam-faq-accordion-content {
            padding: 0 1.5rem 1.5rem;
            color: var(--pdam-gray-600);
            line-height: 1.7;
            border-top: 1px solid var(--pdam-gray-100);
            padding-top: 1.25rem;
            margin-top: 0;
        }

        .pdam-faq-accordion-content p {
            margin-bottom: 1rem;
        }

        .pdam-faq-accordion-content p:last-child {
            margin-bottom: 0;
        }

        .pdam-faq-accordion-content ul,
        .pdam-faq-accordion-content ol {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .pdam-faq-accordion-content li {
            margin-bottom: 0.5rem;
        }

        /* Empty State */
        .pdam-faq-empty {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--pdam-white);
            border-radius: var(--radius-lg);
            border: 2px dashed var(--pdam-gray-200);
        }

        .pdam-faq-empty svg {
            color: var(--pdam-gray-300);
            margin-bottom: 1rem;
        }

        .pdam-faq-empty h3 {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--pdam-dark);
            margin-bottom: 0.5rem;
        }

        .pdam-faq-empty p {
            color: var(--pdam-gray-500);
        }

        /* Help Box */
        .pdam-faq-help-box {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            align-items: center;
            text-align: center;
            margin-top: 3rem;
            padding: 2.5rem;
            background: linear-gradient(135deg, var(--pdam-primary), var(--pdam-blue-dark));
            border-radius: var(--radius-xl);
            color: var(--pdam-white);
        }

        .pdam-faq-help-content h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .pdam-faq-help-content p {
            opacity: 0.9;
            max-width: 500px;
        }

        .pdam-faq-help-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
        }

        .pdam-faq-help-actions .pdam-btn-primary {
            background: var(--pdam-white);
            color: var(--pdam-primary);
        }

        .pdam-faq-help-actions .pdam-btn-primary:hover {
            background: var(--pdam-gray-100);
        }

        .pdam-faq-help-actions .pdam-btn-secondary {
            border-color: rgba(255, 255, 255, 0.3);
            color: var(--pdam-white);
        }

        .pdam-faq-help-actions .pdam-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
        }

        /* Responsive */
        @media (min-width: 768px) {
            .pdam-faq-controls {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }

            .pdam-faq-help-box {
                flex-direction: row;
                text-align: left;
            }

            .pdam-faq-help-content {
                flex: 1;
            }
        }
    </style>
@endpush
