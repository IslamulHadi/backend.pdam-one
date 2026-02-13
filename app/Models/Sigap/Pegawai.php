<?php

declare(strict_types=1);

namespace App\Models\Sigap;

use App\Enums\PejabatLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pegawai extends Model
{
    protected $connection = 'sigap';

    protected $table = 'pegawai';

    protected $keyType = 'string';

    public $incrementing = false;

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    public function bagian(): BelongsTo
    {
        return $this->belongsTo(Bagian::class, 'bagian_id');
    }

    /**
     * Scope to only pejabat-level employees (Direksi, Kabid, Kasubid).
     *
     * @param  Builder<Pegawai>  $query
     * @return Builder<Pegawai>
     */
    public function scopePejabat(Builder $query): Builder
    {
        return $query->whereHas('jabatan', function (Builder $q) {
            $q->whereIn('level_jabatan', [1, 2, 3]);
        });
    }

    /**
     * Scope to only active employees (status_pegawai != 5 means non-aktif in sigap).
     *
     * @param  Builder<Pegawai>  $query
     * @return Builder<Pegawai>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status_pegawai', [1, 2]);
    }

    /**
     * Get the PejabatLevel equivalent from this pegawai's jabatan.
     */
    public function getPejabatLevelAttribute(): ?PejabatLevel
    {
        $levelJabatan = $this->jabatan?->level_jabatan;

        if ($levelJabatan === null) {
            return null;
        }

        return PejabatLevel::fromSigapLevel($levelJabatan);
    }
}
