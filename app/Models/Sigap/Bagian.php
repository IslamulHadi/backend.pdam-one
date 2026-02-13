<?php

declare(strict_types=1);

namespace App\Models\Sigap;

use Illuminate\Database\Eloquent\Model;

class Bagian extends Model
{
    protected $connection = 'sigap';

    protected $table = 'bagian';

    protected $keyType = 'string';

    public $incrementing = false;
}
