<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DownloadCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Download extends Model implements HasMedia
{
    use HasFactory, HasUuids, InteractsWithMedia;

    protected $table = 'downloads';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'title',
        'description',
        'category',
        'download_count',
        'is_active',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'category' => DownloadCategory::class,
            'download_count' => 'integer',
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<Download>  $query
     * @return Builder<Download>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Download>  $query
     * @return Builder<Download>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order')->orderBy('title');
    }

    /**
     * @param  Builder<Download>  $query
     * @return Builder<Download>
     */
    public function scopeByCategory(Builder $query, DownloadCategory|string $category): Builder
    {
        $value = $category instanceof DownloadCategory ? $category->value : $category;

        return $query->where('category', $value);
    }

    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    public function getFileSizeAttribute(): ?string
    {
        $media = $this->getFirstMedia('files');
        if (! $media) {
            return null;
        }

        $bytes = $media->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;

        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }

        return round($bytes, 2).' '.$units[$index];
    }

    public function getFileExtensionAttribute(): ?string
    {
        $media = $this->getFirstMedia('files');

        return $media ? strtoupper($media->extension) : null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('files')
            ->singleFile();
    }
}
