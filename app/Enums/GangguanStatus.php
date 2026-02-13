<?php

declare(strict_types=1);

namespace App\Enums;

enum GangguanStatus: string
{
    case Active = 'active';
    case Resolved = 'resolved';

    public function getLabel(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Resolved => 'Selesai',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Active => 'red',
            self::Resolved => 'green',
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
