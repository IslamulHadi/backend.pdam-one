<?php

declare(strict_types=1);

namespace App\Enums;

enum EmploymentType: string
{
    case FullTime = 'full_time';
    case Contract = 'contract';
    case Internship = 'internship';

    public function getLabel(): string
    {
        return match ($this) {
            self::FullTime => 'Full Time',
            self::Contract => 'Kontrak',
            self::Internship => 'Magang',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::FullTime => 'blue',
            self::Contract => 'orange',
            self::Internship => 'green',
        };
    }

    public static function toArray(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])->toArray();
    }
}
