<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Jalan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pdamone_billing.jalan';

    public function kelurahan(): HasManyThrough
    {
        return $this->hasManyThrough(Kelurahan::class, JalanKelurahan::class, 'jalan_id', 'id', 'id', 'kelurahan_id');
    }
}
