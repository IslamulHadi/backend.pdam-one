<?php

declare(strict_types=1);

use App\Livewire\Concerns\WithRateLimiting;
use App\Models\Cabang;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\PasangBaru;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] #[Title('Pasang Baru')] class extends Component {
    use WithRateLimiting;
    #[Validate('required|string|max:255')]
    public string $nama = '';

    #[Validate('required|string|max:500')]
    public string $alamat = '';

    #[Validate('required|string|size:16|regex:/^[0-9]+$/')]
    public string $no_ktp = '';

    #[Validate('required|string|max:20|regex:/^[0-9+\-() ]+$/')]
    public string $no_hp = '';

    #[Validate('required|string|max:100')]
    public string $pekerjaan = '';

    #[Validate('required|integer|min:1|max:50')]
    public int $jumlah_penghuni = 1;

    #[Validate('required|string|max:100')]
    public string $fungsi_bangunan = '';

    #[Validate('required|string|exists:billing.cabang,id')]
    public ?string $cabang_id = null;

    #[Validate('required|string|exists:billing.kecamatan,id')]
    public ?string $kecamatan_id = null;

    #[Validate('required|string|exists:billing.kelurahan,id')]
    public ?string $kelurahan_id = null;

    #[Validate('nullable|string|exists:billing.jalan,id')]
    public ?string $jalan_id = null;

    public bool $submitted = false;

    protected function messages(): array
    {
        return [
            'nama.required' => 'Nama harus diisi.',
            'alamat.required' => 'Alamat harus diisi.',
            'no_ktp.required' => 'Nomor KTP harus diisi.',
            'no_ktp.size' => 'Nomor KTP harus 16 digit.',
            'no_ktp.regex' => 'Nomor KTP harus berupa angka.',
            'no_hp.required' => 'Nomor HP harus diisi.',
            'no_hp.regex' => 'Nomor HP tidak valid.',
            'pekerjaan.required' => 'Pekerjaan harus diisi.',
            'jumlah_penghuni.required' => 'Jumlah penghuni harus diisi.',
            'jumlah_penghuni.min' => 'Jumlah penghuni minimal 1 orang.',
            'fungsi_bangunan.required' => 'Fungsi bangunan harus diisi.',
            'kelurahan_id.required' => 'Kelurahan harus dipilih.',
        ];
    }

    public function submit(): void
    {
        $this->rateLimit(3);
        $this->validate();

        try {
            $terakhir = PasangBaru::where('created_at', 'like', date('Y-m') . '%')
                ->orderBy('created_at', 'desc')
                ->orderBy('nomor', 'desc')
                ->first();
            $nomor = '001/' . date('m') . '/SPL/' . config('constants.perusahaan_alias') . '/' . date('Y');

            if ($terakhir) {
                $terakhir = sprintf('%03s', (int) substr($terakhir->nomor, 0, 3) + 1);
                $nomor = $terakhir . '/' . date('m') . '/SPL/' . config('constants.perusahaan_alias') . '/' . date('Y');
            }

            DB::beginTransaction();
            $pasangBaru = PasangBaru::create([
                'nomor' => $nomor,
                'nama' => $this->nama,
                'alamat' => $this->alamat,
                'no_ktp' => $this->no_ktp,
                'no_hp' => $this->no_hp,
                'pekerjaan' => $this->pekerjaan,
                'jumlah_penghuni' => $this->jumlah_penghuni,
                'jenis_bangunan' => $this->fungsi_bangunan,
                'cabang_id' => $this->cabang_id,
                'kecamatan_id' => $this->kecamatan_id,
                'kelurahan_id' => $this->kelurahan_id,
                'jalan_id' => $this->jalan_id,
                'user' => 'web',
            ]);
            DB::commit();

            $this->dispatch('sweetalert', [
                'icon' => 'success',
                'title' => 'Berhasil!',
                'text' => 'Formulir pasang baru berhasil dikirim. Tim kami akan segera menghubungi Anda.',
            ]);

            $this->submitted = true;
        } catch (\Throwable $th) {
            $this->dispatch('sweetalert', [
                'icon' => 'error',
                'title' => 'Error!',
                'text' => 'Terjadi kesalahan saat mengirim formulir pasang baru. Silakan coba lagi.',
                // 'text' => $th->getMessage(),
            ]);
            DB::rollBack();
        }
    }

    public function updatedKelurahanId(): void
    {
        $kelurahan = Kelurahan::find($this->kelurahan_id);
        if ($kelurahan) {
            $this->kecamatan_id = $kelurahan->kecamatan_id;
            $kecamatan = $kelurahan->kecamatan;
            if ($kecamatan && property_exists($kecamatan, 'cabang_id')) {
                $this->cabang_id = $kecamatan->cabang_id;
            } elseif ($kecamatan && $kecamatan->getAttribute('cabang_id')) {
                $this->cabang_id = $kecamatan->getAttribute('cabang_id');
            }
        }
        $this->jalan_id = null;

        // Update jalan select2 options via browser events
        $jalanOptions = $this->jalan->pluck('nama', 'id')->toArray();
        $jalanOptionsJson = json_encode($jalanOptions);
        $disabled = ! $this->kelurahan_id ? 'true' : 'false';

        $this->js("
            window.dispatchEvent(new CustomEvent('update-select2-options', { detail: { name: 'jalan_id', options: {$jalanOptionsJson} } }));
            window.dispatchEvent(new CustomEvent('update-select2-disabled', { detail: { name: 'jalan_id', disabled: {$disabled} } }));
            window.dispatchEvent(new CustomEvent('update-select2-value', { detail: { name: 'jalan_id', value: '' } }));
        ");
    }

    public function getJalanProperty(): Collection
    {
        if ($this->kelurahan_id) {
            $kelurahan = Kelurahan::find($this->kelurahan_id);

            return $kelurahan ? $kelurahan->jalan : collect();
        }

        return collect();
    }

    public function resetForm(): void
    {
        $this->reset();
    }

    public function with(): array
    {
        return [
            'kelurahan' => Kelurahan::all(),
            'jalan' => $this->jalan,
        ];
    }
}; ?>

