<?php

declare(strict_types=1);

namespace App\Enums;

enum PejabatLevel: string
{
    case Direksi = 'direksi';
    case Kabid = 'kabid';
    case Kasubid = 'kasubid';

    public function getLabel(): string
    {
        return match ($this) {
            self::Direksi => 'Direksi',
            self::Kabid => 'Kepala Bidang',
            self::Kasubid => 'Kepala Sub Bidang',
        };
    }

    /**
     * Map from sigap's level_jabatan integer to PejabatLevel.
     */
    public static function fromSigapLevel(int $level): ?self
    {
        return match ($level) {
            1 => self::Direksi,
            2 => self::Kabid,
            3 => self::Kasubid,
            default => null,
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
