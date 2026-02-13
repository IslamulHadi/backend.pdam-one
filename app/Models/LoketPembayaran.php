<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LoketType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoketPembayaran extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'loket_pembayaran';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'type',
        'address',
        'phone',
        'latitude',
        'longitude',
        'operational_hours',
        'is_active',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => LoketType::class,
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<LoketPembayaran>  $query
     * @return Builder<LoketPembayaran>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<LoketPembayaran>  $query
     * @return Builder<LoketPembayaran>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    /**
     * @param  Builder<LoketPembayaran>  $query
     * @return Builder<LoketPembayaran>
     */
    public function scopeByType(Builder $query, LoketType|string $type): Builder
    {
        $value = $type instanceof LoketType ? $type->value : $type;

        return $query->where('type', $value);
    }

    /**
     * @param  Builder<LoketPembayaran>  $query
     * @return Builder<LoketPembayaran>
     */
    public function scopeWithCoordinates(Builder $query): Builder
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public function getGoogleMapsUrlAttribute(): ?string
    {
        if (! $this->hasCoordinates()) {
            return null;
        }

        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }
}
