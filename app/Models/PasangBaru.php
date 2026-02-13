<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasangBaru extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pdamone_billing.pasang_baru';
}
