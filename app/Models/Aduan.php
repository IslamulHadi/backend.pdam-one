<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Aduan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pdamone_billing.aduan';

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
            'waktu_pengerjaan' => 'datetime',
        ];
    }

    public const STATUS_OPEN = 0;

    public const STATUS_PENUGASAN = 2;

    public const STATUS_MULAI_DIKERJAKAN = 3;

    public const STATUS_PROSES = 4;

    public const STATUS_SELESAI = 5;

    public function jenisAduan(): BelongsTo
    {
        return $this->belongsTo(JenisAduan::class, 'jenis_aduan_id');
    }

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'kelurahan_id');
    }

    public function jalan(): BelongsTo
    {
        return $this->belongsTo(Jalan::class, 'jalan_id');
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function realisasi(): HasMany
    {
        return $this->hasMany(RealisasiAduan::class, 'aduan_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ((int) $this->status) {
            self::STATUS_OPEN => 'Open',
            self::STATUS_PENUGASAN => 'Penugasan',
            self::STATUS_MULAI_DIKERJAKAN => 'Mulai Dikerjakan',
            self::STATUS_PROSES => 'Proses',
            self::STATUS_SELESAI => 'Selesai',
            default => 'Unknown',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ((int) $this->status) {
            self::STATUS_OPEN => 'var(--pdam-info)',
            self::STATUS_PENUGASAN => 'var(--pdam-warning)',
            self::STATUS_MULAI_DIKERJAKAN => 'var(--pdam-primary)',
            self::STATUS_PROSES => 'var(--pdam-primary)',
            self::STATUS_SELESAI => 'var(--pdam-success)',
            default => 'var(--pdam-gray-500)',
        };
    }

    public function getFotoSebelumUrlAttribute(): ?string
    {
        if (! $this->foto_sebelum) {
            return null;
        }

        return Storage::disk('billing')->url($this->foto_sebelum);
    }

    public function getFotoSedangUrlAttribute(): ?string
    {
        if (! $this->foto_sedang) {
            return null;
        }

        return Storage::disk('billing')->url($this->foto_sedang);
    }

    public function getFotoSesudahUrlAttribute(): ?string
    {
        if (! $this->foto_sesudah) {
            return null;
        }

        return Storage::disk('billing')->url($this->foto_sesudah);
    }
}
