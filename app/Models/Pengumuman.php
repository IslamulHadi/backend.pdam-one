<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PriorityLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Pengumuman extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pengumuman';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'priority',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'priority' => PriorityLevel::class,
            'is_active' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Pengumuman $pengumuman) {
            if (empty($pengumuman->slug)) {
                $pengumuman->slug = Str::slug($pengumuman->title);
            }
            if (empty($pengumuman->excerpt) && ! empty($pengumuman->content)) {
                $pengumuman->excerpt = Str::limit(strip_tags($pengumuman->content), 200);
            }
        });

        $clearCaches = function (): void {
            Cache::forget('page.home');

            for ($i = 1; $i <= 20; $i++) {
                Cache::forget("page.pengumuman.{$i}");
            }
        };

        static::saved($clearCaches);
        static::deleted($clearCaches);
    }

    /**
     * @param  Builder<Pengumuman>  $query
     * @return Builder<Pengumuman>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Pengumuman>  $query
     * @return Builder<Pengumuman>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * @param  Builder<Pengumuman>  $query
     * @return Builder<Pengumuman>
     */
    public function scopeByPriority(Builder $query, PriorityLevel|string $priority): Builder
    {
        $value = $priority instanceof PriorityLevel ? $priority->value : $priority;

        return $query->where('priority', $value);
    }

    /**
     * @param  Builder<Pengumuman>  $query
     * @return Builder<Pengumuman>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByRaw("CASE priority 
            WHEN 'tinggi' THEN 1 
            WHEN 'sedang' THEN 2 
            WHEN 'rendah' THEN 3 
            ELSE 4 END")
            ->latest('created_at');
    }

    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function isScheduled(): bool
    {
        return $this->start_date && $this->start_date->isFuture();
    }
}
