<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TarifAir extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pdamone_billing.tarif_air';

    protected $fillable = [
        'nomor',
        'keterangan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function detail(): HasMany
    {
        return $this->hasMany(TarifAirDetail::class, 'tarif_air_id');
    }
}
