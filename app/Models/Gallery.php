<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GalleryType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Gallery extends Model implements HasMedia
{
    use HasFactory, HasUuids, InteractsWithMedia;

    protected $table = 'galleries';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'title',
        'description',
        'type',
        'video_url',
        'display_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => GalleryType::class,
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        $clearCaches = function (): void {
            Cache::forget('page.home');

            for ($i = 1; $i <= 20; $i++) {
                Cache::forget("page.galeri.{$i}");
            }
        };

        static::saved($clearCaches);
        static::deleted($clearCaches);
    }

    /**
     * @param  Builder<Gallery>  $query
     * @return Builder<Gallery>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Gallery>  $query
     * @return Builder<Gallery>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order');
    }

    /**
     * @param  Builder<Gallery>  $query
     * @return Builder<Gallery>
     */
    public function scopeByType(Builder $query, GalleryType|string $type): Builder
    {
        $value = $type instanceof GalleryType ? $type->value : $type;

        return $query->where('type', $value);
    }

    /**
     * @param  Builder<Gallery>  $query
     * @return Builder<Gallery>
     */
    public function scopeFoto(Builder $query): Builder
    {
        return $query->where('type', GalleryType::Foto->value);
    }

    /**
     * @param  Builder<Gallery>  $query
     * @return Builder<Gallery>
     */
    public function scopeVideo(Builder $query): Builder
    {
        return $query->where('type', GalleryType::Video->value);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');

        $this->addMediaCollection('thumbnail')
            ->singleFile();
    }
}
