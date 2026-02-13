<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Golongan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pdamone_billing.golongan';

    public function tarifAirDetails(): HasMany
    {
        return $this->hasMany(TarifAirDetail::class, 'golongan_id');
    }
}
