<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CompanyInfoKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CompanyInfo extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'info_perusahaan';

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    protected static function booted(): void
    {
        $clearCaches = function (CompanyInfo $info): void {
            Cache::forget("company_info.{$info->key}");

            if ($info->group) {
                Cache::forget("company_info.group.{$info->group}");
            }

            Cache::forget('page.tentang');
            Cache::forget('page.kontak');
        };

        static::saved($clearCaches);
        static::deleted($clearCaches);
    }

    /**
     * @param  Builder<CompanyInfo>  $query
     * @return Builder<CompanyInfo>
     */
    public function scopeByGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group);
    }

    public static function getValue(CompanyInfoKey|string $key, ?string $default = null): ?string
    {
        $keyValue = $key instanceof CompanyInfoKey ? $key->value : $key;

        return Cache::remember("company_info.{$keyValue}", 3600, function () use ($keyValue, $default) {
            $info = self::where('key', $keyValue)->first();

            return $info?->value ?? $default;
        });
    }

    public static function setValue(CompanyInfoKey|string $key, string $value, ?string $group = null): void
    {
        $keyValue = $key instanceof CompanyInfoKey ? $key->value : $key;
        $group = $group ?? ($key instanceof CompanyInfoKey ? $key->getGroup() : null);

        self::updateOrCreate(
            ['key' => $keyValue],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget("company_info.{$keyValue}");
    }

    public static function getByGroup(string $group): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("company_info.group.{$group}", 3600, function () use ($group) {
            return self::byGroup($group)->get();
        });
    }
}
