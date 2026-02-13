<?php

declare(strict_types=1);

namespace App\Enums;

enum PriorityLevel: string
{
    case Tinggi = 'tinggi';
    case Sedang = 'sedang';
    case Rendah = 'rendah';

    public function getLabel(): string
    {
        return match ($this) {
            self::Tinggi => 'Tinggi',
            self::Sedang => 'Sedang',
            self::Rendah => 'Rendah',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Tinggi => 'red',
            self::Sedang => 'yellow',
            self::Rendah => 'blue',
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