<div>
    <!-- Hero Section -->
    <section class="pdam-page-hero">
        <div class="pdam-container">
            <h1 class="pdam-page-hero-title text-white">Pasang Baru</h1>
            <p class="pdam-page-hero-subtitle">
                Ajukan permohonan sambungan air baru secara online dengan mudah dan cepat.
            </p>
        </div>
    </section>

    <!-- Form Section -->
    <section style="padding: 3rem 0; background: var(--pdam-gray-100);">
        <div class="pdam-container">
            @if ($submitted)
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
                        Formulir Berhasil Dikirim!</h2>
                    <p style="color: var(--pdam-gray-600); margin-bottom: 1.5rem;">
                        Terima kasih telah mengisi formulir pasang baru. Tim kami akan segera menghubungi Anda untuk
                        proses selanjutnya.
                    </p>
                    <button wire:click="resetForm" class="pdam-btn pdam-btn-primary">Kirim Formulir Baru</button>
                </div>
            @else
                <!-- Form Card -->
                <div class="pdam-card"
                    style="max-width: 900px; margin: 0 auto; padding: 3rem; box-shadow: var(--shadow-lg);">
                    <!-- Title and Description -->
                    <h1
                        style="font-family: var(--font-display); font-size: 2.25rem; font-weight: 800; color: var(--pdam-dark); margin-bottom: 0.75rem;">
                        Formulir Pasang Baru
                    </h1>
                    <p style="font-size: 1rem; color: var(--pdam-gray-600); margin-bottom: 2.5rem;">
                        Silakan lengkapi data di bawah ini untuk mengajukan permohonan sambungan air baru.
                    </p>

                    <form wire:submit="submit">
                        <!-- Data Pribadi Section -->
                        <div style="margin-bottom: 2rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                                <div
                                    style="width: 2.5rem; height: 2.5rem; background: var(--pdam-primary-lighter); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; color: var(--pdam-primary);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <h3
                                    style="font-family: var(--font-display); font-size: 1.25rem; font-weight: 700; color: var(--pdam-dark); margin: 0;">
                                    Data Pribadi
                                </h3>
                            </div>

                            <div class="pdam-form-group">
                                <label class="pdam-form-label">Nama Lengkap <span
                                        style="color: var(--pdam-danger);">*</span></label>
                                <input type="text" wire:model="nama" class="pdam-form-input"
                                    placeholder="Sesuai KTP">
                                @error('nama')
                                    <span
                                        style="font-size: 0.875rem; color: var(--pdam-danger); margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="pdam-form-row">
                                <div class="pdam-form-group">
                                    <label class="pdam-form-label">Nomor KTP (NIK) <span
                                            style="color: var(--pdam-danger);">*</span></label>
                                    <input type="text" wire:model="no_ktp" class="pdam-form-input"
                                        placeholder="16 digit angka" maxlength="16">
                                    <span
                                        style="font-size: 0.8125rem; color: var(--pdam-gray-500); margin-top: 0.25rem; display: block;">Wajib
                                        16 digit sesuai KTP.</span>
                                    @error('no_ktp')
                                        <span
                                            style="font-size: 0.875rem; color: var(--pdam-danger); margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="pdam-form-group">
                                    <label class="pdam-form-label">Nomor HP / WhatsApp <span
                                            style="color: var(--pdam-danger);">*</span></label>
                                    <input type="tel" wire:model="no_hp" class="pdam-form-input"
                                        placeholder="Contoh: 081234567890">
                                    @error('no_hp')
                                        <span
                                            style="font-size: 0.875rem; color: var(--pdam-danger); margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- <div class="pdam-form-row"> --}}
                            <div class="pdam-form-group">
                                <label class="pdam-form-label">Pekerjaan <span
                                        style="color: var(--pdam-danger);">*</span></label>
                                <input type="text" wire:model="pekerjaan" class="pdam-form-input"
                                    placeholder="Pekerjaan saat ini">
                                @error('pekerjaan')
                                    <span
                                        style="font-size: 0.875rem; color: var(--pdam-danger); margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                @enderror
                            </div>
                            {{-- </div> --}}
                        </div>

                        <!-- Data Bangunan Section -->
                        <div style="margin-bottom: 2rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                                <div
                                    style="width: 2.5rem; height: 2.5rem; background: var(--pdam-primary-lighter); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; color: var(--pdam-primary);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                    </svg>
                                </div>
                                <h3
                                    style="font-family: var(--font-display); font-size: 1.25rem; font-weight: 700; color: var(--pdam-dark); margin: 0;">
                                    Data Bangunan
                                </h3>
                            </div>

                            <div class="pdam-form-row">
                                <div class="pdam-form-group">
                                    <label class="pdam-form-label">Fungsi Bangunan <span
                                            style="color: var(--pdam-danger);">*</span></label>
                                    <input type="text" wire:model="fungsi_bangunan" class="pdam-form-input"
                                        placeholder="Contoh: Rumah, Kantor, Toko, Hotel, dll.">
                                    @error('fungsi_bangunan')
                                        <span
                                            style="font-size: 0.875rem; color: var(--pdam-danger); margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="pdam-form-group">
                                    <label class="pdam-form-label">Jumlah Penghuni <span
                                            style="color: var(--pdam-danger);">*</span></label>
                                    <input type="number" wire:model="jumlah_penghuni" class="pdam-form-input"
                                        placeholder="Jumlah orang" min="1" max="50">
                                    @error('jumlah_penghuni')
                                        <span
                                            style="font-size: 0.875rem; color: var(--pdam-danger); margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="pdam-form-group">
                                <label class="pdam-form-label">Alamat Lengkap Pemasangan <span
                                        style="color: var(--pdam-danger);">*</span></label>
                                <textarea wire:model="alamat" class="pdam-form-input pdam-form-textarea" placeholder="Nama jalan, nomor rumah, RT/RW"
                                    rows="3"></textarea>
                                @error('alamat')
                                    <span
                                        style="font-size: 0.875rem; color: var(--pdam-danger); margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Lokasi Section -->
                        <div style="margin-bottom: 2rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                                <div
                                    style="width: 2.5rem; height: 2.5rem; background: var(--pdam-primary-lighter); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; color: var(--pdam-primary);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                </div>
                                <h3
                                    style="font-family: var(--font-display); font-size: 1.25rem; font-weight: 700; color: var(--pdam-dark); margin: 0;">
                                    Lokasi Pemasangan
                                </h3>
                            </div>


                            <div class="pdam-form-row">
                                <div class="pdam-form-group">
                                    <label class="pdam-form-label">Kelurahan <span
                                            style="color: var(--pdam-danger);">*</span></label>
                                    <x-select2 name="kelurahan_id" wireModel="kelurahan_id" :options="$kelurahan->pluck('nama', 'id')->toArray()"
                                        placeholder="Pilih Kelurahan" :value="$kelurahan_id" />
                                    @error('kelurahan_id')
                                        <span
                                            style="font-size: 0.875rem; color: var(--pdam-danger); margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="pdam-form-group">
                                    <label class="pdam-form-label">Nama Jalan <span
                                            style="color: var(--pdam-danger);">*</span></label>
                                    <x-select2 name="jalan_id" wireModel="jalan_id" :options="$jalan->pluck('nama', 'id')->toArray()"
                                        placeholder="Pilih Jalan" :disabled="!$kelurahan_id" :value="$jalan_id" />
                                    @error('jalan_id')
                                        <span
                                            style="font-size: 0.875rem; color: var(--pdam-danger); margin-top: 0.25rem; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Information Box -->
                        <div
                            style="background: var(--pdam-primary-lighter); border: 1px solid var(--pdam-primary-light); border-radius: var(--radius-lg); padding: 1.25rem; margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-start;">
                            <div
                                style="flex-shrink: 0; width: 1.5rem; height: 1.5rem; color: var(--pdam-primary); margin-top: 0.125rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                            </div>
                            <p
                                style="font-size: 0.9375rem; color: var(--pdam-primary-dark); line-height: 1.6; margin: 0;">
                                Dengan mengirimkan formulir ini, saya menyatakan bahwa data yang saya isikan adalah
                                benar dan saya bersedia mematuhi ketentuan berlangganan PDAM yang berlaku.
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div style="text-align: center;">
                            <button type="submit" class="pdam-btn pdam-btn-primary pdam-btn-lg"
                                style="min-width: 220px; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="submit">Kirim Permohonan</span>
                                <span wire:loading wire:target="submit">Mengirim...</span>
                                <svg wire:loading.remove wire:target="submit" xmlns="http://www.w3.org/2000/svg"
                                    width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" style="flex-shrink: 0;">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </section>
</div>
