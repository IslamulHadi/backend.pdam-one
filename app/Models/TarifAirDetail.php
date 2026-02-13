<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarifAirDetail extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pdamone_billing.tarif_air_detail';

    protected $fillable = [
        'tarif_air_id',
        'golongan_id',
        'minimum',
        'rp_minimum',
        'progresif',
    ];

    protected function casts(): array
    {
        return [
            'minimum' => 'integer',
            'rp_minimum' => 'decimal:2',
            'progresif' => 'array',
        ];
    }

    public function tarifAir(): BelongsTo
    {
        return $this->belongsTo(TarifAir::class, 'tarif_air_id');
    }

    public function golongan(): BelongsTo
    {
        return $this->belongsTo(Golongan::class, 'golongan_id');
    }
}
