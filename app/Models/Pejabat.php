<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PejabatLevel;
use App\Models\Sigap\Pegawai as SigapPegawai;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Pejabat extends Model implements HasMedia
{
    use HasFactory, HasUuids, InteractsWithMedia;

    protected $table = 'pejabat';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'pegawai_id',
        'nama',
        'jabatan',
        'level',
        'bidang',
        'deskripsi',
        'display_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'level' => PejabatLevel::class,
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('foto')
            ->singleFile();
    }

    /**
     * Relationship to sigap pegawai (cross-database).
     */
    public function pegawai(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SigapPegawai::class, 'pegawai_id');
    }

    /**
     * Get the display name - prefer sigap data if linked.
     */
    protected function displayNama(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if ($this->pegawai_id && $this->relationLoaded('pegawai') && $this->pegawai) {
                    return $this->pegawai->nama;
                }

                return $this->nama;
            },
        );
    }

    /**
     * Get the display jabatan - prefer sigap data if linked.
     */
    protected function displayJabatan(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if ($this->pegawai_id && $this->relationLoaded('pegawai') && $this->pegawai?->jabatan) {
                    return $this->pegawai->jabatan->nama;
                }

                return $this->jabatan;
            },
        );
    }

    /**
     * Get the display bidang - prefer sigap data if linked.
     */
    protected function displayBidang(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if ($this->pegawai_id && $this->relationLoaded('pegawai') && $this->pegawai?->bagian) {
                    return $this->pegawai->bagian->nama;
                }

                return $this->bidang;
            },
        );
    }

    /**
     * Get the display level - prefer sigap data if linked.
     */
    protected function displayLevel(): Attribute
    {
        return Attribute::make(
            get: function (): ?PejabatLevel {
                if ($this->pegawai_id && $this->relationLoaded('pegawai') && $this->pegawai) {
                    return $this->pegawai->pejabat_level;
                }

                return $this->level;
            },
        );
    }

    /**
     * Check if this pejabat is linked to sigap.
     */
    public function isLinkedToSigap(): bool
    {
        return $this->pegawai_id !== null;
    }

    /**
     * @param  Builder<Pejabat>  $query
     * @return Builder<Pejabat>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Pejabat>  $query
     * @return Builder<Pejabat>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order');
    }

    /**
     * @param  Builder<Pejabat>  $query
     * @return Builder<Pejabat>
     */
    public function scopeByLevel(Builder $query, PejabatLevel|string $level): Builder
    {
        $value = $level instanceof PejabatLevel ? $level->value : $level;

        return $query->where('level', $value);
    }

    /**
     * @param  Builder<Pejabat>  $query
     * @return Builder<Pejabat>
     */
    public function scopeDireksi(Builder $query): Builder
    {
        return $query->where('level', PejabatLevel::Direksi->value);
    }

    /**
     * @param  Builder<Pejabat>  $query
     * @return Builder<Pejabat>
     */
    public function scopeKabid(Builder $query): Builder
    {
        return $query->where('level', PejabatLevel::Kabid->value);
    }

    /**
     * @param  Builder<Pejabat>  $query
     * @return Builder<Pejabat>
     */
    public function scopeKasubid(Builder $query): Builder
    {
        return $query->where('level', PejabatLevel::Kasubid->value);
    }

    /**
     * Eager load sigap relationships when the SSO connection is available.
     *
     * @param  Builder<Pejabat>  $query
     * @return Builder<Pejabat>
     */
    public function scopeWithSigap(Builder $query): Builder
    {
        try {
            \Illuminate\Support\Facades\DB::connection('sso')->getPdo();

            return $query->with(['pegawai.jabatan', 'pegawai.bagian']);
        } catch (\Exception) {
            return $query;
        }
    }
}
