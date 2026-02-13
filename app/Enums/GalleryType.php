<?php

declare(strict_types=1);

namespace App\Enums;

enum GalleryType: string
{
    case Foto = 'foto';
    case Video = 'video';

    public function getLabel(): string
    {
        return match ($this) {
            self::Foto => 'Foto',
            self::Video => 'Video',
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
