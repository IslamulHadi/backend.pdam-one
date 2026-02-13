<?php

declare(strict_types=1);

namespace App\Enums;

enum LowonganStatus: string
{
    case Open = 'open';
    case Closed = 'closed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Open => 'Dibuka',
            self::Closed => 'Ditutup',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Open => 'green',
            self::Closed => 'red',
        };
    }

    public static function toArray(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])->toArray();
    }
}
