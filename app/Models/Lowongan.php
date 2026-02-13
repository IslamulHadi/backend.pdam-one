<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EmploymentType;
use App\Enums\LowonganStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Lowongan extends Model
{
    use HasUuids;

    protected $table = 'lowongan';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'requirements',
        'responsibilities',
        'department',
        'location',
        'employment_type',
        'status',
        'deadline',
        'is_active',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'employment_type' => EmploymentType::class,
            'status' => LowonganStatus::class,
            'deadline' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Lowongan $lowongan) {
            if (empty($lowongan->slug)) {
                $lowongan->slug = Str::slug($lowongan->title).'-'.Str::random(6);
            }
        });

        $clearCaches = function (): void {
            Cache::forget('page.home');
            Cache::forget('page.karir');
        };

        static::saved($clearCaches);
        static::deleted($clearCaches);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', LowonganStatus::Open);
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', LowonganStatus::Closed);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order')->orderBy('created_at', 'desc');
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('deadline')
                ->orWhere('deadline', '>=', now()->toDateString());
        });
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('employment_type', $type);
    }

    public function isOpen(): bool
    {
        return $this->status === LowonganStatus::Open;
    }

    public function isClosed(): bool
    {
        return $this->status === LowonganStatus::Closed;
    }

    public function isExpired(): bool
    {
        return $this->deadline && $this->deadline->isPast();
    }

    public function isAcceptingApplications(): bool
    {
        return $this->is_active && $this->isOpen() && ! $this->isExpired();
    }
}
