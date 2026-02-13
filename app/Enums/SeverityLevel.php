<?php

declare(strict_types=1);

namespace App\Enums;

enum SeverityLevel: string
{
    case Ringan = 'ringan';
    case Sedang = 'sedang';
    case Berat = 'berat';

    public function getLabel(): string
    {
        return match ($this) {
            self::Ringan => 'Ringan',
            self::Sedang => 'Sedang',
            self::Berat => 'Berat',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Ringan => 'green',
            self::Sedang => 'yellow',
            self::Berat => 'red',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])->toArray();
    }
}
