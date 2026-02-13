<?php

declare(strict_types=1);

namespace App\Models\Sigap;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $connection = 'sigap';

    protected $table = 'jabatan';

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'level_jabatan' => 'integer',
        ];
    }
}
