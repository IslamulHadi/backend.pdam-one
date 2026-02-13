<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumsToArray;

enum FaqCategory: string
{
    use EnumsToArray;

    case Pengaduan = 'pengaduan';
    case Umum = 'umum';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pengaduan => 'Pengaduan',
            self::Umum => 'Umum',
        };
    }
}
