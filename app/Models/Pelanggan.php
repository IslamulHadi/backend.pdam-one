<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Scope;

class Pelanggan extends Model
{
    use HasFactory, HasUuids;
    protected $keyType = "string";
    protected $table = "pdamone_billing.pelanggan";

    public function rekening(): HasMany
    {
        return $this->hasMany(Rekening::class, 'pelanggan_id');
    }
    public function tunggakan(): HasMany
    {
        return $this->rekening()
            ->whereNull('pembayaran_id')
            ->whereNull('angsuran_air_id')
            ->orderBy('periode');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status',  1);
    }
}
