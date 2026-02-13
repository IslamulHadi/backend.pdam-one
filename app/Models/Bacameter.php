<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bacameter extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pdamone_billing.bacameter';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'no_pelanggan',
        'periode',
        'tgl_baca',
        'tgl_upload',
        'stan_lalu',
        'stan_ini',
        'stan_ini_awal',
        'pakai',
        'foto',
    ];
}
