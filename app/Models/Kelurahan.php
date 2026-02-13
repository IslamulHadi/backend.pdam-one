<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Kelurahan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pdamone_billing.kelurahan';

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    public function jalan(): HasManyThrough
    {
        return $this->hasManyThrough(Jalan::class, JalanKelurahan::class, 'kelurahan_id', 'id', 'id', 'jalan_id');
    }
}
