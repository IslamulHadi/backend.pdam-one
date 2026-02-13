<?php

namespace App\Traits;

trait EnumsToArray
{
    public static function toArray(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->name] = $case->value;
        }
        return $array;
    }

    public static function getValues(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[] = $case->value;
        }
        return $array;
    }

    public static function options(): array
    {
        $cases   = static::cases();
        $options = [];
        foreach ($cases as $case) {
            $label = $case->name;
            if (\Str::contains($label, '_')) {
                $label = \Str::replace('_', ' ', $label);
            }
            $options[] = [
                'value' => $case->value,
                'label' => \Str::title($label)
            ];
        }
        return $options;
    }
}
