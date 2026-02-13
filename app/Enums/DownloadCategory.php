<?php

declare(strict_types=1);

namespace App\Enums;

enum DownloadCategory: string
{
    case Formulir = 'formulir';
    case Peraturan = 'peraturan';
    case Panduan = 'panduan';
    case Lainnya = 'lainnya';

    public function getLabel(): string
    {
        return match ($this) {
            self::Formulir => 'Formulir',
            self::Peraturan => 'Peraturan',
            self::Panduan => 'Panduan',
            self::Lainnya => 'Lainnya',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Formulir => 'file-text',
            self::Peraturan => 'file-check',
            self::Panduan => 'book-open',
            self::Lainnya => 'file',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Formulir => 'blue',
            self::Peraturan => 'red',
            self::Panduan => 'green',
            self::Lainnya => 'gray',
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
