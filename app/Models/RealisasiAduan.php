<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealisasiAduan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pdamone_billing.realisasi_aduan';

    protected $fillable = [
        'aduan_id',
        'tgl_proses',
        'tgl_selesai',
        'petugas',
        'keterangan',
    ];

    public function aduan(): BelongsTo
    {
        return $this->belongsTo(Aduan::class, 'aduan_id');
    }
}
