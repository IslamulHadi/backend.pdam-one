<?php

declare(strict_types=1);

namespace App\Enums;

enum LoketType: string
{
    case Bank = 'bank';
    case Minimarket = 'minimarket';
    case KantorPdam = 'kantor_pdam';
    case Online = 'online';
    case Ppob = 'ppob';

    public function getLabel(): string
    {
        return match ($this) {
            self::Bank => 'Bank',
            self::Minimarket => 'Minimarket',
            self::KantorPdam => 'Kantor PDAM',
            self::Online => 'Online/E-Wallet',
            self::Ppob => 'PPOB',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Bank => 'building-bank',
            self::Minimarket => 'shopping-bag',
            self::KantorPdam => 'building',
            self::Online => 'device-mobile',
            self::Ppob => 'cash',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Bank => 'blue',
            self::Minimarket => 'green',
            self::KantorPdam => 'cyan',
            self::Online => 'purple',
            self::Ppob => 'orange',
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
