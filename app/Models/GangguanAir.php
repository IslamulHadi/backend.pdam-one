<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GangguanStatus;
use App\Enums\SeverityLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GangguanAir extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'gangguan_air';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'title',
        'description',
        'affected_areas',
        'severity',
        'status',
        'start_datetime',
        'estimated_end_datetime',
        'actual_end_datetime',
        'resolution_notes',
    ];

    protected function casts(): array
    {
        return [
            'affected_areas' => 'array',
            'severity' => SeverityLevel::class,
            'status' => GangguanStatus::class,
            'start_datetime' => 'datetime',
            'estimated_end_datetime' => 'datetime',
            'actual_end_datetime' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        $clearCaches = function (): void {
            Cache::forget('page.home');
        };

        static::saved($clearCaches);
        static::deleted($clearCaches);
    }

    /**
     * @param  Builder<GangguanAir>  $query
     * @return Builder<GangguanAir>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', GangguanStatus::Active->value);
    }

    /**
     * @param  Builder<GangguanAir>  $query
     * @return Builder<GangguanAir>
     */
    public function scopeResolved(Builder $query): Builder
    {
        return $query->where('status', GangguanStatus::Resolved->value);
    }

    /**
     * @param  Builder<GangguanAir>  $query
     * @return Builder<GangguanAir>
     */
    public function scopeBySeverity(Builder $query, SeverityLevel|string $severity): Builder
    {
        $value = $severity instanceof SeverityLevel ? $severity->value : $severity;

        return $query->where('severity', $value);
    }

    /**
     * @param  Builder<GangguanAir>  $query
     * @return Builder<GangguanAir>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByRaw("CASE status 
            WHEN 'active' THEN 1 
            WHEN 'resolved' THEN 2 
            ELSE 3 END")
            ->orderByRaw("CASE severity 
            WHEN 'berat' THEN 1 
            WHEN 'sedang' THEN 2 
            WHEN 'ringan' THEN 3 
            ELSE 4 END")
            ->latest('start_datetime');
    }

    public function isActive(): bool
    {
        return $this->status === GangguanStatus::Active;
    }

    public function isResolved(): bool
    {
        return $this->status === GangguanStatus::Resolved;
    }

    public function markAsResolved(?string $notes = null): void
    {
        $this->update([
            'status' => GangguanStatus::Resolved,
            'actual_end_datetime' => now(),
            'resolution_notes' => $notes,
        ]);
    }

    public function getAffectedAreasTextAttribute(): string
    {
        if (empty($this->affected_areas)) {
            return '-';
        }

        return implode(', ', $this->affected_areas);
    }
}
