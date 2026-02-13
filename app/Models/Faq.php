<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FaqCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Faq extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tanya_jawab';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'question',
        'answer',
        'category',
        'display_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'category' => FaqCategory::class,
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        $clearCaches = function (): void {
            Cache::forget('page.home');
            Cache::forget('faq.counts');
        };

        static::saved($clearCaches);
        static::deleted($clearCaches);
    }

    /**
     * @param  Builder<Faq>  $query
     * @return Builder<Faq>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Faq>  $query
     * @return Builder<Faq>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order');
    }

    /**
     * @param  Builder<Faq>  $query
     * @return Builder<Faq>
     */
    public function scopeByCategory(Builder $query, FaqCategory|string $category): Builder
    {
        $value = $category instanceof FaqCategory ? $category->value : $category;

        return $query->where('category', $value);
    }
}
