<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisAduan extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';

    protected $table = 'pdamone_billing.jenis_aduan';
}
